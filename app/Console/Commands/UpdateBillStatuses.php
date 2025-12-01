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
    protected $signature = 'bills:update-statuses';
    protected $description = 'Automatically update bill statuses with optimized queries';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting optimized bill status update...');

        try {
            DB::transaction(function () {
                // 1. UPDATE PAYMENT STATUSES (full paid, overpaid, reactivate expired)
                $this->updatePaymentStatusesInBulk();

                // 2. UPDATE EXPIRED BILLS (only active bills that haven't been paid)
                $this->updateExpiredBillsInBulk();
            });

            $this->info("Bill status update completed successfully");

            Log::info("Optimized bill status update completed");

        } catch (\Exception $e) {
            $this->error("Error updating bill statuses: " . $e->getMessage());
            Log::error("Optimized bill status update failed: " . $e->getMessage());
            return 1;
        }

        return 0;
    }

    private function updatePaymentStatusesInBulk()
    {
        $currentTime = now();

        // Update to FULL PAID - when total payments equal billed amount
        DB::table('school_fees as sf')
            ->join(DB::raw('(SELECT student_fee_id, COALESCE(SUM(amount), 0) as total_paid
                           FROM school_fees_payments
                           GROUP BY student_fee_id) as payments'),
                   'sf.id', '=', 'payments.student_fee_id')
            ->whereNotIn('sf.status', ['cancelled', 'full paid', 'overpaid'])
            ->whereRaw('payments.total_paid = sf.amount')
            ->update([
                'sf.status' => 'full paid',
                'sf.updated_at' => $currentTime
            ]);

        // Update to OVERPAID - when total payments exceed billed amount
        DB::table('school_fees as sf')
            ->join(DB::raw('(SELECT student_fee_id, COALESCE(SUM(amount), 0) as total_paid
                           FROM school_fees_payments
                           GROUP BY student_fee_id) as payments'),
                   'sf.id', '=', 'payments.student_fee_id')
            ->whereNotIn('sf.status', ['cancelled', 'overpaid'])
            ->whereRaw('payments.total_paid > sf.amount')
            ->update([
                'sf.status' => 'overpaid',
                'sf.updated_at' => $currentTime
            ]);

        // Reactivate EXPIRED bills if payments were made (but not fully paid)
        DB::table('school_fees as sf')
            ->join(DB::raw('(SELECT student_fee_id, COALESCE(SUM(amount), 0) as total_paid
                           FROM school_fees_payments
                           GROUP BY student_fee_id) as payments'),
                   'sf.id', '=', 'payments.student_fee_id')
            ->where('sf.status', 'expired')
            ->where('payments.total_paid', '>', 0)
            ->whereRaw('payments.total_paid < sf.amount')
            ->update([
                'sf.status' => 'active',
                'sf.updated_at' => $currentTime
            ]);
    }

    private function updateExpiredBillsInBulk()
    {
        $currentDate = Carbon::now()->format('Y-m-d');

        // Expire only ACTIVE bills that are due and not fully paid/overpaid
        DB::table('school_fees as sf')
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
            })
            ->update([
                'sf.status' => 'expired',
                'sf.updated_at' => now()
            ]);
    }
}
