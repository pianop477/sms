<?php

namespace App\Console\Commands;

use App\Models\FeeClearanceToken;
use Illuminate\Console\Command;
use Carbon\Carbon;

class SyncTokensForOffline extends Command
{
    protected $signature = 'tokens:sync-offline';
    protected $description = 'Trigger background sync for offline tokens';

    public function handle()
    {
        $this->info('🔄 Triggering token sync...');

        // This command can be used to notify clients via WebPush
        // or simply log that sync is needed

        $activeTokens = FeeClearanceToken::where('status', 'active')
            ->where('expires_at', '>', now())
            ->count();

        $this->info("📊 Active tokens: {$activeTokens}");

        return 0;
    }
}
