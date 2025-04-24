<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanOldFailedLogins extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'logins:clean-old';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete failed login attempts older than 1 month';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        $deleted = DB::table('failed_logins')
            ->where('attempted_at', '<', Carbon::now()->subWeek())
            ->delete();

        $this->info("Deleted $deleted old failed login attempt(s).");
    }
}
