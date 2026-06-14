<?php

namespace App\Console\Commands;

use App\Models\Student;
use App\Models\StudentFeeAssignment;
use App\Models\FeeClearanceToken;
use App\Models\school;
use App\Models\Parents;
use App\Services\FeeClearanceService;
use App\Services\NextSmsService;
use App\Traits\formatPhoneTrait;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SendExistingTokens extends Command
{
    use formatPhoneTrait;

    protected $signature = 'tokens:send-existing
                            {--school-id= : Process specific school}
                            {--academic-year= : Academic year to process (default: current year)}
                            {--dry-run : Run without sending actual SMS}
                            {--chunk=50 : Number of records per chunk}
                            {--force : Force send SMS even if token already notified}';

    protected $description = 'Send tokens to students who qualify for CURRENT installment based on date and payments';

    protected $appBaseUrl;

    public function __construct()
    {
        $this->appBaseUrl = config('app.url', 'http://localhost');
        parent::__construct();
    }

    private function getCurrentAcademicYear()
    {
        return $this->option('academic-year') ?? date('Y');
    }

    public function handle()
    {
        $academicYear = $this->getCurrentAcademicYear();
        $today = Carbon::today()->toDateString();

        $this->info('🚀 Sending tokens to students who qualify for current installment...');
        $this->info("📅 Academic Year: {$academicYear} | Today: {$today}");
        $this->newLine();

        // Students with active fee assignment for this year
        $studentIds = StudentFeeAssignment::where('academic_year', $academicYear)
            ->where('is_active', true)
            ->pluck('student_id');

        $query = Student::whereIn('id', $studentIds);
        if ($this->option('school-id')) {
            $query->where('school_id', $this->option('school-id'));
        }

        $students = $query->get();
        $dryRun = $this->option('dry-run');
        $force = $this->option('force');
        $chunkSize = (int) $this->option('chunk');

        if ($students->isEmpty()) {
            $this->warn("⚠️  No students found with fee assignment for academic year {$academicYear}");
            return 0;
        }

        $stats = [
            'processed' => 0,
            'eligible' => 0,           // Students with a token (new or existing)
            'already_notified' => 0,    // Token existed and already notified
            'new_sms_sent' => 0,        // SMS sent now
            'no_token' => 0,            // Service returned null (not eligible)
            'errors' => 0,
        ];

        $progressBar = $this->output->createProgressBar($students->count());
        $progressBar->start();

        foreach ($students->chunk($chunkSize) as $chunk) {
            foreach ($chunk as $student) {
                $stats['processed']++;
                $result = $this->processStudent($student, $dryRun, $force, $academicYear);

                if ($result === 'eligible') $stats['eligible']++;
                elseif ($result === 'already_notified') $stats['already_notified']++;
                elseif ($result === 'new_sms_sent') $stats['new_sms_sent']++;
                elseif ($result === 'no_token') $stats['no_token']++;
                elseif ($result === 'error') $stats['errors']++;

                $progressBar->advance();
            }
        }

        $progressBar->finish();
        $this->newLine(2);

        $this->info('📈 ========== SUMMARY ==========');
        $this->info("📅 Academic Year: {$academicYear}");
        $this->info("✅ Processed: {$stats['processed']}");
        $this->info("✅ Eligible (has token): {$stats['eligible']}");
        $this->info("   ├─ Already notified before: {$stats['already_notified']}");
        $this->info("   └─ New SMS sent now: {$stats['new_sms_sent']}");
        $this->info("⚠️  No token (not eligible): {$stats['no_token']}");
        $this->info("❌ Errors: {$stats['errors']}");

        if ($dryRun) {
            $this->warn('⚠️  DRY RUN - No changes were made');
        }

        return 0;
    }

    private function processStudent($student, $dryRun, $force, $academicYear)
    {
        try {
            $student->load('class');
            $service = new FeeClearanceService();

            // Get token (creates or updates if eligible for current installment)
            $token = $service->process($student, $academicYear);

            if (!$token) {
                if (!$dryRun) {
                    $this->line("\n   ⏭️  {$student->admission_number} - Not eligible for current installment");
                }
                return 'no_token';
            }

            // Determine if we need to send SMS
            $needsNotification = $service->needsNotification($token);
            $shouldSend = $needsNotification || $force;

            if ($dryRun) {
                $this->line("\n   [DRY RUN] {$student->admission_number} - {$student->first_name} {$student->last_name}");
                $this->line("      Token: {$token->token}");
                $this->line("      Installment: " . ($token->installment->name ?? 'N/A'));
                $this->line("      Expires: " . Carbon::parse($token->expires_at)->format('d/m/Y'));
                $this->line("      Needs notification: " . ($needsNotification ? 'Yes' : 'No'));
                if ($force) $this->line("      Force mode: will send SMS regardless");
                return 'eligible';
            }

            if (!$shouldSend) {
                $this->line("\n   ⏭️  {$student->admission_number} - Already notified (token: {$token->token})");
                return 'already_notified';
            }

            // Send SMS
            $parent = Parents::with('user')->find($student->parent_id);
            $school = school::find($student->school_id);

            if (!$parent || !$parent->user || !$parent->user->phone) {
                $this->warn("\n   ⚠️  {$student->admission_number} - No parent phone, cannot send SMS");
                Log::warning('No parent phone for token SMS', ['student' => $student->id]);
                // Even if no phone, we consider it "notified" to avoid infinite retries? Better not mark.
                return 'error';
            }

            $formattedToken = $token->token;
            $link = $this->appBaseUrl . '/tokens/verify';
            $expiryDate = Carbon::parse($token->expires_at)->format('d/m/Y');
            $installmentName = $token->installment->name ?? 'Current Term';

            $message = "GATE PASS No: {$formattedToken}\n" .
                "Jina: {$student->first_name} {$student->last_name}\n" .
                "Awamu: {$installmentName}\n" .
                "Expiry: {$expiryDate}\n" .
                "Hakiki hapa: {$link}\n";

            $phone = $this->formatPhoneNumberForSms($parent->user->phone);
            $smsService = new NextSmsService();
            $payload = [
                'from' => $school->sender_id ?? 'SHULE APP',
                'to' => $phone,
                'text' => $message,
                'reference' => 'fee_clearance_' . time(),
            ];

            try {
                $smsService->sendSmsByNext($payload['from'], $payload['to'], $payload['text'], $payload['reference']);
                $this->line("\n   📱 SMS sent to: {$phone} for token {$formattedToken}");
                // Mark token as notified
                $service->markNotificationSent($token);
                return 'new_sms_sent';
            } catch (\Exception $e) {
                $this->error("\n   ❌ SMS failed for {$student->admission_number}: " . $e->getMessage());
                Log::error('SMS sending failed in SendExistingTokens', [
                    'student' => $student->id,
                    'token' => $token->token,
                    'error' => $e->getMessage()
                ]);
                // Do NOT mark as notified because SMS failed
                return 'error';
            }
        } catch (\Exception $e) {
            if (!$dryRun) {
                $this->error("\n   ❌ Error processing {$student->admission_number}: " . $e->getMessage());
            }
            Log::error('Token processing error in SendExistingTokens', [
                'student' => $student->id,
                'error' => $e->getMessage()
            ]);
            return 'error';
        }
    }
}
