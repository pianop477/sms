<?php

namespace App\Http\Controllers\WebAuthn;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Laragear\WebAuthn\Http\Requests\AttestedRequest;
use Illuminate\Support\Str;
use Base64Url\Base64Url;
use Laragear\WebAuthn\Models\WebAuthnCredential;

class WebAuthnRegisterController extends Controller
{
    public function registerOptions(Request $request)
    {
        $request->validate(['username' => 'required|string']);

        // 1. Tafuta user bila kuhitaji authentication
        $user = User::where('email', $request->username)
                ->orWhere('phone', $request->username)
                ->firstOrFail();

        // 2. Angalia kama tayari ana credentials
        if ($user->webauthnCredentials()->exists()) {
            return response()->json([
                'error' => 'User already has biometric credentials registered',
                'has_registered' => true
            ], 409); // HTTP 409 Conflict
        }

        // 3. Tengeneza options
        return response()->json([
            'challenge' => base64_encode(random_bytes(32)),
            'rp' => [
                'name' => config('app.name'),
                'id' => parse_url(config('app.url'), PHP_URL_HOST) ?? 'localhost'
            ],
            'user' => [
                'id' => base64_encode($user->id), // Encode user ID
                'name' => $user->email,
                'displayName' => $user->name
            ],
            'pubKeyCredParams' => [
                ['type' => 'public-key', 'alg' => -7],  // ES256
                ['type' => 'public-key', 'alg' => -257] // RS256
            ]
        ]);
    }

    public function registerVerify(Request $request)
    {
        $request->validate([
            'id' => 'required|string',
            'rawId' => 'required|string',
            'response' => 'required|array',
            'username' => 'required|string' // Ongeza username kwenye verification
        ]);

        // 1. Tafuta user kwa username/phone
        $user = User::where('email', $request->username)
                ->orWhere('phone', $request->username)
                ->firstOrFail();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], 401);
        }

        // 3. Process WebAuthn registration
        try {
            $credential = \Laragear\WebAuthn\Models\WebAuthnCredential::create([
                'id' => $request->id,
                'authenticatable_type' => 'App\Models\User',
                'authenticatable_id' => $user->id,
                'user_id' => $user->id,
                'alias' => 'Biometric Key',
                'counter' => 0,
                'rp_id' => parse_url(config('app.url'), PHP_URL_HOST) ?? 'localhost',
                'origin' => config('app.url'),
                'transports' => json_encode(['internal']),
                'aaguid' => Str::uuid(),
                'public_key' => $request->response['attestationObject'],
                'attestation_format' => 'none',
                'certificates' => json_encode([]),
                'disabled_at' => null
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Biometric registration successful'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Registration failed: '.$e->getMessage()
            ], 422);
        }
    }
}
