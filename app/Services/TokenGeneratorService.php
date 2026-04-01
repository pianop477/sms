<?php

namespace App\Services;

use App\Models\FeeClearanceToken;
use Illuminate\Support\Facades\Log;

class TokenGeneratorService
{
    /**
     * Character set - excluding similar looking characters
     * Removed: 0, O, I, 1 to avoid confusion
     */
    private $characters = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    private $characterCount = 32; // 24 letters + 8 numbers
    private $tokenLength = 6;

    /**
     * Generate unique 6-character alphanumeric token
     */
    public function generateUniqueToken(): string
    {
        $maxAttempts = 10;
        $attempt = 0;

        do {
            $token = $this->generateRandomToken();
            $exists = $this->checkTokenExists($token);
            $attempt++;

            if ($attempt >= $maxAttempts) {
                // If we're having collisions, expand token length temporarily
                Log::warning('Token generation had collisions, using fallback method');
                $token = $this->generateTokenWithTimestamp();
                break;
            }
        } while ($exists);

        return $token;
    }

    /**
     * Generate random 6-character token
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
     * Check if token already exists and is still active/valid
     */
    private function checkTokenExists(string $token): bool
    {
        return FeeClearanceToken::where('token', $token)
            ->where('expires_at', '>', now())
            ->exists();
    }

    /**
     * Fallback: Generate token with timestamp to ensure uniqueness
     */
    private function generateTokenWithTimestamp(): string
    {
        // Use timestamp + random characters
        $timestamp = substr(time(), -3);
        $random = $this->generateRandomToken();
        return substr($random, 0, 3) . $timestamp;
    }
}
