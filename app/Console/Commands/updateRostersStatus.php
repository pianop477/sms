<?php

namespace App\Console\Commands;

use App\Models\school;
use App\Models\Teacher;
use App\Models\TodRoster;
use App\Services\NextSmsService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class updateRostersStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-rosters-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically update roster statuses based on date';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::now()->format('Y-m-d');

        // 1. Kukamata roster zote zenye status active
        $activeRosters = TodRoster::where('status', 'active')->get();

        foreach ($activeRosters as $roster) {
            // Kuhakikisha end_date sio null
            if ($roster->end_date) {
                // Kubadilisha status kuwa completed ikiwa end_date imepita
                if ($today > $roster->end_date) {
                    $roster->status = 'completed';
                    $roster->is_completed = true;
                    $roster->updated_by = 'system';
                    $roster->save();

                    $this->info("Roster {$roster->roster_id} imebadilishwa kuwa completed.");
                    // Log::info("Roster {$roster->roster_id} imebadilishwa kuwa completed.");
                }
            }
        }

        // 2. Kukamata roster zote zenye status pending
        $pendingRosters = TodRoster::where('status', 'pending')
                                    ->whereDate('start_date', '>=', $today)
                                    ->get();

        foreach ($pendingRosters as $roster) {
            // Kuhakikisha start_date sio null
            if ($roster->start_date) {
                // Kubadilisha status kuwa active ikiwa start_date imefika
                if ($today >= $roster->start_date) {
                    // Kuhakikisha hakuna roster nyingine active kwa huyo mwalimu
                    $existingActive = TodRoster::where('teacher_id', $roster->teacher_id)
                                                ->where('status', 'active')
                                                ->exists();

                    if (!$existingActive) {
                        $roster->status = 'active';
                        $roster->updated_by = 'system';
                        $roster->save();

                        $this->info("Roster {$roster->roster_id} imebadilishwa kuwa active.");
                        // Log::info("Roster {$roster->roster_id} imebadilishwa kuwa active.");

                        // Kutuma SMS kwa mwalimu kuwa zamu yake imeanza
                        $this->sendRosterStartSms($roster);
                    }
                } else {
                    // Kutuma tahadhari ya zamu inayokaribia (siku 1 kabla)
                    $daysUntilStart = Carbon::parse($roster->start_date)->diffInDays($today);
                    if ($daysUntilStart == 1) {
                        $this->sendRosterReminderSms($roster);
                    }
                }
            }
        }

        $this->info("Usasishaji wa roster umekamilika.");
    }

    /**
     * Kutuma SMS kwa mwalimu kuwa zamu yake imeanza
     */
    protected function sendRosterStartSms(TodRoster $roster)
    {
        try {
            // Kupata namba ya simu ya mwalimu
            $teacher = Teacher::query()->join('user', 'users.id', 'teaches.user_id')
                                        ->select('users.*')
                                        ->find($roster->teacher_id);

            if ($teacher && $teacher->phone) {
                $phone = $teacher->phone;
                $message = "Habari {$teacher->user->name}. Zamu yako imeanza leo tarehe {$roster->start_date} na itaisha tarehe {$roster->end_date}. Ninakutakiwa uwajibikaji mwema katika wiki hii!";

                // Tuma SMS hapa
                $nextSmsService = new NextSmsService();
                $schoolId = $teacher->school_id;
                $school = school::findOrFail($schoolId);

                $payload = [
                    "from" => $school->sender_id,
                    "to" => $this->formatPhoneNumber($phone),
                    "text" => $message,
                    "reference" => $roster->roster_id,
                ];
                $response = $nextSmsService->sendSmsByNext($payload['from'], $payload['to'], $payload['text'], $payload['reference']);

                $this->info("SMS ya kuanza kwa zamu imetumwa kwa namba: {$phone}");
                Log::info("SMS ya kuanza kwa zamu imetumwa kwa namba: {$phone}");
            }
        } catch (\Exception $e) {
            Log::error("Hitilafu ya kutuma SMS: " . $e->getMessage());
        }
    }


    /**
     * Kubadilisha namba ya simu kuwa muundo sahihi
     */
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
