<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class FinanceTokenService
{
    public function ensureValidToken()
    {
        if (!Auth::check() || Auth::user()->usertype != 5) {
            return null; // Or throw exception
        }

        $token = Session::get('finance_api_token');
        $expiresAt = Session::get('finance_token_expires_at');
        $lastCheck = Session::get('finance_token_last_check');


        if ($lastCheck && now()->diffInSeconds($lastCheck) < 10) {
            return Session::get('finance_api_token');
        }
        Session::put('finance_token_last_check', now());

        // Check if token exists and is still valid for at least 2 minutes
        if ($token && $expiresAt && now()->addSeconds(30)->lt($expiresAt)) {
            return $token; // token itatumika ikiwa imebaki sekunde 30 au zaidi kabla i-expire
        }

        // Try to refresh token if we have an existing one
        if ($token && $expiresAt && now()->lt($expiresAt)) {
            $newToken = $this->refreshToken($token);
            if ($newToken) {
                return $newToken;
            }
        }

        // Get completely new token
        return $this->getNewToken();
    }

    private function refreshToken($oldToken)
    {
        try {
            Log::info("Attempting to refresh finance token");

            $response = Http::timeout(15)
                ->retry(2, 100)
                ->withToken($oldToken)
                ->post(config('app.finance_api_base_url') . '/auth/refresh');

            if ($response->successful()) {
                $tokenData = $response->json();

                if (isset($tokenData['status']) && $tokenData['status'] === true) {
                    $this->storeToken($tokenData);
                    Log::info("Finance token refreshed successfully");
                    return $tokenData['token'];
                }
            }

            Log::warning("Token refresh failed", ['response' => $response->json()]);
            return null;

        } catch (\Throwable $e) {
            Log::error("Token refresh error: " . $e->getMessage());
            return null;
        }
    }

    private function getNewToken()
    {
        try {
            Log::info("Requesting new finance token");

            $tokenUrl = rtrim(config('app.finance_api_base_url'). '/auth/token');
            // $tokenUrl = $baseUrl . '/api/v1.0/auth/token';

            Log::info("Making request to:", ['url' => $tokenUrl]);

            $response = Http::timeout(30)
                ->retry(3, 100)
                ->post($tokenUrl, [  // ← Tumia variable hapa
                    'client_key' => config('app.finance_api_client_key'),
                    'client_secret' => config('app.finance_api_client_secret'),
                ]);

            Log::info("Response status:", ['status' => $response->status()]);

            if ($response->successful()) {
                $tokenData = $response->json();
                Log::info("Token response:", $tokenData);

                if (isset($tokenData['status']) && $tokenData['status'] === true) {
                    $this->storeToken($tokenData);
                    Log::info("New finance token acquired successfully");
                    return $tokenData['token'];
                }
            } else {
                Log::error("Failed to get new token", [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'headers' => $response->headers()
                ]);
            }

            return null;

        } catch (\Throwable $e) {
            Log::error("Finance API connection failed: " . $e->getMessage());
            return null;
        }
    }

    private function storeToken($tokenData)
    {
        $expiresIn = is_numeric($tokenData['expires_in'] ?? null)
                    ? (int) $tokenData['expires_in']
                    : 3600;

        Session::put([
            'finance_api_token' => $tokenData['token'],
            'finance_token_expires_at' => now()->addSeconds($expiresIn),
            'finance_refresh_attempted' => false,
        ]);
    }

    public function getCurrentToken()
    {
        return Session::get('finance_api_token');
    }

    public function clearToken()
    {
        Session::forget([
            'finance_api_token',
            'finance_token_expires_at',
            'finance_refresh_attempted'
        ]);
    }

    public function debugConnection()
    {
        $fullUrl = rtrim(config('app.finance_api_base_url'). '/auth/token');
        // $fullUrl = $baseUrl . '/api/v1.0/auth/token';

        Log::info("Finance API Debug Info:", [
            'base_url' => config('app.finance_api_base_url'),
            'full_token_url' => $fullUrl,
            'client_key_exists' => !empty(config('app.finance_api_client_key')),
            'client_secret_exists' => !empty(config('app.finance_api_client_secret')),
        ]);

        return $fullUrl;
    }
}
