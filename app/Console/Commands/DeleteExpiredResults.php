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

            // **1. Futa Matokeo Yaliyo-Expire**
            $deletedCount = temporary_results::where('expiry_date', '<=', $now)->delete();
            $this->info($deletedCount . ' expired results deleted.');

            // **2. Pata Matokeo Yatakayo-Expire Ndani ya Masaa 6**
            $soonExpiringResults = temporary_results::whereBetween('expiry_date', [$now, $sixHoursLater])->get();

            foreach ($soonExpiringResults as $result) {
                $teacher = Teacher::find($result->teacher_id);
                $school  = School::find($result->school_id);
                $user    = $teacher ? User::find($teacher->user_id) : null;

                if (!$user || !$school || empty($user->phone)) {
                    continue; // Ruka kama data muhimu haipo
                }

                // **Tuma Email Notification**
                try {
                    Mail::to($user->email)->send(new ResultExpiryNotification($result));
                } catch (\Exception $e) {
                    Log::error("Email not sent for result {$result->id}: " . $e->getMessage());
                }

                // **Tuma SMS Notification**
                try {
                    $nextSmsService = new NextSmsService();
                    $payload = [
                        'from'      => $school->sender_id ?? "SHULE APP",
                        'to'        => $this->formatPhoneNumber($user->phone),
                        'text'      => 'Your pending results will expire in 6 hours. Please submit to avoid data loss. Regards, ' . strtoupper($school->school_name),
                        'reference' => uniqid(),
                    ];
                    $nextSmsService->sendSmsByNext(
                        $payload['from'],
                        $payload['to'],
                        $payload['text'],
                        $payload['reference']
                    );
                } catch (\Exception $e) {
                    Log::error("SMS not sent for result {$result->id}: " . $e->getMessage());
                }
            }

            $this->info(count($soonExpiringResults) . ' teachers notified about expiring results.');
        } catch (\Exception $e) {
            Log::error('Error in results:cleanup command', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            $this->error('An error occurred while processing expired results. Check logs for details.');
        }
    }

    private function formatPhoneNumber($phone)
    {
        // Remove any non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Ensure the number starts with the country code (e.g., 255 for Tanzania)
        if (strlen($phone) == 9) {
            $phone = '255' . $phone;
        } elseif (strlen($phone) == 10 && substr($phone, 0, 1) == '0') {
            $phone = '255' . substr($phone, 1);
        }

        return $phone;
    }
}
