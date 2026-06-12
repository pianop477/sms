<?php

namespace App\Console\Commands;

use App\Models\school_fees_payment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BackfillPaymentAcademicYear extends Command
{
    protected $signature = 'payments:backfill-academic-year
                            {--chunk=100 : Number of records per chunk}
                            {--dry-run : Run without actually updating database}';

    protected $description = 'Backfill academic_year column in school_fees_payments based on related school_fees';

    public function handle()
    {
        $this->info('🚀 Starting backfill of academic_year in payments table...');
        $startTime = microtime(true);

        $chunkSize = (int) $this->option('chunk');
        $dryRun = $this->option('dry-run');
        $updatedTotal = 0;
        $skippedTotal = 0;
        $errors = 0;

        if ($dryRun) {
            $this->warn('⚠️  DRY RUN MODE - No changes will be made');
        }

        // Query to get payments that either have null academic_year or academic_year = 0 (if default)
        $query = school_fees_payment::whereNull('academic_year')
            ->orWhere('academic_year', 0)
            ->orWhere('academic_year', '');

        $totalToUpdate = $query->count();
        if ($totalToUpdate === 0) {
            $this->info('✅ No payments need backfill. Exiting.');
            return 0;
        }

        $this->info("📊 Found {$totalToUpdate} payments to update.");

        $progressBar = $this->output->createProgressBar($totalToUpdate);
        $progressBar->start();

        // Chunk processing
        $query->chunk($chunkSize, function ($payments) use (&$updatedTotal, &$skippedTotal, &$errors, $dryRun, $progressBar) {
            foreach ($payments as $payment) {
                try {
                    // Get the related school_fee (bill)
                    $bill = $payment->bill; // assume relationship 'bill' exists
                    if (!$bill || !$bill->academic_year) {
                        $this->warn("   ⚠️  Payment ID {$payment->id} has no related bill or missing academic_year.");
                        $skippedTotal++;
                        $progressBar->advance();
                        continue;
                    }

                    $academicYear = $bill->academic_year;

                    if (!$dryRun) {
                        // Update directly to avoid model events if any
                        DB::table('school_fees_payments')
                            ->where('id', $payment->id)
                            ->update(['academic_year' => $academicYear]);
                    }
                    $updatedTotal++;
                } catch (\Exception $e) {
                    $errors++;
                    Log::error('Failed to update payment academic_year', [
                        'payment_id' => $payment->id,
                        'error' => $e->getMessage()
                    ]);
                }
                $progressBar->advance();
            }
        });

        $progressBar->finish();
        $this->newLine(2);

        $executionTime = round(microtime(true) - $startTime, 2);

        $this->info('📈 ========== SUMMARY ==========');
        $this->info("✅ Payments updated: {$updatedTotal}");
        $this->info("⚠️  Skipped (no related bill/year): {$skippedTotal}");
        $this->info("❌ Errors: {$errors}");
        $this->info("⏱️  Execution time: {$executionTime} seconds");

        if ($dryRun) {
            $this->warn('⚠️  DRY RUN - No actual changes were made.');
        } else {
            $this->info('✅ Backfill completed successfully.');
        }

        return 0;
    }
}
