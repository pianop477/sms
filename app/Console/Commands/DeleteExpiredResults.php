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

            // **1. Tafuta na Futa Matokeo Yaliyo-Expire**
            $expiredResults = temporary_results::where('expiry_date', '<=', $now)->get();
            foreach ($expiredResults as $result) {
                $result->delete();
            }
            $this->info(count($expiredResults) . ' expired results deleted.');

            // **2. Tafuta Matokeo Yatakayo-Expire Karibuni (ndani ya masaa 6)**
            $soonExpiringResults = temporary_results::whereBetween('expiry_date', [$now, $sixHoursLater])->get();

            foreach ($soonExpiringResults as $result) {
                $teacher = Teacher::find($result->teacher_id);
                $user = User::find($teacher?->user_id);

                if ($user) {
                    // **Tuma Email Notification**
                    Mail::to($user->email)->send(new ResultExpiryNotification($result));

                    // **Tuma SMS Notification**
                    $nextSmsService = new NextSmsService();
                    $school = school::find($user->school_id);

                    $payload = [
                        'from' => $school->sender_id ?? "SHULE APP",
                        'to' => $this->formatPhoneNumber($user->phone),
                        'text' => 'Your results will expire in 6 hours. Please submit them to avoid data loss. Regards, ' . strtoupper($school->school_name),
                        'reference' => uniqid(),
                    ];

                    $nextSmsService->sendSmsByNext($payload['from'], $payload['to'], $payload['text'], $payload['reference']);
                }
            }

            $this->info(count($soonExpiringResults) . ' teachers notified about expiring results.');
        } catch (\Exception $e) {
            // \Log::error('Error in results:cleanup command: ' . $e->getMessage());
            $this->error('An error occurred while processing expired results.');
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
