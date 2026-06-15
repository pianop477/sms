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

class SyncTokensAfterCorrection extends Command
{
    use formatPhoneTrait;

    protected $signature = 'tokens:sync-after-correction
                            {--academic-year= : Academic year to sync}
                            {--student-id= : Sync specific student}
                            {--school-id= : Sync students from specific school}
                            {--dry-run : Run without making changes}
                            {--show-details : Show detailed information}
                            {--force : Force sync even if no changes}';

    protected $description = 'Sync tokens after payment corrections (never duplicate token)';

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
        $showDetails = $this->option('show-details');
        $force = $this->option('force');

        $this->info('🔄 Syncing tokens after payment corrections...');
        $this->info("📅 Academic Year: {$academicYear}");
        $this->newLine();

        $studentIds = StudentFeeAssignment::where('academic_year', $academicYear)
            ->where('is_active', true)
            ->pluck('student_id');

        $query = Student::whereIn('id', $studentIds);
        if ($this->option('student-id')) $query->where('id', $this->option('student-id'));
        if ($this->option('school-id')) $query->where('school_id', $this->option('school-id'));

        $students = $query->get();
        if ($students->isEmpty()) {
            $this->warn("No students found with fee assignments for {$academicYear}");
            return 0;
        }

        $this->info("Students to process: {$students->count()}");
        if ($dryRun) $this->warn('DRY RUN mode');

        $service = new FeeClearanceService();
        $stats = ['processed' => 0, 'updated' => 0, 'created' => 0, 'no_change' => 0, 'errors' => 0, 'sms_sent' => 0];

        $progressBar = $this->output->createProgressBar($students->count());
        $progressBar->start();

        foreach ($students as $student) {
            $stats['processed']++;
            $result = $this->syncStudent($student, $service, $academicYear, $dryRun, $showDetails, $force);
            if ($result === 'updated') $stats['updated']++;
            elseif ($result === 'created') $stats['created']++;
            elseif ($result === 'sms_sent') $stats['sms_sent']++;
            elseif ($result === 'no_change') $stats['no_change']++;
            elseif ($result === 'error') $stats['errors']++;
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);
        $this->displaySummary($stats, $academicYear, $dryRun);
        return 0;
    }

    private function syncStudent($student, $service, $academicYear, $dryRun, $showDetails, $force)
    {
        try {
            if ($showDetails && !$dryRun) {
                $this->line("\n📝 {$student->admission_number} - {$student->first_name} {$student->last_name}");
            }

            $result = $service->process($student, $academicYear);
            $token = $result['token'];
            $action = $result['action'];

            if (!$token) {
                if ($showDetails) $this->line("   ⏭️  Not eligible for current installment");
                return 'no_change';
            }

            // Only send SMS if token was created or updated (installment changed)
            $shouldSendSms = in_array($action, ['created', 'updated']) || $force;

            if ($dryRun) {
                $this->line("   [DRY RUN] Token: {$token->token}, Action: {$action}, SendSMS: " . ($shouldSendSms ? 'Yes' : 'No'));
                return $action === 'created' ? 'created' : ($action === 'updated' ? 'updated' : 'no_change');
            }

            if (!$shouldSendSms) {
                if ($showDetails) $this->line("   ✅ Token already correct (no SMS needed)");
                return $action === 'created' ? 'created' : ($action === 'updated' ? 'updated' : 'no_change');
            }

            // Send SMS (only if token created or updated)
            $parent = Parents::with('user')->find($student->parent_id);
            $school = school::find($student->school_id);

            if ($parent && $parent->user && $parent->user->phone) {
                $phone = $this->formatPhoneNumberForSms($parent->user->phone);
                $message = "GATEPASS NA. ni: {$token->token}\n" .
                    "Jina la Mtoto: {$student->first_name} {$student->last_name}\n" .
                    "Awamu: " . ($token->installment->name ?? 'Current') . "\n" .
                    "Mwisho wa Awamu: " . Carbon::parse($token->expires_at)->format('d/m/Y') . "\n" .
                    "Hakiki uwapo shuleni/kwenye Basi";

                $smsService = new NextSmsService();
                $payload = [
                    'from' => $school->sender_id ?? 'SHULE APP',
                    'to' => $phone,
                    'text' => $message,
                    'reference' => 'fee_correction_' . time(),
                ];
                $smsService->sendSmsByNext($payload['from'], $payload['to'], $payload['text'], $payload['reference']);
                $this->line("   📱 SMS sent to {$phone} (Action: {$action})");
                return 'sms_sent';
            } else {
                $this->warn("   ⚠️  No parent phone for {$student->admission_number}");
                return $action === 'created' ? 'created' : 'updated';
            }
        } catch (\Exception $e) {
            Log::error('Sync error', ['student' => $student->id, 'error' => $e->getMessage()]);
            if ($showDetails) $this->error("   ❌ {$e->getMessage()}");
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
        if ($dryRun) $this->warn('DRY RUN - No changes');
    }
}
