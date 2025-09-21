<?php

namespace App\Console\Commands;

use App\Models\school;
use App\Models\Teacher;
use App\Models\TodRoster;
use App\Services\NextSmsService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class updateRostersStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'roster:update-rosters-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically update roster statuses based on date';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $today = Carbon::today();

        // 1. ACTIVE → COMPLETED end_date ikipita
        $active = TodRoster::where('status', 'active')->get();
        foreach ($active as $roster) {
            if ($roster->end_date && $today->gt(Carbon::parse($roster->end_date))) {
                $roster->status       = 'completed';
                $roster->is_completed = true;
                $roster->updated_by   = 'system';
                $roster->save();

                $this->info("Roster {$roster->roster_id} imewekwa completed.");
                Log::info("Roster {$roster->roster_id} imewekwa completed.");
            }
        }

        // 2. PENDING → ACTIVE (na SMS ya kuanza)
        $pending = TodRoster::where('status', 'pending')->get();
        $grouped = $pending->groupBy('roster_id');

        foreach ($grouped as $rosterId => $records) {
            foreach ($records as $roster) {
                if (!$roster->start_date) {
                    continue;
                }

                $start = Carbon::parse($roster->start_date);

                if ($today->greaterThanOrEqualTo($start)) {
                    // hakikisha hana roster nyingine active
                    $exists = TodRoster::where('teacher_id', $roster->teacher_id)
                        ->where('status', 'active')
                        ->exists();

                    if (!$exists) {
                        $roster->status     = 'active';
                        $roster->updated_by = 'system';
                        $roster->save();

                        $this->info("Roster {$roster->roster_id} imewekwa active kwa mwalimu {$roster->teacher_id}.");
                        Log::info("Roster {$roster->roster_id} imewekwa active kwa mwalimu {$roster->teacher_id}.");

                        // SMS ya kuanza tu
                        $this->sendStartSms($roster);
                    }
                }
            }
        }

        $this->info('Usasishaji wa roster umekamilika.');
        return Command::SUCCESS;
    }

    /**
     * SMS ya kuanza kwa mwalimu mmoja
     */
    protected function sendStartSms(TodRoster $roster): void
    {
        try {
            $teacher = DB::table('teachers')
                ->join('users', 'users.id', '=', 'teachers.user_id')
                ->select('teachers.id as teacher_id', 'teachers.school_id', 'users.first_name', 'users.phone')
                ->where('teachers.id', $roster->teacher_id)
                ->first();

            if (!$teacher || empty($teacher->phone)) {
                return;
            }

            $phone   = $teacher->phone;
            $message = "Habari {$teacher->first_name}. Ninakutaarifu kuwa utakuwa zamu wiki hii kuanzia {$roster->start_date}
                        na kumalizika {$roster->end_date}. Ninakutakia uwajibikaji mwema wa zamu hii. Asante!";

            $school = school::findOrFail($teacher->school_id);
            $nextSmsService = new NextSmsService();

            $payload = [
                'from' => $school->sender_id,
                'to' => $this->formatPhoneNumber($phone),
                'text' => $message,
                'reference' => $roster->roster_id,
            ];

            $response = $nextSmsService->sendSmsByNext(
                $payload['from'],
                $payload['to'],
                $payload['text'],
                $payload['reference'],
            );

            $this->info("SMS ya kuanza imetumwa kwa {$phone}");
            Log::info("SMS ya kuanza imetumwa kwa {$phone}");
        } catch (\Exception $e) {
            Log::error("Hitilafu ya kutuma SMS: " . $e->getMessage());
        }
    }

    private function formatPhoneNumber(string $phone): string
    {
        $digits = preg_replace('/\D/', '', $phone);

        if (strlen($digits) === 9) {
            return '255' . $digits;
        }
        if (strlen($digits) === 10 && str_starts_with($digits, '0')) {
            return '255' . substr($digits, 1);
        }

        return $digits;
    }
}
