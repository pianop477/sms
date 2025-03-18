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
        //
        $now = Carbon::now();

        $expiredResults = temporary_results::where('expiry_date', '<=', $now)->get();

        // Check for results expiring in the next 6 hours and send notification
        $soonExpiringResults = temporary_results::where('expiry_date', '>=', $now)
                                              ->where('expiry_date', '<=', $now->addHours(6))
                                              ->get();

        foreach($soonExpiringResults as $result) {
            $expiryDate = Carbon::parse($result->expiry_date);

            if($expiryDate->diffInHours($now) <= 6) {
                $teacher = Teacher::find($result->teacher_id);
                $user = User::where('id', $teacher->user_id)->first();

                if($user) {
                    Mail::to($user->email)->send(new ResultExpiryNotification($result));

                    //notify using SMS
                    $nextSmsService = new NextSmsService();
                    $school = school::where('id', $user->school_id)->first();

                    $payload = [
                        'from' => $school->sender_id ?? "SHULE APP",
                        'to' => $this->formatPhoneNumber($user->phone),
                        'text' => 'Your results will expire in 6 hours later, please make sure you submit it as soon as possible to avoid data loss. Regards, ' . $school->school_name,
                        'reference' => uniqid(),
                    ];

                    $response = $nextSmsService->sendSmsByNext($payload['from'], $payload['to'], $payload['text'], $payload['reference']);
                }
            }

            $result->delete();
        }

        $this->info('Expired results have been deleted and teachers have been notified.');

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
