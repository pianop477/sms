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
                            {--force : Force send SMS even if token already sent}';

    protected $description = 'Send tokens to students who qualify for CURRENT installment (one SMS per installment change)';

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
        $dryRun = $this->option('dry-run');
        $force = $this->option('force');
        $chunkSize = (int) $this->option('chunk');

        $this->info('🚀 Sending tokens to qualified students...');
        $this->info("📅 Academic Year: {$academicYear}");
        $this->newLine();

        $studentIds = StudentFeeAssignment::where('academic_year', $academicYear)
            ->where('is_active', true)
            ->pluck('student_id');

        $query = Student::whereIn('id', $studentIds);
        if ($this->option('school-id')) $query->where('school_id', $this->option('school-id'));

        $students = $query->get();
        if ($students->isEmpty()) {
            $this->warn("No students with fee assignment for {$academicYear}");
            return 0;
        }

        $stats = ['processed' => 0, 'created' => 0, 'updated' => 0, 'sms_sent' => 0, 'no_change' => 0, 'errors' => 0];
        $progressBar = $this->output->createProgressBar($students->count());
        $progressBar->start();

        foreach ($students->chunk($chunkSize) as $chunk) {
            foreach ($chunk as $student) {
                $stats['processed']++;
                $result = $this->processStudent($student, $dryRun, $force, $academicYear);
                if ($result === 'created') $stats['created']++;
                elseif ($result === 'updated') $stats['updated']++;
                elseif ($result === 'sms_sent') $stats['sms_sent']++;
                elseif ($result === 'no_change') $stats['no_change']++;
                elseif ($result === 'error') $stats['errors']++;
                $progressBar->advance();
            }
        }

        $progressBar->finish();
        $this->newLine(2);
        $this->displaySummary($stats, $academicYear, $dryRun);
        return 0;
    }

    private function processStudent($student, $dryRun, $force, $academicYear)
    {
        try {
            $service = new FeeClearanceService();
            $result = $service->process($student, $academicYear);
            $token = $result['token'];
            $action = $result['action']; // 'created', 'updated', or 'none'

            if (!$token) {
                if (!$dryRun) $this->line("\n   ⏭️  {$student->admission_number} - Not eligible");
                return 'no_change';
            }

            $shouldSend = ($action !== 'none') || $force;

            if ($dryRun) {
                $this->line("\n   [DRY RUN] {$student->admission_number} - Token: {$token->token}, Action: {$action}, SendSMS: " . ($shouldSend ? 'Yes' : 'No'));
                return $action;
            }

            if (!$shouldSend) {
                $this->line("\n   ⏭️  {$student->admission_number} - No change (token: {$token->token})");
                return $action;
            }

            // Send SMS
            $parent = Parents::with('user')->find($student->parent_id);
            $school = school::find($student->school_id);
            if (!$parent || !$parent->user || !$parent->user->phone) {
                $this->warn("\n   ⚠️  {$student->admission_number} - No parent phone");
                return $action;
            }

            $phone = $this->formatPhoneNumberForSms($parent->user->phone);
            $message = "GATE PASS No: {$token->token}\nJina: {$student->first_name} {$student->last_name}\nAwamu: " . ($token->installment->name ?? 'Current') . "\nExpiry: " . Carbon::parse($token->expires_at)->format('d/m/Y') . "\nHakiki: {$this->appBaseUrl}/tokens/verify\n";

            $smsService = new NextSmsService();
            $payload = [
                'from' => $school->sender_id ?? 'SHULE APP',
                'to' => $phone,
                'text' => $message,
                'reference' => 'token_send_' . time(),
            ];
            $smsService->sendSmsByNext($payload['from'], $payload['to'], $payload['text'], $payload['reference']);
            $this->line("\n   📱 SMS sent to {$phone} for token {$token->token} (Action: {$action})");
            return 'sms_sent';
        } catch (\Exception $e) {
            Log::error('SendExistingTokens error', ['student' => $student->id, 'error' => $e->getMessage()]);
            if (!$dryRun) $this->error("\n   ❌ {$e->getMessage()}");
            return 'error';
        }
    }

    private function displaySummary($stats, $year, $dryRun)
    {
        $this->info('📈 ========== SUMMARY ==========');
        $this->info("📅 Academic Year: {$year}");
        $this->info("✅ Processed: {$stats['processed']}");
        $this->info("✅ Tokens created: {$stats['created']}");
        $this->info("✅ Tokens updated: {$stats['updated']}");
        $this->info("✅ SMS sent: {$stats['sms_sent']}");
        $this->info("⏭️  No change: {$stats['no_change']}");
        $this->info("❌ Errors: {$stats['errors']}");
        if ($dryRun) $this->warn('DRY RUN');
    }
}
