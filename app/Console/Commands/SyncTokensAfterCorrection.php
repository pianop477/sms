<?php

namespace App\Console\Commands;

use App\Models\Student;
use App\Models\StudentFeeAssignment;
use App\Models\FeeClearanceToken;
use App\Services\FeeClearanceService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SyncTokensAfterCorrection extends Command
{
    protected $signature = 'tokens:sync-after-correction
                            {--academic-year= : Academic year to sync (default: current year)}
                            {--student-id= : Sync specific student only}
                            {--school-id= : Sync students from specific school}
                            {--dry-run : Run without making changes}
                            {--show-details : Show detailed information per student}
                            {--force : Force sync even if no changes detected}';

    protected $description = 'Sync all tokens after payment corrections (one token per student per academic year)';

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
        $studentId = $this->option('student-id');
        $schoolId = $this->option('school-id');
        $dryRun = $this->option('dry-run');
        $showDetails = $this->option('show-details');
        $force = $this->option('force');

        $this->info('🔄 Syncing tokens after payment corrections...');
        $this->info("📅 Academic Year: {$academicYear}");
        $this->info("📌 Policy: One token per student per academic year (update existing, never duplicate)");
        $this->newLine();

        $studentIdsFromAssignments = StudentFeeAssignment::where('academic_year', $academicYear)
            ->where('is_active', true)
            ->pluck('student_id');

        $query = Student::whereIn('id', $studentIdsFromAssignments);

        if ($studentId) {
            $query->where('id', $studentId);
        }
        if ($schoolId) {
            $query->where('school_id', $schoolId);
        }

        $students = $query->get();

        if ($students->isEmpty()) {
            $this->warn("⚠️  No students found with fee assignments for academic year {$academicYear}");
            return 0;
        }

        $this->info("📊 Students to process: {$students->count()}");
        $this->newLine();

        if ($dryRun) {
            $this->warn('⚠️  DRY RUN MODE: No changes will be made');
            $this->newLine();
        }

        $service = new FeeClearanceService();
        $stats = [
            'processed' => 0,
            'updated' => 0,      // existing token updated (changed installment/expiry)
            'expired' => 0,      // token expired because student no longer eligible
            'created' => 0,      // only if no token existed at all for this student/year
            'no_change' => 0,
            'no_assignment' => 0,
            'errors' => 0
        ];

        $progressBar = $this->output->createProgressBar($students->count());
        $progressBar->start();

        foreach ($students as $student) {
            $stats['processed']++;
            $result = $this->syncStudentToken($student, $service, $academicYear, $dryRun, $showDetails, $force, $stats);
            if ($result === 'updated') $stats['updated']++;
            elseif ($result === 'expired') $stats['expired']++;
            elseif ($result === 'created') $stats['created']++;
            elseif ($result === 'no_change') $stats['no_change']++;
            elseif ($result === 'no_assignment') $stats['no_assignment']++;
            elseif ($result === 'error') $stats['errors']++;
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);
        $this->displaySummary($stats, $academicYear, $dryRun);

        return 0;
    }

    private function syncStudentToken($student, $service, $academicYear, $dryRun, $showDetails, $force, &$stats)
    {
        try {
            // Get fee assignment
            $assignment = StudentFeeAssignment::where('student_id', $student->id)
                ->where('academic_year', $academicYear)
                ->where('is_active', true)
                ->first();

            if (!$assignment) {
                if ($showDetails) {
                    $this->line("\n   ⏭️  {$student->admission_number} - No fee assignment for year {$academicYear}");
                }
                return 'no_assignment';
            }

            if ($showDetails && !$dryRun) {
                $this->line("\n   📝 {$student->admission_number} - {$student->first_name} {$student->last_name}");
                $this->line("      Academic Year: {$academicYear}");
                $this->line("      Fee Structure: " . ($assignment->feeStructure->name ?? 'N/A'));
            }

            if ($dryRun) {
                // Evaluate but don't change anything
                $evaluation = $service->evaluate($student, $academicYear);
                if ($showDetails) {
                    $this->line("      Eligible: " . ($evaluation['eligible'] ? 'Yes' : 'No'));
                    if (isset($evaluation['installment']) && $evaluation['installment']) {
                        $this->line("      Target Installment: " . $evaluation['installment']->name);
                    }
                }
                return 'no_change';
            }

            // Use the service method that handles eligibility, updates token, and resets notification_sent
            $token = $service->syncTokenAfterPaymentCorrection($student, $academicYear);

            if ($token === null) {
                // Student not eligible or token expired
                // Check if there was an active token that got expired
                $anyActive = FeeClearanceToken::where('student_id', $student->id)
                    ->where('academic_year', $academicYear)
                    ->where('status', 'active')
                    ->exists();

                if (!$anyActive) {
                    if ($showDetails) {
                        $this->line("      🔄 Token expired or not eligible");
                    }
                    return 'expired';
                }
                return 'no_change';
            }

            // Determine what happened: was token created or updated?
            // We can check if token was recently created or if its updated_at is recent (or compare with old data)
            // Simpler: since service->syncTokenAfterPaymentCorrection returns token, we can check if it existed before.
            $existingBefore = FeeClearanceToken::where('student_id', $student->id)
                ->where('academic_year', $academicYear)
                ->where('id', '!=', $token->id) // Not necessary
                ->first();

            // Actually, the service returns the token after update/create. We can use wasRecentlyCreated.
            if ($token->wasRecentlyCreated) {
                if ($showDetails) {
                    $formattedToken = substr($token->token, 0, 3) . '-' . substr($token->token, 3, 3);
                    $this->line("      ✅ New token created: {$formattedToken}");
                    $this->line("         Installment: " . ($token->installment->name ?? 'N/A'));
                    $this->line("         Expires: " . Carbon::parse($token->expires_at)->format('d/m/Y'));
                }
                return 'created';
            } else {
                // Token was updated (existing)
                if ($showDetails) {
                    $this->line("      🔄 Token updated (existing token)");
                    $this->line("         Installment: " . ($token->installment->name ?? 'N/A'));
                    $this->line("         Expires: " . Carbon::parse($token->expires_at)->format('d/m/Y'));
                    $this->line("         Token Code: " . substr($token->token, 0, 3) . '-' . substr($token->token, 3, 3));
                }
                return 'updated';
            }

        } catch (\Exception $e) {
            if ($showDetails) {
                $this->error("\n   ❌ Error for {$student->admission_number}: {$e->getMessage()}");
            }
            Log::error('Token sync failed after payment correction', [
                'student_id' => $student->id,
                'academic_year' => $academicYear,
                'error' => $e->getMessage()
            ]);
            return 'error';
        }
    }

    private function displaySummary($stats, $academicYear, $dryRun)
    {
        $this->info('📈 ========== SUMMARY ==========');
        $this->info("📅 Academic Year: {$academicYear}");
        $this->info("✅ Processed: {$stats['processed']}");
        $this->info("✅ Tokens updated (reused): {$stats['updated']}");
        $this->info("✅ Tokens expired: {$stats['expired']}");
        $this->info("✅ New tokens created: {$stats['created']}");
        $this->info("⏭️  No change needed: {$stats['no_change']}");
        $this->info("⚠️  No assignment found: {$stats['no_assignment']}");
        $this->info("❌ Errors: {$stats['errors']}");
        $this->newLine();
        $this->line("📌 Token policy: One token per student per academic year. Existing tokens are updated, never duplicated.");

        if ($dryRun) {
            $this->warn('⚠️  DRY RUN - No changes were made');
        }
    }
}
