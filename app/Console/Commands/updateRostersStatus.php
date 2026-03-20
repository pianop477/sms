<?php

namespace App\Console\Commands;

use App\Models\TodRoster;
use App\Models\User;
use App\Models\School;
use App\Models\AcademicNotification;
use App\Services\NextSmsService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
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
     * Saa za kazi (working hours)
     */
    const REMINDER_HOUR = 20; // 8:00 PM
    const BATCH_SIZE = 100; // Kwa ajili ya performance

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('========================================');
        $this->info('ROSTER UPDATE COMMAND - ' . Carbon::now()->format('Y-m-d H:i:s'));
        $this->info('========================================');

        $today = Carbon::today();
        $now = Carbon::now();

        /**
         * 1. ACTIVE → COMPLETED (end_date ikipita)
         * IMEBORESHA: Inatumia chunking kwa performance
         */
        $this->info("\n📌 STEP 1: Kuweka completed rosters...");
        $this->processCompletedRosters($today);

        /**
         * 2. REMINDER SMS (MOJA TU)
         * IMEBORESHA: Inatumia chunking na better error handling
         */
        $this->info("\n📌 STEP 2: Kutuma reminder SMS kwa walimu...");
        $this->processReminderSms($today, $now);

        /**
         * 3. PENDING → ACTIVE (SILENT)
         * IMEBORESHA: Inatumia bulk update kwa performance
         */
        $this->info("\n📌 STEP 3: Kuweka pending → active...");
        $this->processActivation($today);

        /**
         * 4. NOTIFY ACADEMIC
         * IMEBORESHA: Inatumia tracking na duplicate prevention
         */
        $this->info("\n📌 STEP 4: Kuangalia na notify academic...");
        $this->checkAndNotifyLowRosters();

        $this->info("\n========================================");
        $this->info('✅ USASAJISHAJI UMekamilika!');
        $this->info('========================================');

        return Command::SUCCESS;
    }

    /**
     * PROCESS COMPLETED ROSTERS (Iliyoboreshwa)
     */
    protected function processCompletedRosters(Carbon $today): void
    {
        try {
            $completedCount = 0;

            TodRoster::where('status', 'active')
                ->whereNotNull('end_date')
                ->whereDate('end_date', '<', $today)
                ->chunk(self::BATCH_SIZE, function ($rosters) use (&$completedCount) {
                    foreach ($rosters as $roster) {
                        $roster->update([
                            'status' => 'completed',
                            'is_completed' => true,
                            'updated_by' => 'system',
                            'updated_at' => Carbon::now()
                        ]);
                        $completedCount++;
                        $this->line("  ✓ Roster {$roster->roster_id} imewekwa COMPLETED.");
                    }
                });

            $this->info("  ✅ Jumla ya rosters {$completedCount} zimewekwa completed.");
        } catch (\Exception $e) {
            $this->error('  ❌ Hitilafu kwenye completed rosters: ' . $e->getMessage());
            Log::error('Roster completion error: ' . $e->getMessage());
        }
    }

    /**
     * PROCESS REMINDER SMS (Iliyoboreshwa)
     */
    protected function processReminderSms(Carbon $today, Carbon $now): void
    {
        try {
            // Angalia kama ni saa ya kutuma reminder (baada ya 20:00)
            if ($now->hour < self::REMINDER_HOUR) {
                $this->line("  ⏰ Sio saa ya kutuma reminder (subiri baada ya saa " . self::REMINDER_HOUR . ":00)");
                return;
            }

            $reminderCount = 0;
            $tomorrow = $today->copy()->addDay();

            // Pata rosters zinazoanza kesho
            TodRoster::where('status', 'pending')
                ->where('reminder_sent', 0)
                ->whereDate('start_date', $tomorrow)
                ->chunk(self::BATCH_SIZE, function ($rosters) use (&$reminderCount) {
                    foreach ($rosters as $roster) {
                        $success = $this->sendReminderSms($roster);

                        if ($success) {
                            $roster->update([
                                'reminder_sent' => 1,
                                'updated_by' => 'system',
                                'updated_at' => Carbon::now()
                            ]);
                            $reminderCount++;
                            $this->line("  ✓ Reminder imetumwa kwa roster {$roster->roster_id}");
                        }
                    }
                });

            $this->info("  ✅ Jumla ya reminder {$reminderCount} zimetumwa.");
        } catch (\Exception $e) {
            $this->error('  ❌ Hitilafu kwenye reminder SMS: ' . $e->getMessage());
            Log::error('Reminder SMS error: ' . $e->getMessage());
        }
    }

    /**
     * PROCESS ACTIVATION (Iliyoboreshwa)
     */
    protected function processActivation(Carbon $today): void
    {
        try {
            // Skip weekend
            if ($today->isWeekend()) {
                $this->info("  ⏸️ Weekend: Hakuna activation (siku ya wiki tu)");
                return;
            }

            $activationCount = 0;

            // Pata rosters zinazoanza leo
            TodRoster::where('status', 'pending')
                ->whereDate('start_date', $today)
                ->chunk(self::BATCH_SIZE, function ($rosters) use (&$activationCount) {
                    foreach ($rosters as $roster) {
                        // Hakikisha hakuna active roster kwa teacher huyu
                        $hasActive = TodRoster::where('teacher_id', $roster->teacher_id)
                            ->where('status', 'active')
                            ->exists();

                        if (!$hasActive) {
                            $roster->update([
                                'status' => 'active',
                                'updated_by' => 'system',
                                'updated_at' => Carbon::now()
                            ]);
                            $activationCount++;
                            $this->line("  ✓ Roster {$roster->roster_id} imewekwa ACTIVE.");
                        }
                    }
                });

            $this->info("  ✅ Jumla ya rosters {$activationCount} zimewekwa active.");
        } catch (\Exception $e) {
            $this->error('  ❌ Hitilafu kwenye activation: ' . $e->getMessage());
            Log::error('Roster activation error: ' . $e->getMessage());
        }
    }

    /**
     * ANGANIA NA NOTIFY ACADEMIC (Iliyoboreshwa kabisa)
     */
    protected function checkAndNotifyLowRosters(): void
    {
        try {
            // HESABU KWA USAHIHI
            $pendingCount = TodRoster::where('status', 'pending')
                ->where('is_completed', 0)
                ->distinct('roster_id')
                ->count('roster_id');

            $activeCount = TodRoster::where('status', 'active')
                ->where('is_completed', 0)
                ->distinct('roster_id')
                ->count('roster_id');

            $totalRosters = $pendingCount + $activeCount;

            $this->line("  📊 Pending: {$pendingCount}, Active: {$activeCount}, Total: {$totalRosters}");

            $today = Carbon::today()->format('Y-m-d');

            // ========== KESI 1: IMEBAKIA 1 TU (na active ≥ 1) ==========
            if ($pendingCount == 1 && $activeCount >= 1) {

                $uniqueKey = "low_roster_{$today}";

                // ANGA KAMA TUMESHA TUMA LEO
                $alreadySent = AcademicNotification::where('unique_key', $uniqueKey)->exists();

                if (!$alreadySent) {
                    // Pata maelezo ya roster iliyobakia
                    $pendingRoster = TodRoster::where('status', 'pending')
                        ->where('is_completed', 0)
                        ->select('roster_id', 'start_date', 'end_date')
                        ->first();

                    $rosterDetails = "";
                    if ($pendingRoster) {
                        $startDate = Carbon::parse($pendingRoster->start_date)->format('d/m/Y');
                        $endDate = Carbon::parse($pendingRoster->end_date)->format('d/m/Y');
                        // $rosterDetails = " (ID: {$pendingRoster->roster_id}, Tarehe: {$startDate} - {$endDate})";
                    }

                    // TUMA NOTIFICATION
                    $sent = $this->notifyAcademicUsers('low_roster', $rosterDetails);

                    if ($sent) {
                        // RECORD KWAMBA TUMESHA TUMA
                        AcademicNotification::create([
                            'notification_type' => 'low_roster',
                            'pending_count' => $pendingCount,
                            'active_count' => $activeCount,
                            'sent_at' => Carbon::now(),
                            'notification_date' => Carbon::today(),
                            'unique_key' => $uniqueKey
                        ]);

                        $this->info("  ✅ NOTIFICATION: Imebakia roster 1 moja tu pending{$rosterDetails}");
                    }
                } else {
                    $this->line("  ℹ️ Notification ya low roster tayari imetumwa leo.");
                }
            }

            // ========== KESI 2: HAKUNA ROSTER KABISA ==========
            elseif ($totalRosters == 0) {

                $uniqueKey = "no_roster_{$today}";

                $alreadySent = AcademicNotification::where('unique_key', $uniqueKey)->exists();

                if (!$alreadySent) {
                    $sent = $this->notifyAcademicUsers('no_roster', '', true);

                    if ($sent) {
                        AcademicNotification::create([
                            'notification_type' => 'no_roster',
                            'pending_count' => 0,
                            'active_count' => 0,
                            'sent_at' => Carbon::now(),
                            'notification_date' => Carbon::today(),
                            'unique_key' => $uniqueKey
                        ]);

                        $this->info("  ✅ NOTIFICATION: Hakuna roster kabisa kwenye system.");
                    }
                } else {
                    $this->line("  ℹ️ Notification ya no roster tayari imetumwa leo.");
                }
            }

            // ========== KESI 3: ZAIDI YA 1 - RESET ==========
            elseif ($pendingCount > 1) {
                // Futa notifications za leo kwa low_roster
                $deleted = AcademicNotification::whereDate('sent_at', Carbon::today())
                    ->where('notification_type', 'low_roster')
                    ->delete();

                if ($deleted > 0) {
                    $this->line("  🔄 Reset: Pending rosters ni {$pendingCount} (>1), notification cleared kwa leo.");
                }
            }
        } catch (\Exception $e) {
            $this->error('  ❌ Hitilafu: ' . $e->getMessage());
            Log::error('Roster notification error: ' . $e->getMessage());
        }
    }

    /**
     * TUMA NOTIFICATION KWA ACADEMIC (Iliyoboreshwa)
     */
    protected function notifyAcademicUsers(string $type, string $rosterDetails = '', bool $isEmpty = false): bool
    {
        try {
            // MLINZI WA CACHE
            $cacheKey = 'academic_notify_' . $type . '_' . Carbon::today()->format('Y-m-d');
            if (Cache::has($cacheKey)) {
                $this->warn("  ⚠️ Cache check: Notification ya {$type} imeshatumwa leo.");
                return false;
            }

            // PATA ACADEMIC USERS
            $academicUsers = User::join('teachers', 'users.id', '=', 'teachers.user_id')
                ->where('users.usertype', 3)
                ->where('teachers.role_id', 3)
                ->select('users.*', 'teachers.school_id')
                ->get();

            if ($academicUsers->isEmpty()) {
                $this->warn('  ⚠️ Hakuna academic users.');
                return false;
            }

            // PATA SCHOOL
            $schoolId = $academicUsers->first()->school_id;
            $school = School::find($schoolId);

            if (!$school) {
                $this->error('  ❌ School haikupatikana.');
                return false;
            }

            // FILTER NAMBA HALALI
            $validUsers = $academicUsers->filter(function ($user) {
                $phone = $this->formatPhoneNumber($user->phone ?? '');
                return !empty($phone) && strlen($phone) >= 10;
            });

            if ($validUsers->isEmpty()) {
                $this->warn('  ⚠️ Hakuna namba halali za simu.');
                return false;
            }

            // TENGENEZA UJUMBE
            if ($type == 'no_roster') {
                $message = "URGENT: No pending or active rosters found in the system. Please create new rosters immediately to maintain automation. Automation will stop if no rosters are added. Thanks.";
            } else {
                $message = "REMINDER: Only ONE pending roster remaining in the system{$rosterDetails}. Please add more rosters to ensure continuous automation. Otherwise automation will stop when this roster ends. Best regards.";
            }

            $sms = new NextSmsService();
            $successCount = 0;

            // TUMA SMS
            foreach ($validUsers as $user) {
                try {
                    $formattedPhone = $this->formatPhoneNumber($user->phone);

                    // THIBITISHA NAMBA
                    if (!$this->validatePhoneNumber($formattedPhone)) {
                        $this->warn("  ⚠️ Namba si sahihi kwa {$user->first_name}: {$user->phone}");
                        continue;
                    }

                    $response = $sms->sendSmsByNext(
                        $school->sender_id ?? 'SHULE APP',
                        $formattedPhone,
                        $message,
                        'academic_' . $type . '_' . time() . '_' . $user->id
                    );

                    $this->line("  ✓ SMS imetumwa kwa {$user->first_name} ({$formattedPhone})");
                    $successCount++;

                    // SUBIRI KIDOGO (kuepuka rate limiting)
                    if ($successCount % 3 == 0) {
                        usleep(500000); // 0.5 seconds
                    }
                } catch (\Exception $e) {
                    $this->error("  ❌ Kushindwa kutuma SMS kwa {$user->phone}: " . $e->getMessage());
                    Log::error('Academic SMS failed', [
                        'user' => $user->id,
                        'phone' => $user->phone,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            // WEKA CACHE
            if ($successCount > 0) {
                Cache::put($cacheKey, true, now()->endOfDay());
                $this->info("  ✅ Notification ya {$type}: SMS {$successCount} zimetumwa.");
                return true;
            }

            return false;
        } catch (\Exception $e) {
            $this->error('  ❌ Hitilafu ya kutuma notification: ' . $e->getMessage());
            Log::error('Academic notification fatal: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * SMS YA UKUMBUSHO KWA MWALIMU (Iliyoboreshwa)
     */
    protected function sendReminderSms(TodRoster $roster): bool
    {
        try {
            // PATA MWALIMU
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
                $this->warn("  ⚠️ Mwalimu hapatikani kwa roster {$roster->roster_id}");
                return false;
            }

            // PATA SHULE
            $school = School::find($teacher->school_id);
            if (!$school) {
                $this->error("  ❌ Shule haipatikani kwa roster {$roster->roster_id}");
                return false;
            }

            // FORMAT NAMBA
            $formattedPhone = $this->formatPhoneNumber($teacher->phone);
            if (!$this->validatePhoneNumber($formattedPhone)) {
                $this->warn("  ⚠️ Namba si sahihi kwa {$teacher->first_name}: {$teacher->phone}");
                return false;
            }

            // TENGENEZA UJUMBE
            $startDate = Carbon::parse($roster->start_date)->format('d/m/Y');
            $fullname = ucwords(strtolower($teacher->first_name));

            $message = "Hello {$fullname}, kindly be reminded that your duty shift starts tomorrow ({$startDate}). Please ensure all requirements are fulfilled on time. Your cooperation is highly appreciated.";

            // TUMA SMS
            $sms = new NextSmsService();
            $response = $sms->sendSmsByNext(
                $school->sender_id ?? 'SHULE APP',
                $formattedPhone,
                $message,
                $roster->roster_id . '_reminder_' . time()
            );

            return true;
        } catch (\Exception $e) {
            $this->error('  ❌ Hitilafu ya SMS ya ukumbusho: ' . $e->getMessage());
            Log::error('Reminder SMS error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * FORMAT NAMBA YA SIMU (Iliyoboreshwa)
     */
    private function formatPhoneNumber(?string $phone): string
    {
        if (empty($phone)) {
            return '';
        }

        // Ondoa spaces na special characters
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Kama inaanza na 0, badilisha na 255
        if (substr($phone, 0, 1) === '0') {
            return '255' . substr($phone, 1);
        }

        // Kama inaanza na 255, acha hivyo
        if (substr($phone, 0, 3) === '255') {
            return $phone;
        }

        // Kama ni namba fupi ya Tanzania (9 digits)
        if (strlen($phone) == 9) {
            return '255' . $phone;
        }

        // Kama ni namba ya kawaida (10-12 digits)
        if (strlen($phone) >= 10 && strlen($phone) <= 12) {
            if (substr($phone, 0, 1) === '0') {
                return '255' . substr($phone, 1);
            } elseif (substr($phone, 0, 3) !== '255') {
                return '255' . $phone;
            }
            return $phone;
        }

        return $phone;
    }

    /**
     * THIBITISHA NAMBA YA SIMU (mpya)
     */
    private function validatePhoneNumber(string $phone): bool
    {
        if (empty($phone)) {
            return false;
        }

        // Tanzania namba: 255 + 9 digits
        if (strlen($phone) == 12 && substr($phone, 0, 3) === '255') {
            return true;
        }

        return false;
    }
}
