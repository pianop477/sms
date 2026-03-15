<?php

namespace App\Console\Commands;

use App\Models\TodRoster;
use App\Models\school;
use App\Models\User;
use App\Models\Teacher;
use App\Services\NextSmsService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

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

        /**
         * 4. NOTIFY ACADEMIC WAKATI ROSTER ZINAKARIBIA KUISHA
         * - Angalia kwa kutumia roster_id (unique roster)
         * - Tuma notification pale tu endapo imebakia roster 1 MOJA TU (pending)
         */
        $this->checkAndNotifyLowRosters();

        $this->info('Usasishaji wa roster umekamilika.');
        return Command::SUCCESS;
    }

    /**
     * Angalia na notify academic kama roster ziko chache
     */
    protected function checkAndNotifyLowRosters(): void
    {
        try {
            // HESABU KWA KUTUMIA ROSTER_ID (DISTINCT)
            // Pata unique pending rosters (kwa kutumia roster_id)
            $pendingRostersCount = TodRoster::where('status', 'pending')
                ->where('is_completed', 0)
                ->distinct('roster_id')
                ->count('roster_id');

            // Pata unique active rosters (kwa kutumia roster_id)
            $activeRostersCount = TodRoster::where('status', 'active')
                ->where('is_completed', 0)
                ->distinct('roster_id')
                ->count('roster_id');

            // Jumla ya unique rosters zinazotarajiwa (pending + active)
            $totalUniqueRosters = $pendingRostersCount + $activeRostersCount;

            // Log kwa ajili ya debugging (kwa kutumia unique rosters)
            $this->line("Pending unique rosters: {$pendingRostersCount}, Active unique: {$activeRostersCount}, Total unique: {$totalUniqueRosters}");

            /**
             * TUMA NOTIFICATION PALE TU IMEBAKIA ROSTER 1 MOJA TU (PENDING)
             * Na hiyo roster moja iwe pending (si active)
             * Maana yake:
             * - Active rosters = 1 (inayoendelea sasa)
             * - Pending rosters = 1 (inayosubiri)
             * Hii inamaanisha jumla ya rosters zilizopo = 2
             * Lakini tunataka kumtaarifu academic kuwa imebakia roster 1 tu pending
             */

            // Kama kuna active rosters inayoendelea (should be 1 normally)
            // Na pending rosters ni 1 (moja tu imebakia)
            if ($pendingRostersCount == 1) {
                // Pata maelezo ya pending roster iliyobakia
                $pendingRoster = TodRoster::where('status', 'pending')
                    ->where('is_completed', 0)
                    ->select('roster_id', 'start_date', 'end_date')
                    ->first();

                $rosterDetails = "";
                if ($pendingRoster) {
                    $startDate = Carbon::parse($pendingRoster->start_date)->format('d/m/Y');
                    $endDate = Carbon::parse($pendingRoster->end_date)->format('d/m/Y');
                    $rosterDetails = " (Roster ID: {$pendingRoster->roster_id}, Start: {$startDate}, End: {$endDate})";
                }

                $this->notifyAcademicUsers(1, $rosterDetails);
                $this->info("NOTIFICATION: Imebakia roster 1 moja tu pending{$rosterDetails}");
            }
            // Kama hakuna roster kabisa (active = 0, pending = 0)
            elseif ($totalUniqueRosters == 0) {
                $this->notifyAcademicUsers(0, '', true); // true inaonyesha hakuna roster kabisa
                $this->info("NOTIFICATION: Hakuna roster kabisa kwenye system.");
            } else {
                $this->line("Hakuna notification: Pending rosters = {$pendingRostersCount} (tunataka 1 tu)");
            }

        } catch (\Exception $e) {
            $this->error('Hitilafu wakati wa kuangalia rosters: ' . $e->getMessage());
        }
    }

    /**
     * Tuma notification kwa academic users
     */
    protected function notifyAcademicUsers(int $pendingCount, string $rosterDetails = '', bool $isEmpty = false): void
    {
        try {
            // Pata academic users (user type = 3 na teacher role = 3)
            $academicUsers = User::join('teachers', 'users.id', '=', 'teachers.user_id')
                ->where('users.usertype', 3)
                ->where('teachers.role_id', 3)
                ->select('users.*', 'teachers.school_id')
                ->get();

            if ($academicUsers->isEmpty()) {
                $this->warn('Hakuna academic users waliopatikana.');
                return;
            }

            // Pata school information
            $schoolId = $academicUsers->first()->school_id;
            $school = school::find($schoolId);

            if (!$school) {
                $this->error('School haikupatikana.');
                return;
            }

            // Tengeneza ujumbe kulingana na hali
            if ($isEmpty) {
                $message = "Hello! This is to notify you that there are currently no pending or active rosters in the system. Please create new rosters immediately to ensure automation continues smoothly. Automation will stop if no rosters are added. Thanks.";
            } else {
                $message = "Hello! This is to notify you that there is only ONE pending roster remaining in the system{$rosterDetails}. Please consider adding more rosters to maintain System Automation. Otherwise Automation will stop if no new rosters are added. Best regards.";
            }

            $sms = new NextSmsService();

            // Tuma SMS kwa kila academic user
            foreach ($academicUsers as $user) {
                if (!empty($user->phone)) {
                    try {
                        $sms->sendSmsByNext(
                            $school->sender_id ?? 'SHULE APP',
                            $this->formatPhoneNumber($user->phone),
                            $message,
                            'roster_notification_' . time()
                        );

                        $this->info("Notification SMS imetumwa kwa {$user->first_name} {$user->last_name}");
                    } catch (\Exception $e) {
                        $this->error("Kushindwa kutuma SMS kwa {$user->phone}: " . $e->getMessage());
                    }
                }
            }

        } catch (\Exception $e) {
            $this->error('Hitilafu ya kutuma notification kwa academic: ' . $e->getMessage());
        }
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

            $message = "Hello {$fullname}, kindly be reminded that your duty shift starts tomorrow ({$startDate}). Please ensure all requirements are fulfilled on time. Your cooperation is highly appreciated.";

            $school = school::findOrFail($teacher->school_id);
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
