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

    protected $description = 'Sync all tokens after payment corrections for a specific academic year';

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
            'updated' => 0,
            'expired' => 0,
            'created' => 0,
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
                $evaluation = $service->evaluate($student, $academicYear);
                if ($showDetails) {
                    $this->line("      Eligible: " . ($evaluation['eligible'] ? 'Yes' : 'No'));
                    if (isset($evaluation['total_paid']) && isset($evaluation['required'])) {
                        $this->line("      Total Paid: " . number_format($evaluation['total_paid'], 0));
                        $this->line("      Required: " . number_format($evaluation['required'], 0));
                    }
                }
                return 'no_change';
            }

            // Get current eligibility
            $evaluation = $service->evaluate($student, $academicYear);

            if (!$evaluation['eligible']) {
                // Expire any active token
                $activeToken = FeeClearanceToken::where('student_id', $student->id)
                    ->where('academic_year', $academicYear)
                    ->where('status', 'active')
                    ->first();

                if ($activeToken) {
                    $activeToken->update(['status' => 'expired']);
                    if ($showDetails) {
                        $this->line("      🔄 Token expired - Student no longer qualifies");
                        $this->line("         Reason: {$evaluation['reason']}");
                    }
                    return 'expired';
                }

                if ($showDetails) {
                    $this->line("      ⏭️  Not eligible - No token exists");
                }
                return 'no_change';
            }

            $targetInstallment = $evaluation['installment'];

            // ========== FIX: Check for ANY token with same student+installment ==========
            $existingToken = FeeClearanceToken::where('student_id', $student->id)
                ->where('academic_year', $academicYear)
                ->where('installment_id', $targetInstallment->id)
                ->first();

            if ($existingToken) {
                // Token already exists (even if expired). Update it.
                $oldStatus = $existingToken->status;
                $oldExpiry = $existingToken->expires_at;

                $existingToken->update([
                    'status' => 'active',
                    'expires_at' => $targetInstallment->end_date,
                    'fee_structure_id' => $targetInstallment->fee_structure_id,
                    'updated_at' => now(),
                ]);

                if ($showDetails) {
                    $this->line("      🔄 Token updated (was {$oldStatus})");
                    $this->line("         Installment: {$targetInstallment->name}");
                    $this->line("         Expiry: " . Carbon::parse($targetInstallment->end_date)->format('d/m/Y'));
                }

                // Also, if there was any other active token for a different installment, expire it
                $otherActive = FeeClearanceToken::where('student_id', $student->id)
                    ->where('academic_year', $academicYear)
                    ->where('status', 'active')
                    ->where('installment_id', '!=', $targetInstallment->id)
                    ->first();

                if ($otherActive) {
                    $otherActive->update(['status' => 'expired']);
                    if ($showDetails) {
                        $this->line("      ⚠️  Expired previous token for different installment");
                    }
                }

                return 'updated';
            }

            // ========== No existing token for this installment ==========
            // Check if there is any active token for a different installment
            $activeTokenForOther = FeeClearanceToken::where('student_id', $student->id)
                ->where('academic_year', $academicYear)
                ->where('status', 'active')
                ->first();

            if ($activeTokenForOther && $activeTokenForOther->installment_id != $targetInstallment->id) {
                // Expire the old one before creating new
                $activeTokenForOther->update(['status' => 'expired']);
                if ($showDetails) {
                    $this->line("      ⚠️  Expired old token for installment {$activeTokenForOther->installment->name}");
                }
            }

            // Now create new token using service or direct creation
            $tokenResult = $service->process($student, $academicYear);

            if ($tokenResult) {
                if ($showDetails) {
                    $formattedToken = substr($tokenResult->token, 0, 3) . '-' . substr($tokenResult->token, 3, 3);
                    $this->line("      ✅ New token created: {$formattedToken}");
                    $this->line("         Installment: {$targetInstallment->name}");
                    $this->line("         Expires: " . Carbon::parse($targetInstallment->end_date)->format('d/m/Y'));
                }
                return 'created';
            }

            return 'error';

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
        $this->info("✅ Tokens updated: {$stats['updated']}");
        $this->info("✅ Tokens expired: {$stats['expired']}");
        $this->info("✅ New tokens created: {$stats['created']}");
        $this->info("⏭️  No change needed: {$stats['no_change']}");
        $this->info("⚠️  No assignment found: {$stats['no_assignment']}");
        $this->info("❌ Errors: {$stats['errors']}");

        if ($dryRun) {
            $this->warn('⚠️  DRY RUN - No changes were made');
        }
    }
}
