<?php

namespace App\Console\Commands;

use App\Mail\ResultExpiryNotification;
use App\Models\school;
use App\Models\Teacher;
use App\Models\temporary_results;
use App\Models\User;
use App\Services\NextSmsService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class DeleteExpiredResults extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'results:delete-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete expired results and notify teachers';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $now = Carbon::now();
            $sixHoursLater = $now->clone()->addHours(6);

            // 1. Delete expired results
            $deletedCount = temporary_results::where('expiry_date', '<=', $now)->delete();
            $this->info($deletedCount . ' expired results deleted.');

            // 2. Select results that will expire within 6 hours AND have not been notified
            $soonExpiringResults = temporary_results::whereBetween('expiry_date', [$now, $sixHoursLater])
                ->where('expiry_notified', false)
                ->get();

            foreach ($soonExpiringResults as $result) {

                $teacher = Teacher::find($result->teacher_id);
                $school  = School::find($result->school_id);
                $user    = $teacher ? User::find($teacher->user_id) : null;

                // Skip if important data missing
                if (!$user || !$school || empty($user->phone)) {
                    continue;
                }

                /*
                |--------------------------------------------------------------------------
                | SEND EMAIL NOTIFICATION (ONCE ONLY)
                |--------------------------------------------------------------------------
                */
                try {
                    Mail::to($user->email)->send(new ResultExpiryNotification($result));
                } catch (\Exception $e) {
                    // Log::error("Email not sent for result {$result->id}: " . $e->getMessage());
                }

                /*
                |--------------------------------------------------------------------------
                | SEND SMS NOTIFICATION (ONCE ONLY)
                |--------------------------------------------------------------------------
                */
                // try {
                //     $nextSmsService = new NextSmsService();

                //     $payload = [
                //         'from'      => $school->sender_id ?? "SHULE APP",
                //         'to'        => $this->formatPhoneNumber($user->phone),
                //         'text'      => "Your pending results will expire in 6 hours. Please submit to avoid data loss. Regards, " . strtoupper($school->school_name),
                //         'reference' => uniqid(),
                //     ];

                //     $nextSmsService->sendSmsByNext(
                //         $payload['from'],
                //         $payload['to'],
                //         $payload['text'],
                //         $payload['reference']
                //     );
                // } catch (\Exception $e) {
                //     // Log::error("SMS not sent for result {$result->id}: " . $e->getMessage());
                // }

                /*
                |--------------------------------------------------------------------------
                | MARK AS NOTIFIED — VERY IMPORTANT
                |--------------------------------------------------------------------------
                */
                $result->expiry_notified = true;
                $result->save();
            }

            $this->info(count($soonExpiringResults) . ' teachers notified (once only).');

        } catch (\Exception $e) {
            // Log::error('Error in results:cleanup command', [
            //     'error' => $e->getMessage(),
            //     'trace' => $e->getTraceAsString(),
            // ]);

            $this->error('An error occurred. Check logs for details.');
        }
    }


    private function formatPhoneNumber($phone)
    {
        // Remove any non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Add TZ country code
        if (strlen($phone) == 9) {
            $phone = '255' . $phone;
        } elseif (strlen($phone) == 10 && substr($phone, 0, 1) == '0') {
            $phone = '255' . substr($phone, 1);
        }

        return $phone;
    }

}
