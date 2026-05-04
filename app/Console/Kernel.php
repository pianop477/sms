<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();
        $schedule->command('contracts:manage')
            ->dailyAt('00:00')
            ->withoutOverlapping()
            ->sendOutputTo(storage_path('logs/contract-scheduler.log'));

        // Also run every hour for more frequent updates (optional)
        $schedule->command('contracts:manage')
            ->hourly()
            ->withoutOverlapping()
            ->runInBackground();
        $schedule->command('otp:cleanup-expired')
            ->hourly()
            ->withoutOverlapping()
            ->runInBackground();
        $schedule->command('school:check-active-school')->daily();
        $schedule->command('results:delete-expired')->everyMinute();
        // $schedule->command('students:delete-graduated-students')->daily();
        $schedule->command('delete:old-exam-results')->daily();
        $schedule->command('delete:old-attendance-reports')->daily();
        $schedule->command('delete:student-old-reports')->daily();
        $schedule->command('contracts:delete-old')->daily();
        $schedule->command('logins:clean-old')->daily();
        $schedule->command('security:blocked-user-agents')->everyMinute();
        $schedule->command('cleanup:old-reports')->daily();
        $schedule->command('packages:deactivate-expired')->daily();
        $schedule->command('otp:clean-expired-otps')->daily();

        $schedule->command('roster:update-rosters-status')
            ->dailyAt('00:01')
            ->description('Activate today\'s rosters at midnight');

        // 2. Reminders za kesho - KILA SIKU saa 20:00 (jioni)
        $schedule->command('roster:update-rosters-status')
            ->dailyAt('20:00')
            ->description('Send tomorrow reminders at 8PM');
        $schedule->command('parents:truncate-inactive-parents')->daily();
        $schedule->command('bills:update-statuses')->everySecond();
        $schedule->command('reminders:service-expiry')
            ->dailyAt('08:00')
            ->withoutOverlapping();

        $schedule->command('students:assign-fee-structure --force --chunk=100')
            ->everyFifteenMinutes()
            ->withoutOverlapping();

        $schedule->command('tokens:send-existing --chunk=100')
            ->hourly()
            ->withoutOverlapping();

        $schedule->command('tokens:sync-offline')
            ->everyThirtyMinutes()
            ->withoutOverlapping();

        // ========== PWA VERSION UPDATE ==========
        // Update PWA version weekly on Sunday at 2 AM
        $schedule->command('pwa:version')
            ->dailyAt('02:00')
            ->withoutOverlapping();

        // Add to schedule() method
        $schedule->command('tokens:auto-expire')
            ->dailyAt('02:30')  // Run daily at 12:30 AM
            ->withoutOverlapping();

        // Cleanup old tokens (older than 365 days / 1 year)
        $schedule->command('tokens:clean-expired --days=365')
            ->weekly()
            ->mondays()
            ->at('01:00')
            ->withoutOverlapping();

        // Sync tokens after payment corrections - run every hour
        $schedule->command('tokens:sync-after-correction')
            ->everyMinute()
            ->withoutOverlapping();

        $schedule->command('e-permit:auto-approve')
            ->everyFiveMinutes()
            ->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
