<?php

namespace App\Console\Commands;

use App\Models\FeeClearanceToken;
use App\Services\FeeClearanceService;
use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AutoExpireTokens extends Command
{
    protected $signature = 'tokens:auto-expire
                            {--dry-run : Run without actually updating database}';

    protected $description = 'Automatically expire tokens whose expiry date has passed';

    public function handle()
    {
        $this->info('🕐 Checking for tokens to auto-expire...');
        $startTime = microtime(true);
        $dryRun = $this->option('dry-run');

        $expiredTokens = FeeClearanceToken::where('status', 'active')
            ->where('expires_at', '<', Carbon::now())
            ->get();

        $count = $expiredTokens->count();

        if ($count === 0) {
            $this->info('✅ No tokens need to be expired.');
            return 0;
        }

        $this->info("📊 Found {$count} tokens that have expired.");

        if ($dryRun) {
            $this->warn('⚠️  DRY RUN - No changes will be made');
            foreach ($expiredTokens as $token) {
                $this->line("   Would expire token: {$token->token} (expired at: {$token->expires_at})");
            }
            return 0;
        }

        // Update status to expired
        $updated = FeeClearanceToken::where('status', 'active')
            ->where('expires_at', '<', Carbon::now())
            ->update(['status' => 'expired']);

        $executionTime = round(microtime(true) - $startTime, 2);

        $this->info("✅ Expired {$updated} tokens automatically.");
        $this->info("⏱️  Completed in {$executionTime} seconds");

        return 0;
    }
}
