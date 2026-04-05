<?php

namespace App\Console\Commands;

use App\Models\FeeClearanceToken;
use App\Models\Student;
use App\Services\FeeClearanceService;
use App\Services\NextSmsService;
use App\Models\school;
use App\Models\Parents;
use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AutoExpireTokens extends Command
{
    protected $signature = 'tokens:auto-expire
                            {--academic-year= : Academic year to process (default: current year)}
                            {--school-id= : Process tokens for specific school}
                            {--notify : Send notifications for expired tokens}
                            {--dry-run : Run without actually updating database}
                            {--show-details : Show detailed information per token}';

    protected $description = 'Automatically expire tokens whose expiry date has passed';

    protected $appBaseUrl;

    public function __construct()
    {
        $this->appBaseUrl = config('app.url', 'http://localhost');
        parent::__construct();
    }

    /**
     * Get current academic year
     */
    private function getCurrentAcademicYear()
    {
        if ($this->option('academic-year')) {
            return $this->option('academic-year');
        }
        return date('Y');
    }

    public function handle()
    {
        $academicYear = $this->getCurrentAcademicYear();
        $schoolId = $this->option('school-id');
        $dryRun = $this->option('dry-run');
        $showDetails = $this->option('show-details');
        $sendNotification = $this->option('notify');

        $this->info('🕐 Checking for tokens to auto-expire...');
        $this->info("📅 Academic Year: {$academicYear}");
        $startTime = microtime(true);
        $this->newLine();

        // ✅ Build query with filters
        $query = FeeClearanceToken::with(['student', 'student.class', 'installment'])
            ->where('status', 'active')
            ->where('expires_at', '<', Carbon::now());

        if ($academicYear) {
            $query->where('academic_year', $academicYear);
        }

        if ($schoolId) {
            $query->whereHas('student', function($q) use ($schoolId) {
                $q->where('school_id', $schoolId);
            });
        }

        $expiredTokens = $query->get();
        $count = $expiredTokens->count();

        if ($count === 0) {
            $this->info('✅ No tokens need to be expired.');

            // Show summary even when no tokens
            $this->displaySummary($count, 0, $startTime, $academicYear);
            return 0;
        }

        $this->info("📊 Found {$count} tokens that have expired.");
        $this->newLine();

        if ($dryRun) {
            $this->warn('⚠️  DRY RUN - No changes will be made');
            $this->newLine();

            foreach ($expiredTokens as $token) {
                $this->displayTokenDetails($token, $showDetails);
            }

            $this->displaySummary($count, 0, $startTime, $academicYear, $dryRun);
            return 0;
        }

        // ✅ Process each token individually for better logging
        $expiredCount = 0;
        $notifiedCount = 0;
        $errors = 0;

        $progressBar = $this->output->createProgressBar($count);
        $progressBar->start();

        foreach ($expiredTokens as $token) {
            try {
                // Update token status
                $token->update([
                    'status' => 'expired',
                    'updated_at' => now()
                ]);
                $expiredCount++;

                if ($showDetails) {
                    $this->displayTokenDetails($token, true);
                }

                // Send notification if requested
                if ($sendNotification) {
                    $this->sendExpiryNotification($token);
                    $notifiedCount++;
                }

                // Log token expiry
                // Log::info('Token auto-expired', [
                //     'token_id' => $token->id,
                //     'token' => $token->token,
                //     'student_id' => $token->student_id,
                //     'academic_year' => $token->academic_year,
                //     'expired_at' => $token->expires_at
                // ]);

            } catch (\Exception $e) {
                $errors++;
                Log::error('Failed to expire token', [
                    'token_id' => $token->id,
                    'error' => $e->getMessage()
                ]);

                if ($showDetails) {
                    $this->error("\n   ❌ Error expiring token {$token->token}: {$e->getMessage()}");
                }
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        $this->displaySummary($count, $expiredCount, $startTime, $academicYear, $dryRun);

        if ($sendNotification) {
            $this->info("📱 Notifications sent: {$notifiedCount}");
        }

        if ($errors > 0) {
            $this->error("❌ Errors: {$errors}");
        }

        return 0;
    }

    /**
     * Display detailed token information
     */
    private function displayTokenDetails($token, $showDetails)
    {
        if (!$showDetails) {
            return;
        }

        $student = $token->student;
        $formattedToken = substr($token->token, 0, 3) . '-' . substr($token->token, 3, 3);

        $this->line("\n   📝 Token: {$formattedToken}");
        $this->line("      Student: " . ($student ? $student->first_name . ' ' . $student->last_name : 'N/A'));
        $this->line("      Admission: " . ($student ? $student->admission_number : 'N/A'));
        $this->line("      Academic Year: {$token->academic_year}");
        $this->line("      Installment: " . ($token->installment ? $token->installment->name : 'N/A'));
        $this->line("      Expired: " . Carbon::parse($token->expires_at)->format('d/m/Y H:i'));
        $this->line("      Status: active → expired");
    }

    /**
     * Send notification for expired token
     */
    private function sendExpiryNotification($token)
    {
        $student = $token->student;

        if (!$student) {
            return;
        }

        $parent = Parents::with('user')->find($student->parent_id);

        if (!$parent || !$parent->user || !$parent->user->phone) {
            $this->line("      ⚠️  No parent phone found for notification");
            return;
        }

        $school = school::find($student->school_id);
        $formattedToken = substr($token->token, 0, 3) . '-' . substr($token->token, 3, 3);
        $expiryDate = Carbon::parse($token->expires_at)->format('d/m/Y');

        $message = "TAARIFA: Gate Pass yako imeisha muda wake.\n\n" .
                  "Gate Pass: {$formattedToken}\n" .
                  "Student: {$student->first_name} {$student->last_name}\n" .
                  "Academic Year: {$token->academic_year}\n\n" .
                  "Ili kupata Gate Pass mpya, tafadhali maliza deni lako.\n\n" .
                  "Asante.";

        try {
            $smsService = new NextSmsService();
            $smsService->sendSmsByNext(
                $school->sender_id ?? 'SHULE APP',
                $parent->user->phone,
                $message,
                'token_expired_' . $token->id
            );

            if ($this->option('show-details')) {
                $this->line("      📱 Notification sent to: {$parent->user->phone}");
            }

        } catch (\Exception $e) {
            $this->line("      ⚠️  Failed to send notification: " . $e->getMessage());
            Log::error('Token expiry notification failed', [
                'token_id' => $token->id,
                'phone' => $parent->user->phone,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Display summary statistics
     */
    private function displaySummary($found, $expired, $startTime, $academicYear, $dryRun = false)
    {
        $executionTime = round(microtime(true) - $startTime, 2);

        $this->newLine();
        $this->info('📈 ========== SUMMARY ==========');
        $this->info("📅 Academic Year: {$academicYear}");
        $this->info("🔍 Found expired tokens: {$found}");
        $this->info("✅ Tokens expired: {$expired}");
        $this->info("⏱️  Execution time: {$executionTime} seconds");

        if ($dryRun) {
            $this->warn('⚠️  DRY RUN - No changes were made');
        }
    }
}
