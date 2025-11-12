<?php

namespace App\Http\Controllers\WebAuthn;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\WebAuthnCredential;
use App\Services\FinanceTokenService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
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

        $user = $credential->user;

        // Authenticate user into the app
        auth()->login($user);

        // Only proceed with Finance API token if usertype == 5
        if ($user->usertype == 5) {
            try {
                $tokenService = new FinanceTokenService();

                $debugUrl = $tokenService->debugConnection();

                 $token = $tokenService->ensureValidToken();

                if (!$token) {
                    $sessionId = Session::getId();
                    DB::table('sessions')->where('id', $sessionId)->delete();
                    Auth::logout();
                    Session::invalidate();
                    Log::error("Finance token acquisition failed for user: {$user->id}");
                    return to_route('login')->with('error', 'Unable to connect to finance service. Please try again.');
                }

                Log::info("Finance API token acquired for user: {$user->id}");
            } catch (Exception $e) {
                return response()->json([
                    'status' => false,
                    'message' => $e->getMessage()
                ], 400);
            }
        } else {
            // Optional: log or notify that usertype != 5 is skipping API token
            // Log::info("Biometric login: usertype {$user->usertype} skipping Finance API token");
        }

        return response()->json([
            'success' => true,
            'redirect' => route('home'),
        ], 200);
    }

}
