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
        $now = Carbon::now();

        // 1. ACTIVE → COMPLETED end_date ikipita
        $active = TodRoster::where('status', 'active')->get();
        foreach ($active as $roster) {
            if ($roster->end_date && $today->gt(Carbon::parse($roster->end_date))) {
                $roster->status       = 'completed';
                $roster->is_completed = true;
                $roster->updated_by   = 'system';
                $roster->save();

                $this->info("Roster {$roster->roster_id} imewekwa completed.");
                // Log::info("Roster {$roster->roster_id} imewekwa completed.");
            }
        }

        // 2. Check for PENDING rosters that need reminder SMS (one day before start)
        $pending = TodRoster::where('status', 'pending')
            ->whereNotNull('start_date')
            ->whereDate('start_date', '=', $today->copy()->addDay())
            ->get();

        foreach ($pending as $roster) {
            // Check if reminder hasn't been sent yet
            if (!$roster->reminder_sent && $now->hour >= 20) {
                $this->sendReminderSms($roster);

                // Mark reminder as sent
                $roster->reminder_sent = true;
                $roster->updated_by = 'system';
                $roster->save();
            }
        }

        // 3. PENDING → ACTIVE on actual start date
        $pendingToActivate = TodRoster::where('status', 'pending')
            ->whereNotNull('start_date')
            ->whereDate('start_date', '<=', $today)
            ->get();

        $grouped = $pendingToActivate->groupBy('roster_id');

        foreach ($grouped as $rosterId => $records) {
            foreach ($records as $roster) {
                // hakikisha hana roster nyingine active
                $exists = TodRoster::where('teacher_id', $roster->teacher_id)
                    ->where('status', 'active')
                    ->exists();

                if (!$exists) {
                    $roster->status     = 'active';
                    $roster->updated_by = 'system';
                    $roster->save();

                    $this->info("Roster {$roster->roster_id} imewekwa active kwa mwalimu {$roster->teacher_id}.");
                    // Log::info("Roster {$roster->roster_id} imewekwa active kwa mwalimu {$roster->teacher_id}.");

                    // SMS ya kuanza (kwa siku hiyo ya kuanza)
                    $this->sendStartSms($roster);
                }
            }
        }

        $this->info('Usasishaji wa roster umekamilika.');
        return Command::SUCCESS;
    }

    /**
     * SMS ya ukumbusho (siku moja kabla)
     */
    protected function sendReminderSms(TodRoster $roster): void
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

            $phone = $teacher->phone;
            $startDate = Carbon::parse($roster->start_date);
            $endDate = Carbon::parse($roster->end_date);

            $message = "Habari {$teacher->first_name}! Unakumbushwa kwamba utakuwa zamu kesho "
                . $startDate->format('d/m/Y') . " hadi "
                . $endDate->format('d/m/Y')
                . ". Tafadhali jiandae kwa ajili ya kazi yako ya zamu.";

            $school = school::findOrFail($teacher->school_id);
            $nextSmsService = new NextSmsService();

            $payload = [
                'from' => $school->sender_id,
                'to' => $this->formatPhoneNumber($phone),
                'text' => $message,
                'reference' => $roster->roster_id . '_reminder',
            ];

            $response = $nextSmsService->sendSmsByNext(
                $payload['from'],
                $payload['to'],
                $payload['text'],
                $payload['reference'],
            );

            if (!$response['success']) {
                throw new \Exception($response['error']);
            }

            $this->info("SMS ya ukumbusho imetumwa kwa {$phone}");
            // Log::info("SMS ya ukumbusho imetumwa kwa {$phone}");
        } catch (\Exception $e) {
            $this->error("Hitilafu ya kutuma SMS ya ukumbusho: " . $e->getMessage());
            // Log::error("Hitilafu ya kutuma SMS ya ukumbusho: " . $e->getMessage());
        }
    }

    /**
     * SMS ya kuanza (siku hiyo ya kuanza)
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

            $phone = $teacher->phone;
            $message = "Habari {$teacher->first_name}! Utakuwa zamu wiki hii kuanzia " .
                Carbon::parse($roster->start_date)->format('d/m/Y') . " hadi " .
                Carbon::parse($roster->end_date)->format('d/m/Y') .
                " Ninakutakia uwajibikaji mwema.";

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

            if (!$response['success']) {
                throw new \Exception($response['error']);
            }

            $this->info("SMS ya kuanza imetumwa kwa {$phone}");
            // Log::info("SMS ya kuanza imetumwa kwa {$phone}");
        } catch (\Exception $e) {
            $this->error("Hitilafu ya kutuma SMS: " . $e->getMessage());
            // Log::error("Hitilafu ya kutuma SMS: " . $e->getMessage());
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
