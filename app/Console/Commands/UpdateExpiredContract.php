<?php

namespace App\Console\Commands;

use App\Models\Contract;
use App\Models\school;
use App\Models\school_constracts;
use App\Models\Teacher;
use App\Models\User;
use App\Services\NextSmsService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateExpiredContract extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'contracts:manage';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update the status of the expired contracts';

    /**
     * Execute the console command.
     */

     public function __construct()
     {
        parent::__construct();
     }

     public function handle()
    {
        $this->info('Starting contract management...');

        // 1. Update expired contracts
        $this->updateExpiredContracts();

        // 2. Send expiry reminders (1 month before)
        $this->sendExpiryReminders();

        // 3. Send expiry warnings (1 week before)
        $this->sendExpiryWarnings();

        $this->info('Contract management completed successfully!');
    }

    /**
     * Update contracts that have passed their end date
     */
    private function updateExpiredContracts()
    {
        $expiredContracts = school_constracts::where('status', 'activated')
            ->where('is_active', true)
            ->where('end_date', '<', now())
            ->get();

        $count = 0;
        foreach ($expiredContracts as $contract) {
            $contract->update([
                'is_active' => false,
                'status' => 'expired',
                'expired_at' => now()
            ]);

            // Log the expiry
            // Log::info('Contract expired automatically', [
            //     'contract_id' => $contract->id,
            //     'applicant_id' => $contract->applicant_id,
            //     'end_date' => $contract->end_date
            // ]);

            $count++;
        }

        $this->info("Updated {$count} expired contracts.");
    }

    /**
     * Send reminders for contracts expiring in 1 month
     */
    private function sendExpiryReminders()
    {
        $oneMonthFromNow = now()->addMonth();

        $expiringContracts = school_constracts::where('status', 'activated')
            ->where('is_active', true)
            ->whereMonth('end_date', $oneMonthFromNow->month)
            ->whereYear('end_date', $oneMonthFromNow->year)
            ->get();

        $smsService = new NextSmsService();
        $count = 0;

        foreach ($expiringContracts as $contract) {
            // Get applicant details
            $applicant = $this->resolveApplicantDetails($contract->applicant_id, $contract->school_id);

            if (!empty($applicant['phone'])) {
                try {
                    $destination = $this->formatPhoneNumber($applicant['phone']);

                    $message = "Habari {$applicant['first_name']}, mkataba wako wa ajira utaisha muda wake mwezi 1 kuanzia leo. Tafadhali wasiliana na ofisi ya shule kwa ajili ya mwendelezo.";

                    $smsService->sendSmsByNext(
                        "SHULE APP",
                        $destination,
                        $message,
                        uniqid()
                    );

                    // Log::info('Expiry reminder sent', [
                    //     'contract_id' => $contract->id,
                    //     'phone' => $applicant['phone']
                    // ]);

                    $count++;
                } catch (\Exception $e) {
                    // Log::error('Failed to send expiry reminder', [
                    //     'contract_id' => $contract->id,
                    //     'error' => $e->getMessage()
                    // ]);
                }
            }
        }

        $this->info("Sent {$count} one-month expiry reminders.");
    }

    /**
     * Send warnings for contracts expiring in 1 week
     */
    private function sendExpiryWarnings()
    {
        $oneWeekFromNow = now()->addWeek();

        $expiringContracts = school_constracts::where('status', 'activated')
            ->where('is_active', true)
            ->whereDate('end_date', '<=', $oneWeekFromNow)
            ->whereDate('end_date', '>', now())
            ->get();

        $smsService = new NextSmsService();
        $count = 0;

        foreach ($expiringContracts as $contract) {
            $applicant = $this->resolveApplicantDetails($contract->applicant_id, $contract->school_id);

            if (!empty($applicant['phone'])) {
                try {
                    $destination = $this->formatPhoneNumber($applicant['phone']);
                    $daysLeft = now()->diffInDays($contract->end_date);

                    $message = "Habari {$applicant['first_name']}, mkataba wako wa ajira utaisha muda wake siku {$daysLeft} zijazo. Tafadhali chukua hatua za haraka kwa ajili ya mwendelezo.";

                    $smsService->sendSmsByNext(
                        "SHULE APP",
                        $destination,
                        $message,
                        uniqid()
                    );

                    // Log::info('Expiry warning sent', [
                    //     'contract_id' => $contract->id,
                    //     'days_left' => $daysLeft
                    // ]);

                    $count++;
                } catch (\Exception $e) {
                    // Log::error('Failed to send expiry warning', [
                    //     'contract_id' => $contract->id,
                    //     'error' => $e->getMessage()
                    // ]);
                }
            }
        }

        $this->info("Sent {$count} one-week expiry warnings.");
    }

    /**
     * Helper function to get applicant details
     */
    private function resolveApplicantDetails($applicantId, $schoolId)
    {
        // Try to find in TEACHERS table
        $teacher = DB::table('teachers')
            ->join('users', 'users.id', '=', 'teachers.user_id')
            ->where('teachers.member_id', $applicantId)
            ->where('teachers.school_id', $schoolId)
            ->select(
                'users.first_name',
                'users.last_name',
                'users.phone',
                'teachers.member_id as staff_id'
            )
            ->first();

        if ($teacher) {
            return (array) $teacher;
        }

        // Try to find in TRANSPORT table
        $transport = DB::table('transports')
            ->where('transports.staff_id', $applicantId)
            ->where('transports.school_id', $schoolId)
            ->select(
                'transports.driver_name as first_name',
                DB::raw("'' as last_name"),
                'transports.phone',
                'transports.staff_id'
            )
            ->first();

        if ($transport) {
            return (array) $transport;
        }

        // Try to find in OTHER_STAFFS table
        $otherStaff = DB::table('other_staffs')
            ->where('other_staffs.staff_id', $applicantId)
            ->where('other_staffs.school_id', $schoolId)
            ->select(
                'other_staffs.first_name',
                'other_staffs.last_name',
                'other_staffs.phone',
                'other_staffs.staff_id'
            )
            ->first();

        if ($otherStaff) {
            return (array) $otherStaff;
        }

        return [
            'first_name' => 'Unknown',
            'last_name' => '',
            'phone' => null,
            'staff_id' => $applicantId
        ];
    }

    /**
     * Format phone number helper
     */
    private function formatPhoneNumber($phone)
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);

        if (strlen($phone) == 9) {
            $phone = '255' . $phone;
        } elseif (strlen($phone) == 10 && substr($phone, 0, 1) == '0') {
            $phone = '255' . substr($phone, 1);
        }

        return $phone;
    }
}
