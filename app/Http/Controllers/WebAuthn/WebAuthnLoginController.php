<?php

namespace App\Http\Controllers\WebAuthn;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\WebAuthnCredential;
use Illuminate\Http\Request;
use Laragear\WebAuthn\Http\Requests\AssertedRequest;
use Laragear\WebAuthn\Http\Requests\AssertionRequest;

class WebAuthnLoginController extends Controller
{
    /**
     * Returns the challenge to assertion.
     */
    public function loginOptions(Request $request)
    {
        $request->validate(['username' => 'required|string']);

        $user = User::where('email', $request->username)
                   ->orWhere('phone', $request->username)
                   ->firstOrFail();

        return response()->json([
            'challenge' => base64_encode(random_bytes(32)),
            'allowCredentials' => $user->webauthnCredentials->map(function ($cred) {
                return [
                    'id' => $cred->id,
                    'type' => 'public-key'
                ];
            }),
            'userVerification' => 'required'
        ]);
    }

    /**
     * Verify the assertion and login the user.
     */
    public function loginVerify(Request $request)
    {
        $credential = WebAuthnCredential::with('user')->find($request->id);

        if (!$credential || !$credential->user) {
            return response()->json([
                'success' => false,
                'message' => 'Biometric credential or associated user not found'
            ], 404);
        }

        auth()->login($credential->user);

        return response()->json([
            'success' => true,
            'redirect' => '/home'
        ]);
    }
}
