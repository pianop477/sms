<?php

namespace App\Console\Commands;

use App\Models\contract_otp_validation;
use App\Models\ContractOtpValidation;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CleanupExpiredOtps extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'otp:cleanup-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up expired OTP validations - mark as used/expired and delete after 24 hours';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting OTP cleanup process...');

        try {
            $now = Carbon::now();

            // ===== STEP 1: Mark expired OTPs =====
            $expiredOtps = contract_otp_validation::where('expires_at', '<', $now)
                ->where('is_expired', false)
                ->where('is_used', false)
                ->whereIn('is_verified', [false, true])
                ->update([
                    'is_expired' => true,
                    'is_used' => true,
                    'updated_at' => $now
                ]);

            $this->info("Marked {$expiredOtps} OTPs as expired and used.");
            Log::info("OTP Cleanup: Marked {$expiredOtps} OTPs as expired.");

            // ===== STEP 2: Mark used OTPs (if any were missed) =====
            // This covers cases where OTP might have been used but not marked properly
            $usedOtps = contract_otp_validation::where('verified_at', '<', $now)
                ->where('is_used', false)
                ->update([
                    'is_used' => true,
                    'updated_at' => $now
                ]);

            $this->info("Marked {$usedOtps} OTPs as used.");
            Log::info("OTP Cleanup: Marked {$usedOtps} OTPs as used.");

            // ===== STEP 3: Delete OTPs older than 24 hours =====
            // Calculate threshold: 24 hours ago from now
            $threshold = $now->copy()->subHours(24);

            // Find OTPs that are either:
            // 1. Expired and older than 24 hours
            // 2. Used and older than 24 hours
            // 3. Verified and older than 24 hours
            $oldOtps = contract_otp_validation::where(function($query) use ($threshold) {
                $query->where('expires_at', '<', $threshold)
                    ->orWhere('verified_at', '<', $threshold)
                    ->orWhere('created_at', '<', $threshold);
            })->delete();

            $this->info("Deleted {$oldOtps} OTP records older than 24 hours.");
            // Log::info("OTP Cleanup: Deleted {$oldOtps} old OTP records.");

            // ===== STEP 4: Also delete any orphaned/invalid records =====
            // Optional: Delete records that are both expired and used regardless of time
            $invalidOtps = contract_otp_validation::where('is_expired', true)
                ->where('is_used', true)
                ->where('created_at', '<', $now->copy()->subHours(1)) // Give them at least 1 hour
                ->delete();

            if ($invalidOtps > 0) {
                $this->info("Deleted {$invalidOtps} invalid OTP records.");
                // Log::info("OTP Cleanup: Deleted {$invalidOtps} invalid OTP records.");
            }

            $this->info('OTP cleanup completed successfully!');

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('Error during OTP cleanup: ' . $e->getMessage());
            // Log::error('OTP Cleanup Error: ' . $e->getMessage(), [
            //     'exception' => $e
            // ]);

            return Command::FAILURE;
        }
    }
}
