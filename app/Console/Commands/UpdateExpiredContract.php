<?php

namespace App\Console\Commands;

use App\Models\Contract;
use Carbon\Carbon;
use Illuminate\Console\Command;

class UpdateExpiredContract extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-expired-contract';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update the status of the expired contracts';

    /**
     * Execute the console command.
     */

     public function __construct()
     {
        parent::__construct();
     }

    public function handle()
    {
        //
        $now = Carbon::now();
        $expiredContract = Contract::where('status', 'approved')
                                    ->where('end_date', '<', $now)
                                    ->update(['status', 'expired']);
        $this->info("Expired contract Updated: {$expiredContract}");
    }
}
