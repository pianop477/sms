<?php

namespace App\Console\Commands;

use App\Models\FeeClearanceToken;
use App\Models\Student;
use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CleanExpiredTokens extends Command
{
    protected $signature = 'tokens:clean-expired
                            {--academic-year= : Academic year to clean (default: current year)}
                            {--school-id= : Clean tokens for specific school}
                            {--days=30 : Keep expired tokens for this many days before hard delete}
                            {--soft-delete : Only mark as expired, don\'t delete}
                            {--archive : Archive deleted tokens to separate table}
                            {--dry-run : Run without actually deleting}
                            {--show-details : Show detailed information per token}';

    protected $description = 'Clean up expired fee clearance tokens';

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
        $softDelete = $this->option('soft-delete');
        $archive = $this->option('archive');
        $dryRun = $this->option('dry-run');
        $showDetails = $this->option('show-details');
        $keepDays = (int) $this->option('days');

        $this->info('🧹 Starting expired tokens cleanup...');
        $this->info("📅 Academic Year: {$academicYear}");
        $this->info("📅 Keep days: {$keepDays}");
        $startTime = microtime(true);
        $this->newLine();

        if ($dryRun) {
            $this->warn('⚠️  DRY RUN MODE: No changes will be made');
            $this->newLine();
        }

        // ✅ Build query for expired tokens
        $query = FeeClearanceToken::with(['student', 'student.class', 'installment'])
            ->where('academic_year', $academicYear);

        if ($schoolId) {
            $query->whereHas('student', function($q) use ($schoolId) {
                $q->where('school_id', $schoolId);
            });
        }

        // 1. Mark tokens that are expired (active but past expiry)
        $expiringQuery = clone $query;
        $expiringQuery->where('status', 'active')
            ->where('expires_at', '<', Carbon::now());

        $expiringTokens = $expiringQuery->get();
        $expiredCount = $expiringTokens->count();

        if ($expiredCount > 0) {
            $this->info("📊 Found {$expiredCount} tokens to mark as expired");

            if ($showDetails && !$dryRun) {
                foreach ($expiringTokens as $token) {
                    $this->displayTokenDetails($token, 'Mark as expired');
                }
            }

            if (!$dryRun) {
                $expiringQuery->update(['status' => 'expired']);
                $this->info("✅ Marked {$expiredCount} expired tokens as expired");

                // Log::info('Tokens marked as expired', [
                //     'academic_year' => $academicYear,
                //     'school_id' => $schoolId,
                //     'count' => $expiredCount
                // ]);
            }
        } else {
            $this->info("✅ No active tokens need to be marked as expired");
        }

        $this->newLine();

        // 2. Handle tokens that have been expired for a while
        $cutoffDate = Carbon::now()->subDays($keepDays);

        $deletedQuery = clone $query;
        $deletedQuery->where('status', 'expired')
            ->where('expires_at', '<', $cutoffDate);

        $deletableTokens = $deletedQuery->get();
        $deletableCount = $deletableTokens->count();

        if ($deletableCount > 0) {
            $this->info("📊 Found {$deletableCount} tokens expired for more than {$keepDays} days");

            if ($showDetails && !$dryRun) {
                foreach ($deletableTokens as $token) {
                    $this->displayTokenDetails($token, $softDelete ? 'Soft delete' : 'Hard delete');
                }
            }

            if (!$dryRun) {
                if ($softDelete) {
                    // Soft delete: Just mark as archived or keep in expired state
                    // You can add an 'archived' status if needed
                    $deletedQuery->update(['status' => 'archived']);
                    $this->info("✅ Archived {$deletableCount} tokens (soft delete)");

                    // Log::info('Tokens archived (soft delete)', [
                    //     'academic_year' => $academicYear,
                    //     'school_id' => $schoolId,
                    //     'count' => $deletableCount,
                    //     'older_than_days' => $keepDays
                    // ]);
                } else {
                    if ($archive) {
                        // Archive to separate table before deletion
                        // $archivedCount = $this->archiveTokens($deletableTokens);
                        // $this->info("📦 Archived {$archivedCount} tokens before deletion");
                    }

                    // Hard delete
                    $deletedCount = $deletedQuery->delete();
                    $this->info("🗑️  Deleted {$deletedCount} tokens older than {$keepDays} days");

                    // Log::info('Tokens hard deleted', [
                    //     'academic_year' => $academicYear,
                    //     'school_id' => $schoolId,
                    //     'count' => $deletedCount,
                    //     'older_than_days' => $keepDays,
                    //     'archived' => $archive
                    // ]);
                }
            }
        } else {
            $this->info("✅ No tokens expired for more than {$keepDays} days");
        }

        $executionTime = round(microtime(true) - $startTime, 2);
        $this->newLine();
        $this->displaySummary($expiredCount, $deletableCount, $executionTime, $academicYear, $dryRun);

        return 0;
    }

    /**
     * Display token details
     */
    private function displayTokenDetails($token, $action)
    {
        $student = $token->student;
        $formattedToken = substr($token->token, 0, 3) . '-' . substr($token->token, 3, 3);
        $expiredDays = Carbon::parse($token->expires_at)->diffInDays(now());

        $this->line("\n   📝 Token: {$formattedToken}");
        $this->line("      Student: " . ($student ? $student->first_name . ' ' . $student->last_name : 'N/A'));
        $this->line("      Admission: " . ($student ? $student->admission_number : 'N/A'));
        $this->line("      Academic Year: {$token->academic_year}");
        $this->line("      Expired: " . Carbon::parse($token->expires_at)->format('d/m/Y'));
        $this->line("      Days expired: {$expiredDays}");
        $this->line("      Action: {$action}");
    }

    /**
     * Archive tokens to separate table before deletion
     */

    /**
     * Display summary statistics
     */
    private function displaySummary($expiredCount, $deletableCount, $executionTime, $academicYear, $dryRun)
    {
        $this->info('📈 ========== SUMMARY ==========');
        $this->info("📅 Academic Year: {$academicYear}");
        $this->info("✅ Tokens marked as expired: {$expiredCount}");
        $this->info("🗑️  Tokens eligible for cleanup: {$deletableCount}");
        $this->info("⏱️  Execution time: {$executionTime} seconds");

        if ($dryRun) {
            $this->warn('⚠️  DRY RUN - No changes were made');
        }
    }
}
