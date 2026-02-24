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
        $credential_exsts = WebAuthnCredential::where('user_id', $user->id)->count();
        if ($credential_exsts >= 3) {
            return response()->json([
                'error' => 'Maximum number of devices has been reached for biometric registration.',
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
                'displayName' => $user->first_name
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
            'username' => 'required|string'
        ]);

        // Tafuta user
        $user = User::where('email', $request->username)
            ->orWhere('phone', $request->username)
            ->firstOrFail();

        // Angalia kama credential hii tayari ipo (kwa device hii mahususi)
        $existingCredential = WebAuthnCredential::where('id', $request->id)->first();

        if ($existingCredential) {
            return response()->json([
                'success' => false,
                'message' => 'This device is already registered'
            ], 409);
        }

        // Angalia kama user amefikia kikomo cha devices (3)
        $userDevices = WebAuthnCredential::where('user_id', $user->id)->count();
        if ($userDevices >= 3) {
            return response()->json([
                'success' => false,
                'message' => 'Maximum number of devices (3) reached for this user'
            ], 409);
        }

        // Process WebAuthn registration
        try {
            // Hakikisha unatumia unique ID kutoka kwenye request
            // 'id' inapaswa kuwa unique kwa kila device
            $credential = WebAuthnCredential::create([
                'id' => $request->id, // Hii inapaswa kuwa unique kwa kila device
                'authenticatable_type' => 'App\Models\User',
                'authenticatable_id' => $user->id,
                'user_id' => $user->id,
                'alias' => 'Biometric Key ' . ($userDevices + 1),
                'counter' => 0,
                'rp_id' => parse_url(config('app.url'), PHP_URL_HOST) ?? 'localhost',
                'origin' => config('app.url'),
                'transports' => json_encode(['internal', 'hybrid', 'usb']),
                'aaguid' => Str::uuid(),
                'public_key' => $request->response['attestationObject'] ?? '',
                'attestation_format' => $request->response['attestationObject'] ? 'fido-u2f' : 'none',
                'certificates' => json_encode([]),
                'disabled_at' => null
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Biometric registration successful',
                'devices_count' => $userDevices + 1,
                'remaining_slots' => 3 - ($userDevices + 1)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Registration failed: ' . $e->getMessage()
            ], 422);
        }
    }
}
