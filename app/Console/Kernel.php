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
        $schedule->command('contracts:update-expired-contract')->daily();
        $schedule->command('school:update-status')->daily();
        $schedule->command('results:delete-expired')->everyMinute();
        $schedule->command('students:delete-graduated-students')->daily();
        $schedule->command('delete:old-exam-results')->everyMinute();
        $schedule->command('delete:old-attendance-reports')->daily();
        $schedule->command('delete:student-old-reports')->daily();
        $schedule->command('contracts:delete-old')->daily();
        $schedule->command('logins:clean-old')->daily();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
