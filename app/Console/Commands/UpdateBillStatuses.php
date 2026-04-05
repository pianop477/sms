<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateBillStatuses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bills:update-statuses
                            {--student-id= : Update statuses for specific student}
                            {--bill-id= : Update statuses for specific bill}
                            {--dry-run : Show what would be updated without making changes}
                            {--force : Force update even if no changes detected}
                            {--show-details : Show detailed information about each update}';

    protected $description = 'Automatically update bill statuses with dynamic detection of payment changes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $startTime = microtime(true);
        $this->info('🚀 Starting dynamic bill status update...');
        $this->newLine();

        $dryRun = $this->option('dry-run');
        $showDetails = $this->option('show-details');
        $force = $this->option('force');

        if ($dryRun) {
            $this->warn('⚠️  DRY RUN MODE: No changes will be made');
            $this->newLine();
        }

        try {
            $stats = [
                'full_paid' => 0,
                'overpaid' => 0,
                'reactivated' => 0,
                'expired' => 0,
                'partial_to_active' => 0,
                'errors' => 0,
                'skipped' => 0
            ];

            DB::transaction(function () use (&$stats, $dryRun, $showDetails, $force) {
                // 1. UPDATE PAYMENT STATUSES (full paid, overpaid, reactivate expired)
                $this->updatePaymentStatusesInBulk($stats, $dryRun, $showDetails, $force);

                // 2. UPDATE EXPIRED BILLS (only active bills that haven't been paid)
                $this->updateExpiredBillsInBulk($stats, $dryRun, $showDetails, $force);

                // 3. ✅ NEW: Handle bills that were overpaid but payment was reduced
                $this->handleReducedPayments($stats, $dryRun, $showDetails, $force);

                // 4. ✅ NEW: Handle bills that were full paid but payment was reduced
                $this->handleFullPaidToActive($stats, $dryRun, $showDetails, $force);

                // 5. ✅ NEW: Handle bills that were overpaid to full paid (when payment adjusted)
                $this->handleOverpaidToFullPaid($stats, $dryRun, $showDetails, $force);
            });

            $executionTime = round(microtime(true) - $startTime, 2);

            $this->newLine();
            $this->info('📈 ========== SUMMARY ==========');
            $this->info("✅ Full Paid: {$stats['full_paid']}");
            $this->info("⚠️  Overpaid: {$stats['overpaid']}");
            $this->info("🔄 Reactivated (Expired → Active): {$stats['reactivated']}");
            $this->info("🔄 Partial to Active: {$stats['partial_to_active']}");
            $this->info("📌 Expired: {$stats['expired']}");
            $this->info("❌ Errors: {$stats['errors']}");
            $this->info("⏭️  Skipped: {$stats['skipped']}");
            $this->info("⏱️  Execution time: {$executionTime} seconds");

            if ($dryRun) {
                $this->warn('⚠️  DRY RUN - No changes were made');
            }

            Log::info("Dynamic bill status update completed", $stats);

        } catch (\Exception $e) {
            $this->error("❌ Error updating bill statuses: " . $e->getMessage());
            Log::error("Dynamic bill status update failed: " . $e->getMessage());
            return 1;
        }

        return 0;
    }

    /**
     * Update bills that are fully paid or overpaid
     * ✅ Now checks for dynamic changes and can revert statuses
     */
    private function updatePaymentStatusesInBulk(&$stats, $dryRun, $showDetails, $force)
    {
        $currentTime = now();

        // Build query with filters
        $query = DB::table('school_fees as sf')
            ->join(DB::raw('(SELECT student_fee_id, COALESCE(SUM(amount), 0) as total_paid
                           FROM school_fees_payments
                           GROUP BY student_fee_id) as payments'),
                   'sf.id', '=', 'payments.student_fee_id');

        $this->applyFilters($query);

        // 1. Update to FULL PAID - when total payments equal billed amount
        $fullPaidQuery = clone $query;
        $fullPaidQuery->whereNotIn('sf.status', ['cancelled', 'full paid', 'overpaid'])
            ->whereRaw('payments.total_paid = sf.amount');

        $fullPaidBills = $fullPaidQuery->select('sf.id', 'sf.amount', 'payments.total_paid', 'sf.status')
            ->get();

        foreach ($fullPaidBills as $bill) {
            if ($showDetails) {
                $this->line("\n   📝 Bill #{$bill->id}");
                $this->line("      Status: {$bill->status} → full paid");
                $this->line("      Amount: " . number_format($bill->amount, 0) . " TZS");
                $this->line("      Paid: " . number_format($bill->total_paid, 0) . " TZS");
            }

            if (!$dryRun) {
                DB::table('school_fees')
                    ->where('id', $bill->id)
                    ->update([
                        'status' => 'full paid',
                        'updated_at' => $currentTime
                    ]);
            }
            $stats['full_paid']++;
        }

        // 2. Update to OVERPAID - when total payments exceed billed amount
        $overpaidQuery = clone $query;
        $overpaidQuery->whereNotIn('sf.status', ['cancelled', 'overpaid'])
            ->whereRaw('payments.total_paid > sf.amount');

        $overpaidBills = $overpaidQuery->select('sf.id', 'sf.amount', 'payments.total_paid', 'sf.status')
            ->get();

        foreach ($overpaidBills as $bill) {
            $overpaidAmount = $bill->total_paid - $bill->amount;
            if ($showDetails) {
                $this->line("\n   ⚠️  Bill #{$bill->id}");
                $this->line("      Status: {$bill->status} → overpaid");
                $this->line("      Amount: " . number_format($bill->amount, 0) . " TZS");
                $this->line("      Paid: " . number_format($bill->total_paid, 0) . " TZS");
                $this->line("      Overpaid: " . number_format($overpaidAmount, 0) . " TZS");
            }

            if (!$dryRun) {
                DB::table('school_fees')
                    ->where('id', $bill->id)
                    ->update([
                        'status' => 'overpaid',
                        'updated_at' => $currentTime
                    ]);
            }
            $stats['overpaid']++;
        }

        // 3. Reactivate EXPIRED bills if payments were made (but not fully paid)
        $reactivateQuery = clone $query;
        $reactivateQuery->where('sf.status', 'expired')
            ->where('payments.total_paid', '>', 0)
            ->whereRaw('payments.total_paid < sf.amount');

        $reactivateBills = $reactivateQuery->select('sf.id', 'sf.amount', 'payments.total_paid', 'sf.status')
            ->get();

        foreach ($reactivateBills as $bill) {
            if ($showDetails) {
                $this->line("\n   🔄 Bill #{$bill->id}");
                $this->line("      Status: {$bill->status} → active");
                $this->line("      Payment detected: " . number_format($bill->total_paid, 0) . " TZS");
            }

            if (!$dryRun) {
                DB::table('school_fees')
                    ->where('id', $bill->id)
                    ->update([
                        'status' => 'active',
                        'updated_at' => $currentTime
                    ]);
            }
            $stats['reactivated']++;
        }
    }

    /**
     * ✅ NEW: Handle bills that were overpaid but payment was reduced
     * This reverts overpaid bills to active or partial paid status
     */
    private function handleReducedPayments(&$stats, $dryRun, $showDetails, $force)
    {
        $currentTime = now();

        $query = DB::table('school_fees as sf')
            ->join(DB::raw('(SELECT student_fee_id, COALESCE(SUM(amount), 0) as total_paid
                           FROM school_fees_payments
                           GROUP BY student_fee_id) as payments'),
                   'sf.id', '=', 'payments.student_fee_id')
            ->where('sf.status', 'overpaid')
            ->whereRaw('payments.total_paid <= sf.amount');

        $this->applyFilters($query);

        $reducedBills = $query->select('sf.id', 'sf.amount', 'payments.total_paid', 'sf.status')
            ->get();

        foreach ($reducedBills as $bill) {
            $newStatus = ($bill->total_paid == $bill->amount) ? 'full paid' : 'active';

            if ($showDetails) {
                $this->line("\n   🔄 Bill #{$bill->id} (Payment Reduced)");
                $this->line("      Status: {$bill->status} → {$newStatus}");
                $this->line("      Amount: " . number_format($bill->amount, 0) . " TZS");
                $this->line("      Paid: " . number_format($bill->total_paid, 0) . " TZS");
            }

            if (!$dryRun) {
                DB::table('school_fees')
                    ->where('id', $bill->id)
                    ->update([
                        'status' => $newStatus,
                        'updated_at' => $currentTime
                    ]);
            }
            $stats['partial_to_active']++;
        }
    }

    /**
     * ✅ NEW: Handle bills that were full paid but payment was reduced
     * This reverts full paid bills back to active
     */
    private function handleFullPaidToActive(&$stats, $dryRun, $showDetails, $force)
    {
        $currentTime = now();

        $query = DB::table('school_fees as sf')
            ->join(DB::raw('(SELECT student_fee_id, COALESCE(SUM(amount), 0) as total_paid
                           FROM school_fees_payments
                           GROUP BY student_fee_id) as payments'),
                   'sf.id', '=', 'payments.student_fee_id')
            ->where('sf.status', 'full paid')
            ->whereRaw('payments.total_paid < sf.amount');

        $this->applyFilters($query);

        $reducedBills = $query->select('sf.id', 'sf.amount', 'payments.total_paid', 'sf.status')
            ->get();

        foreach ($reducedBills as $bill) {
            $newStatus = ($bill->total_paid > 0) ? 'active' : 'pending';

            if ($showDetails) {
                $this->line("\n   🔄 Bill #{$bill->id} (Payment Reduced from Full Paid)");
                $this->line("      Status: {$bill->status} → {$newStatus}");
                $this->line("      Amount: " . number_format($bill->amount, 0) . " TZS");
                $this->line("      Paid: " . number_format($bill->total_paid, 0) . " TZS");
                $this->line("      Remaining: " . number_format($bill->amount - $bill->total_paid, 0) . " TZS");
            }

            if (!$dryRun) {
                DB::table('school_fees')
                    ->where('id', $bill->id)
                    ->update([
                        'status' => $newStatus,
                        'updated_at' => $currentTime
                    ]);
            }
            $stats['partial_to_active']++;
        }
    }

    /**
     * ✅ NEW: Handle bills that were overpaid to full paid (when payment adjusted)
     */
    private function handleOverpaidToFullPaid(&$stats, $dryRun, $showDetails, $force)
    {
        $currentTime = now();

        $query = DB::table('school_fees as sf')
            ->join(DB::raw('(SELECT student_fee_id, COALESCE(SUM(amount), 0) as total_paid
                           FROM school_fees_payments
                           GROUP BY student_fee_id) as payments'),
                   'sf.id', '=', 'payments.student_fee_id')
            ->where('sf.status', 'overpaid')
            ->whereRaw('payments.total_paid = sf.amount');

        $this->applyFilters($query);

        $exactPaidBills = $query->select('sf.id', 'sf.amount', 'payments.total_paid', 'sf.status')
            ->get();

        foreach ($exactPaidBills as $bill) {
            if ($showDetails) {
                $this->line("\n   🔄 Bill #{$bill->id} (Payment Adjusted to Exact)");
                $this->line("      Status: {$bill->status} → full paid");
                $this->line("      Amount: " . number_format($bill->amount, 0) . " TZS");
                $this->line("      Paid: " . number_format($bill->total_paid, 0) . " TZS");
            }

            if (!$dryRun) {
                DB::table('school_fees')
                    ->where('id', $bill->id)
                    ->update([
                        'status' => 'full paid',
                        'updated_at' => $currentTime
                    ]);
            }
            $stats['full_paid']++;
        }
    }

    /**
     * Update expired bills (due date passed)
     */
    private function updateExpiredBillsInBulk(&$stats, $dryRun, $showDetails, $force)
    {
        $currentDate = Carbon::now()->format('Y-m-d');

        $query = DB::table('school_fees as sf')
            ->leftJoin(DB::raw('(SELECT student_fee_id, COALESCE(SUM(amount), 0) as total_paid
                               FROM school_fees_payments
                               GROUP BY student_fee_id) as payments'),
                       'sf.id', '=', 'payments.student_fee_id')
            ->where('sf.status', 'active')
            ->whereNotNull('sf.due_date')
            ->where('sf.due_date', '<', $currentDate)
            ->where(function($query) {
                $query->whereNull('payments.total_paid')
                      ->orWhere(function($q) {
                          $q->whereRaw('payments.total_paid < sf.amount')
                            ->where('payments.total_paid', '>=', 0);
                      });
            });

        $this->applyFilters($query);

        $expiredBills = $query->select('sf.id', 'sf.amount', 'sf.due_date', 'payments.total_paid')
            ->get();

        foreach ($expiredBills as $bill) {
            if ($showDetails) {
                $this->line("\n   📌 Bill #{$bill->id}");
                $this->line("      Status: active → expired");
                $this->line("      Due Date: {$bill->due_date}");
                $this->line("      Amount: " . number_format($bill->amount, 0) . " TZS");
                $this->line("      Paid: " . number_format($bill->total_paid ?? 0, 0) . " TZS");
            }

            if (!$dryRun) {
                DB::table('school_fees')
                    ->where('id', $bill->id)
                    ->update([
                        'status' => 'expired',
                        'updated_at' => now()
                    ]);
            }
            $stats['expired']++;
        }
    }

    /**
     * Apply filters to query (student-id, bill-id)
     */
    private function applyFilters(&$query)
    {
        if ($this->option('student-id')) {
            $query->where('sf.student_id', $this->option('student-id'));
        }

        if ($this->option('bill-id')) {
            $query->where('sf.id', $this->option('bill-id'));
        }
    }
}
