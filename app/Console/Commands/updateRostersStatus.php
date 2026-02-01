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
        $today     = Carbon::today();
        $now       = Carbon::now();
        $dayOfWeek = $today->dayOfWeek; // 0=Sun, 1=Mon

        /**
         * 1. ACTIVE → COMPLETED (end_date ikipita)
         */
        $activeRosters = TodRoster::where('status', 'active')->get();

        foreach ($activeRosters as $roster) {
            if ($roster->end_date && $today->gt(Carbon::parse($roster->end_date))) {
                $roster->update([
                    'status'       => 'completed',
                    'is_completed' => true,
                    'updated_by'   => 'system',
                ]);

                $this->info("Roster {$roster->roster_id} imewekwa COMPLETED.");
            }
        }

        /**
         * 2. REMINDER SMS (MOJA TU)
         * - siku 1 kabla ya start_date
         * - baada ya saa 20:00
         * - reminder_sent = 0 → 1
         */
        $pendingForReminder = TodRoster::where('status', 'pending')
            ->where('reminder_sent', 0)
            ->whereNotNull('start_date')
            ->get();

        foreach ($pendingForReminder as $roster) {
            $startDate = Carbon::parse($roster->start_date);

            // calendar-safe: siku 1 kabla
            if ($today->diffInDays($startDate, false) === 1 && $now->hour >= 20) {
                $this->sendReminderSms($roster);

                $roster->update([
                    'reminder_sent' => 1,
                    'updated_by'    => 'system',
                ]);

                $this->info("Reminder SMS imetumwa kwa roster {$roster->roster_id}");
            }
        }

        /**
         * 3. PENDING → ACTIVE (SILENT)
         * - siku ya kuanza TU
         * - HAKUNA SMS
         */
        if ($dayOfWeek >= 1 && $dayOfWeek <= 5) { // Mon–Fri
            $pendingToActivate = TodRoster::where('status', 'pending')
                ->whereDate('start_date', $today)
                ->get();

            foreach ($pendingToActivate as $roster) {
                $exists = TodRoster::where('teacher_id', $roster->teacher_id)
                    ->where('status', 'active')
                    ->exists();

                if (!$exists) {
                    $roster->update([
                        'status'     => 'active',
                        'updated_by' => 'system',
                    ]);

                    $this->info("Roster {$roster->roster_id} imewekwa ACTIVE (silent).");
                }
            }
        } else {
            $this->info('Weekend: hakuna activation.');
        }

        $this->info('Usasishaji wa roster umekamilika.');
        return Command::SUCCESS;
    }

    /**
     * SMS YA UKUMBUSHO (SMS MOJA TU)
     */
    protected function sendReminderSms(TodRoster $roster): void
    {
        try {
            $teacher = DB::table('teachers')
                ->join('users', 'users.id', '=', 'teachers.user_id')
                ->select(
                    'teachers.school_id',
                    'users.first_name',
                    'users.last_name',
                    'users.phone'
                )
                ->where('teachers.id', $roster->teacher_id)
                ->first();

            if (!$teacher || empty($teacher->phone)) {
                return;
            }

            $startDate = Carbon::parse($roster->start_date)->format('d/m/Y');
            $fullname  = ucwords(strtolower($teacher->first_name));

            $message = "Hello {$fullname}! Kindly be reminded that your duty shift will commence tomorrow on {$startDate}. Your cooperation is highly appreciated.";

            $school = School::findOrFail($teacher->school_id);
            $sms    = new NextSmsService();

            $sms->sendSmsByNext(
                $school->sender_id,
                $this->formatPhoneNumber($teacher->phone),
                $message,
                $roster->roster_id . '_reminder'
            );
        } catch (\Exception $e) {
            $this->error('Hitilafu ya SMS ya ukumbusho: ' . $e->getMessage());
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
