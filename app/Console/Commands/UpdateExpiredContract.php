<?php

namespace App\Console\Commands;

use App\Models\Contract;
use App\Models\school;
use App\Models\Teacher;
use App\Models\User;
use App\Services\NextSmsService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class UpdateExpiredContract extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'contracts:update-expired-contract';

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
        //
        $now = Carbon::now();
        $expiredContract = Contract::where('status', 'approved')
                                    ->where('end_date', '<', $now)
                                    ->update(['status', 'expired']);
            if($expiredContract) {
                $teacher = Teacher::find($expiredContract->teacher_id);
                $user = User::where('id', $teacher->user_id)->first();
                $school = school::where('id', $user->school_id)->first();
                $nextSmsService = new NextSmsService();

                $payload = [
                    'from' => $school->sender_id ?? "SHULE APP",
                    'to' => $this->formatPhoneNumber($user->phone),
                    'text' => "Hello ". ucwords(strtoupper($user->first_name)). ", your contract has been expired, kindly apply for contract renewal through system. Visit https://shuleapp.tech to apply",
                    'reference' => uniqid(),
                    ];

                $response = $nextSmsService->sendSmsByNext($payload['from'], $payload['to'], $payload['text'], $payload['reference']);

            }
        $this->info("Expired contract Updated: {$expiredContract}");
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
