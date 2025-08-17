<?php

namespace App\Console\Commands;

use App\Models\otps;
use Illuminate\Console\Command;

class CleanExpiredOtps extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'opt:clean-expired-otps';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        otps::where('expires_at', '<', now())->delete();
        $this->info('Expired OTPs cleaned up successfully.');
    }
}
