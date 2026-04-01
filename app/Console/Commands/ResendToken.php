<?php

namespace App\Console\Commands;

use App\Models\Student;
use App\Models\FeeClearanceToken;
use App\Models\school;
use App\Models\Parents;
use App\Services\FeeClearanceService;
use App\Services\NextSmsService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ResendToken extends Command
{
    protected $signature = 'token:resend
                            {--student-id= : Student ID to resend token}
                            {--admission= : Student admission number}
                            {--phone= : Parent phone number}
                            {--dry-run : Run without sending actual SMS}';

    protected $description = 'Resend gate pass token to parent';

    protected $appBaseUrl;

    public function __construct()
    {
        $this->appBaseUrl = config('app.url', 'http://localhost');
        parent::__construct();
    }

    public function handle()
    {
        $studentId = $this->option('student-id');
        $admission = $this->option('admission');
        $phone = $this->option('phone');
        $dryRun = $this->option('dry-run');

        // Find student
        $student = null;

        if ($studentId) {
            $student = Student::with(['parents.user', 'class'])->find($studentId);
        } elseif ($admission) {
            $student = Student::with(['parents.user', 'class'])
                ->where('admission_number', $admission)
                ->first();
        } elseif ($phone) {
            // Find by parent phone - correct relationship: parents.user_id -> users.id
            $parent = Parents::with('user')
                ->whereHas('user', function($q) use ($phone) {
                    $q->where('phone', 'like', "%{$phone}%");
                })
                ->first();

            if ($parent) {
                $student = Student::with(['parents.user', 'class'])
                    ->where('parent_id', $parent->id)
                    ->first();
            }
        }

        if (!$student) {
            $this->error('❌ Student not found!');
            $this->info('Use: --student-id=1 or --admission=ABC123 or --phone=0712345678');
            return 1;
        }

        $this->info("📝 Student: {$student->first_name} {$student->last_name} ({$student->admission_number})");
        $this->info("   Class: " . ($student->class->class_name ?? 'N/A'));

        // Get active token with installment relationship
        $activeToken = FeeClearanceToken::with('installment')
            ->where('student_id', $student->id)
            ->where('status', 'active')
            ->where('expires_at', '>', now())
            ->first();

        if (!$activeToken) {
            $this->warn('⚠️  No active token found for this student!');

            // Check if student is eligible but token not generated
            $service = new FeeClearanceService();
            $evaluation = $service->evaluate($student);

            if ($evaluation['eligible']) {
                $this->info('   Student is eligible but token not generated yet.');
                $this->info('   Run: php artisan token:generate --student-id=' . $student->id);
            } else {
                $this->info('   Reason: ' . ($evaluation['reason'] ?? 'Not eligible'));
                if (isset($evaluation['total_paid']) && isset($evaluation['required'])) {
                    $this->info("   Total Paid: " . number_format($evaluation['total_paid'], 0) . " / Required: " . number_format($evaluation['required'], 0));
                }
            }
            return 1;
        }

        // Format token with dash for display
        $formattedToken = substr($activeToken->token, 0, 3) . '-' . substr($activeToken->token, 3, 3);

        $this->info("✅ Active token found: {$formattedToken}");
        $this->info("   Expires: " . Carbon::parse($activeToken->expires_at)->format('d/m/Y H:i'));

        if ($dryRun) {
            $this->warn('⚠️  DRY RUN - SMS will not be sent');
            $dryPhone = $student->parents && $student->parents->user ? $student->parents->user->phone : 'No phone';
            $this->info("   Would send token to: {$dryPhone}");
            $this->info("   Message preview:");
            $this->line("   ┌─────────────────────────────────────────");
            $this->line("   │ Token: {$formattedToken}");
            $this->line("   │ Student: {$student->first_name} {$student->last_name}");
            $this->line("   │ Expires: " . Carbon::parse($activeToken->expires_at)->format('d/m/Y'));
            $this->line("   └─────────────────────────────────────────");
            return 0;
        }

        // Get parent details - correct relationship: parent.user.phone
        $parent = $student->parents()->with('user')->first();

        if (!$parent || !$parent->user) {
            $this->error('❌ Parent record not found!');
            return 1;
        }

        if (!$parent->user->phone) {
            $this->error('❌ Parent phone number not found!');
            $this->info("   Parent ID: {$parent->id}");
            $this->info("   User ID: {$parent->user->id}");
            $this->info("   User phone: " . ($parent->user->phone ?? 'NULL'));
            return 1;
        }

        $school = school::find($student->school_id);

        if (!$school) {
            $this->warn('⚠️  School not found, using default sender ID');
        }

        // Get installment name safely
        $installmentName = 'Current Term';
        if ($activeToken->installment) {
            $installmentName = $activeToken->installment->name;
        } else {
            // Try to load installment if not loaded
            $activeToken->load('installment');
            if ($activeToken->installment) {
                $installmentName = $activeToken->installment->name;
            }
        }

        $expiryDate = Carbon::parse($activeToken->expires_at)->format('d/m/Y');

        // Prepare message
        $link = $this->appBaseUrl . '/tokens/verify';
        $message = "Habari, Gate Pass No yako ni:.\n\n" .
                "{$formattedToken}\n\n" .
                "Kwa ajili ya: {$student->first_name} {$student->last_name}\n" .
                "Muda wa kuisha: {$expiryDate}\n\n" .
                "Hakiki kupitia: {$link}\n\n" .
                "Onesha Getini au Kwenye School Bus.\n\n" .
                "Asante.";

        // Send SMS
        try {
            $smsService = new NextSmsService();
            $result = $smsService->sendSmsByNext(
                $school->sender_id ?? 'SHULE APP',
                $parent->user->phone,
                $message,
                'resend_token_' . time()
            );

            $this->info("📱 SMS sent successfully!");
            $this->info("   To: {$parent->user->phone}");
            $this->info("   Token: {$formattedToken}");
            $this->info("   Expires: {$expiryDate}");

            // Log::info('Token resent successfully via command', [
            //     'student_id' => $student->id,
            //     'student_name' => $student->first_name . ' ' . $student->last_name,
            //     'token' => $activeToken->token,
            //     'formatted_token' => $formattedToken,
            //     'phone' => $parent->user->phone,
            //     'installment' => $installmentName
            // ]);

        } catch (\Exception $e) {
            $this->error('❌ Failed to send SMS: ' . $e->getMessage());
            Log::error('Token resend failed via command', [
                'student_id' => $student->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 1;
        }

        $this->newLine();
        $this->info('✅ Token resent successfully!');

        return 0;
    }
}
