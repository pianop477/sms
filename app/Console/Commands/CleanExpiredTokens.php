<?php

namespace App\Console\Commands;

use App\Models\FeeClearanceToken;
use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CleanExpiredTokens extends Command
{
    protected $signature = 'tokens:clean-expired
                            {--days=30 : Keep expired tokens for this many days before hard delete}
                            {--soft-delete : Only mark as expired, don\'t delete}';

    protected $description = 'Clean up expired fee clearance tokens';

    public function handle()
    {
        $this->info('🧹 Starting expired tokens cleanup...');
        $startTime = microtime(true);

        $softDelete = $this->option('soft-delete');
        $keepDays = (int) $this->option('days');

        // 1. Kwanza: Mark tokens that are expired (active but past expiry)
        $expiredCount = FeeClearanceToken::where('status', 'active')
            ->where('expires_at', '<', Carbon::now())
            ->update(['status' => 'expired']);

        $this->info("✅ Marked {$expiredCount} expired tokens as expired");

        if (!$softDelete) {
            // 2. Kisha: Hard delete tokens that expired more than $keepDays days ago
            $cutoffDate = Carbon::now()->subDays($keepDays);

            $deletedCount = FeeClearanceToken::where('status', 'expired')
                ->where('expires_at', '<', $cutoffDate)
                ->delete();

            $this->info("🗑️  Deleted {$deletedCount} tokens older than {$keepDays} days");
        }

        $executionTime = round(microtime(true) - $startTime, 2);
        $this->info("⏱️  Cleanup completed in {$executionTime} seconds");

        Log::channel('fee_assignment')->info('Token cleanup completed', [
            'expired_marked' => $expiredCount,
            'deleted' => $deletedCount ?? 0,
            'execution_time' => $executionTime
        ]);

        return 0;
    }
}
