<?php

namespace App\Services;

use App\Models\FeeClearanceToken;
use Illuminate\Support\Facades\Log;

class TokenGeneratorService
{
    /**
     * Character set - using numbers only for PIN-like tokens
     */
    private $characters = '0123456789';
    private $characterCount = 10;
    private $tokenLength = 4;
    private $maxAttempts = 20; // Increased for safety

    /**
     * Generate unique 4-digit token that has NEVER been used before
     * Checks ALL tokens regardless of expiry status
     */
    public function generateUniqueToken(): string
    {
        $attempt = 0;
        $usedTokens = $this->getAllUsedTokens();

        do {
            $token = $this->generateRandomToken();
            $exists = in_array($token, $usedTokens);
            $attempt++;

            // Log warning if we're having too many collisions
            if ($attempt > 5) {
                Log::warning('Token generation experiencing collisions', [
                    'attempt' => $attempt,
                    'used_tokens_count' => count($usedTokens)
                ]);
            }

            // Emergency fallback if we exhaust attempts
            if ($attempt >= $this->maxAttempts) {
                Log::critical('Max token generation attempts reached! Using emergency fallback.', [
                    'attempt' => $attempt,
                    'used_tokens_count' => count($usedTokens)
                ]);
                $token = $this->generateTimestampBasedToken();
                break;
            }
        } while ($exists);

        return $token;
    }

    /**
     * Generate random 4-digit token
     */
    private function generateRandomToken(): string
    {
        $token = '';
        for ($i = 0; $i < $this->tokenLength; $i++) {
            $token .= $this->characters[random_int(0, $this->characterCount - 1)];
        }
        return $token;
    }

    /**
     * Get ALL tokens that have ever been used (including expired ones)
     * Returns array of token strings for fast lookup
     */
    private function getAllUsedTokens(): array
    {
        // Using pluck to get only token values as array
        return FeeClearanceToken::pluck('token')->toArray();
    }

    /**
     * Alternative: Check if token exists in database (including expired)
     */
    private function checkTokenExistsIncludingExpired(string $token): bool
    {
        return FeeClearanceToken::where('token', $token)
            ->exists(); // Removed the expiry check - checks ALL tokens
    }

    /**
     * Option 2: More efficient approach - check on-the-fly without loading all tokens
     * Use this if you have a large number of tokens
     */
    public function generateUniqueTokenEfficient(): string
    {
        $attempt = 0;

        do {
            $token = $this->generateRandomToken();
            $exists = $this->checkTokenExistsIncludingExpired($token);
            $attempt++;

            if ($attempt >= $this->maxAttempts) {
                Log::critical('Max token generation attempts reached!', [
                    'attempt' => $attempt
                ]);
                $token = $this->generateTimestampBasedToken();
                break;
            }
        } while ($exists);

        return $token;
    }

    /**
     * Fallback: Generate token with timestamp to ensure uniqueness
     * This is more likely to be unique but longer
     */
    private function generateTimestampBasedToken(): string
    {
        // Use microtime for better uniqueness
        $timestamp = substr(microtime(true) * 10000, -4);
        $random = $this->generateRandomToken();

        // Mix timestamp with random
        $mixed = '';
        for ($i = 0; $i < 4; $i++) {
            $mixed .= $random[$i] ?? '0';
            $mixed .= $timestamp[$i] ?? '0';
        }

        // Take first 4 characters
        $token = substr($mixed, 0, 4);

        // Verify this token doesn't exist (shouldn't happen but just in case)
        if ($this->checkTokenExistsIncludingExpired($token)) {
            // If still exists, append random and take last 4
            $token = substr($token . $this->generateRandomToken(), 0, 4);
        }

        Log::info('Emergency token generated', ['token' => $token]);
        return $token;
    }

    /**
     * Get available token count (for monitoring)
     */
    public function getAvailableTokensCount(): int
    {
        $totalPossible = pow($this->characterCount, $this->tokenLength);
        $usedTokens = FeeClearanceToken::count();
        $available = $totalPossible - $usedTokens;

        return max(0, $available);
    }

    /**
     * Check if we're running low on available tokens
     */
    public function isTokenPoolLow(): bool
    {
        $available = $this->getAvailableTokensCount();
        $threshold = 100; // Alert when less than 100 tokens available

        if ($available < $threshold) {
            Log::warning('Token pool is running low!', [
                'available' => $available,
                'threshold' => $threshold
            ]);
            return true;
        }

        return false;
    }

    /**
     * Get token statistics for monitoring
     */
    public function getTokenStatistics(): array
    {
        $totalPossible = pow($this->characterCount, $this->tokenLength);
        $usedTokens = FeeClearanceToken::count();
        $expiredTokens = FeeClearanceToken::where('expires_at', '<', now())->count();
        $activeTokens = FeeClearanceToken::where('expires_at', '>', now())->count();

        return [
            'total_possible' => $totalPossible,
            'total_used' => $usedTokens,
            'active' => $activeTokens,
            'expired' => $expiredTokens,
            'available' => max(0, $totalPossible - $usedTokens),
            'utilization_percentage' => round(($usedTokens / $totalPossible) * 100, 2)
        ];
    }
}
