<?php

namespace App\Console\Commands;

use App\Models\Student;
use App\Models\StudentFeeAssignment;
use App\Models\FeeStructure;
use App\Models\FeeInstallment;
use App\Models\FeeClearanceToken;
use App\Models\school;
use App\Models\Parents;
use App\Services\FeeClearanceService;
use App\Services\NextSmsService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SendExistingTokens extends Command
{
    protected $signature = 'tokens:send-existing
                            {--school-id= : Process specific school}
                            {--dry-run : Run without sending actual SMS}
                            {--chunk=50 : Number of records per chunk}';

    protected $description = 'Send tokens to students who already qualify but never received token';

    protected $appBaseUrl;
    public function __construct()
    {
        $this->appBaseUrl = config('app.url', 'http://localhost');
        parent::__construct();
    }

    public function handle()
    {
        $this->info('🚀 Checking for students who qualify but have no token...');

        // Get students with active fee assignments
        $query = Student::whereHas('feeAssignment', function ($q) {
            $q->where('is_active', true);
        });

        if ($this->option('school-id')) {
            $query->where('school_id', $this->option('school-id'));
        }

        $students = $query->get();
        $dryRun = $this->option('dry-run');
        $chunkSize = (int) $this->option('chunk');

        $stats = [
            'processed' => 0,
            'eligible' => 0,
            'already_have_token' => 0,
            'new_tokens' => 0,
            'sms_sent' => 0,
            'errors' => 0
        ];

        $progressBar = $this->output->createProgressBar($students->count());
        $progressBar->start();

        foreach ($students->chunk($chunkSize) as $chunk) {
            foreach ($chunk as $student) {
                $stats['processed']++;
                $result = $this->processStudent($student, $dryRun);

                if ($result === 'eligible') $stats['eligible']++;
                elseif ($result === 'already_have') $stats['already_have_token']++;
                elseif ($result === 'new_token') $stats['new_tokens']++;
                elseif ($result === 'sms_sent') $stats['sms_sent']++;
                elseif ($result === 'error') $stats['errors']++;

                $progressBar->advance();
            }
        }

        $progressBar->finish();
        $this->newLine(2);

        $this->info('📈 ========== SUMMARY ==========');
        $this->info("✅ Processed: {$stats['processed']}");
        $this->info("✅ Eligible students: {$stats['eligible']}");
        $this->info("   ├─ Already had token: {$stats['already_have_token']}");
        $this->info("   └─ New tokens created: {$stats['new_tokens']}");
        $this->info("📱 SMS Sent: {$stats['sms_sent']}");
        $this->info("❌ Errors: {$stats['errors']}");

        if ($dryRun) {
            $this->warn('⚠️  DRY RUN - No changes made');
        }

        return 0;
    }

    private function processStudent($student, $dryRun)
    {
        try {
            // Load class
            $student->load('class');

            // Get active fee assignment with its fee structure
            $assignment = StudentFeeAssignment::with('feeStructure')
                ->where('student_id', $student->id)
                ->where('is_active', true)
                ->first();

            if (!$assignment || !$assignment->feeStructure) {
                return 'not_eligible';
            }

            $feeStructureId = $assignment->fee_structure_id;
            $feeStructure = $assignment->feeStructure;

            // Get total paid from payments
            $totalPaid = $student->payments()->sum('amount');

            // Get current installment (time-based)
            $currentInstallment = FeeInstallment::where('fee_structure_id', $feeStructureId)
                ->where('start_date', '<=', now())
                ->where('end_date', '>=', now())
                ->orderBy('order')
                ->first();

            if (!$currentInstallment) {
                return 'not_eligible';
            }

            // Check if student has reached cumulative required
            if ($totalPaid < $currentInstallment->cumulative_required) {
                return 'not_eligible';
            }

            // Check if token already exists for this installment
            $existingToken = FeeClearanceToken::where([
                'student_id' => $student->id,
                'installment_id' => $currentInstallment->id,
            ])->first();

            if ($existingToken) {
                if (!$dryRun) {
                    $this->line("\n   ⏭️  {$student->admission_number} - Already has token for {$currentInstallment->name}");
                }
                return 'already_have';
            }

            // Show details
            if ($dryRun) {
                $this->line("\n   [DRY RUN] {$student->admission_number} - {$student->first_name} {$student->last_name}");
                $this->line("      Class: " . ($student->class->class_name ?? 'N/A'));
                $this->line("      Installment: {$currentInstallment->name}");
                $this->line("      Total Paid: " . number_format($totalPaid, 0) . " / Required: " . number_format($currentInstallment->cumulative_required, 0));
                return 'eligible';
            }

            // Show details for actual run
            $this->line("\n   📝 {$student->admission_number} - {$student->first_name} {$student->last_name}");
            $this->line("      Class: " . ($student->class->class_name ?? 'N/A'));
            $this->line("      Fee Structure: {$feeStructure->name}");
            $this->line("      Installment: {$currentInstallment->name}");
            $this->line("      Total Paid: " . number_format($totalPaid, 0) . " / Required: " . number_format($currentInstallment->cumulative_required, 0));

            // Generate token using FeeClearanceService
            $service = new FeeClearanceService();
            $tokenResult = $service->process($student);

            // Check if token was generated
            if (!$tokenResult) {
                $this->error("      ❌ Failed to generate token");
                return 'error';
            }

            // Get the token (it might be existing or new)
            $token = $tokenResult;

            // Check if this is a new token or existing
            $isNewToken = $token->wasRecentlyCreated ?? false;

            if ($isNewToken) {
                $this->line("      ✅ New Token: {$token->token}");
                $statsType = 'new_token';
            } else {
                $this->line("      ⏭️  Token already existed: {$token->token}");
                return 'already_have';
            }

            $this->line("      Expires: " . Carbon::parse($token->expires_at)->format('d/m/Y'));

            // Send SMS
            $parent = Parents::with('user')->find($student->parent_id);
            $school = school::find($student->school_id);

            if ($parent && $parent->user && $parent->user->phone) {
                // Format token for SMS (add dash for readability)
                $formattedToken = substr($token->token, 0, 3) . '-' . substr($token->token, 3, 3);

                $link = $this->appBaseUrl . '/tokens/verify';
                $message = "Habari, Gate Pass No yako ni:.\n\n" .
                    "{$formattedToken}\n\n" .
                    "Kwa ajili ya: {$student->first_name} {$student->last_name}\n" .
                    "Muda wa kuisha: ". Carbon::parse($currentInstallment->end_date)->format('d/m/Y') ."\n\n" .
                    "Hakiki kupitia: {$link}\n\n" .
                    "Onesha Getini au Kwenye School Bus.\n\n" .
                    "Asante.";

                // TODO: Uncomment this when SMS service is ready
                $smsService = new NextSmsService();
                // $smsService->sendSmsByNext(
                //     $school->sender_id ?? 'SHULE APP',
                //     $parent->user->phone,
                //     $message,
                //     'fee_clearance_' . time()
                // );

                $this->line("      📱 SMS would be sent to: {$parent->user->phone}");
                $this->line("      📨 Message: {$formattedToken}");
                // Log::info('Token generated and SMS sent', [
                //     'student_id' => $student->id,
                //     'token' => $token->token,
                //     'expires_at' => $token->expires_at,
                //     'parent_phone' => $parent->user->phone
                // ]);
                return 'sms_sent';
            } else {
                $this->line("      ⚠️  No phone number found for parent");
                return 'new_token';
            }
        } catch (\Exception $e) {
            if (!$dryRun) {
                $this->error("\n   ❌ Error for {$student->admission_number}: {$e->getMessage()}");
            }
            Log::error('Token generation failed', [
                'student_id' => $student->id,
                'error' => $e->getMessage()
            ]);
            return 'error';
        }
    }
}
