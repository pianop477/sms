<?php

namespace App\Console\Commands;

use App\Models\holiday_package;
use Carbon\Carbon;
use Illuminate\Console\Command;

class DeactivateExpiredPackages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'packages:deactivate-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deactivate holiday packages whose due date has passed';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        $today = Carbon::now()->toDateString();

        $count = holiday_package::where('is_active', true)
            ->whereDate('due_date', '<', $today)
            ->update(['is_active' => false]);

        $this->info("Deactivated {$count} expired holiday packages.");

        return 0;
    }
}
