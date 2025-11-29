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
         try {
             $now = Carbon::now();

             // Pata mikataba yote iliyoisha muda
             $expiredContracts = Contract::where('status', 'approved')
                                         ->where('end_date', '<', $now)
                                         ->get();

             if ($expiredContracts->isEmpty()) {
                 $this->info('No expired contracts found.');
                 return;
             }

             foreach ($expiredContracts as $contract) {
                 // Badilisha status ya contract kuwa 'expired'
                 $contract->update(['status' => 'expired']);

                 $teacher = Teacher::find($contract->teacher_id);
                 if (!$teacher) continue;

                 $user = User::find($teacher->user_id);
                 if (!$user) continue;

                 $school = School::find($user->school_id);
                 if (!$school) continue;

                 // Tuma SMS notification
                 $nextSmsService = new NextSmsService();
                 $payload = [
                     'from' => $school->sender_id ?? "SHULE APP",
                     'to' => $this->formatPhoneNumber($user->phone),
                     'text' => "Hello " . ucwords(strtolower($user->first_name)) . ", your contract has expired. Kindly apply for renewal via the system. Visit https://shuleapp.tech to apply.",
                     'reference' => uniqid(),
                 ];

                 $response = $nextSmsService->sendSmsByNext(
                     $payload['from'],
                     $payload['to'],
                     $payload['text'],
                     $payload['reference']
                 );
                 if(!$response['success']) {
                    throw new \Exception($response['error']);
                    // Alert()->toast('SMS failed: '.$response['error'], 'error');
                    // return back();
                }

                //  Log::info("Contract expired for Teacher ID: {$teacher->id}, User: {$user->first_name}");
             }

             $this->info(count($expiredContracts) . ' contracts updated to expired status.');
         } catch (\Exception $e) {
            //  Log::error('Error in contracts:expire command: ' . $e->getMessage());
             $this->error('An error occurred while expiring contracts.');
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
