<?php

namespace App\Http\Controllers;

use App\Models\otps;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\WebAuthnCredential;
use App\Services\BeemSmsService;
use App\Services\NextSmsService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

class BiometricController extends Controller
{
    public function sendOtp(Request $request)
    {
        $request->validate(['username' => 'required']);

        // Global IP rate limit
        if (RateLimiter::tooManyAttempts('otp-ip:' . $request->ip(), 5)) {
            return response()->json([
                'success' => false,
                'message' => 'Too many requests. Please try again later.'
            ], 429);
        }

        RateLimiter::hit('otp-ip:' . $request->ip(), 60);

        $user = User::where('email', $request->username)
            ->orWhere('phone', $request->username)
            ->first();

        // Generic response to prevent enumeration
        if (!$user) {
            return response()->json([
                'success' => true,
                'message' => 'OTP has been sent successfully.'
            ]);
        }

        // Check OTP lock
        $lockedOtp = otps::where('user_id', $user->id)
                    ->where('locked_until', '>', now())
                    ->latest()
                    ->first();

        if ($lockedOtp) {
            return response()->json([
                'success' => false,
                'message' => 'OTP temporarily locked. Please try again later.'
            ], 429);
        }

        // Biometric registration limit
        if (WebAuthnCredential::where('user_id', $user->id)->count() >= 3) {
            return response()->json([
                'success' => false,
                'message' => 'Biometric registration limit reached.'
            ], 429);
        }

        // Cooldown per user (60s)
        $recentOtp = otps::where('user_id', $user->id)
            ->where('created_at', '>', now()->subSeconds(60))
            ->first();

        if ($recentOtp) {
            return response()->json([
                'success' => false,
                'message' => 'Please wait before requesting another OTP.'
            ], 429);
        }

        // Expire old OTPs
        otps::where('user_id', $user->id)
            ->where('used', false)
            ->update(['expires_at' => now()]);

        // Generate OTP
        $otp = random_int(10000, 99999);

        otps::create([
            'user_id'      => $user->id,
            'otp'          => Hash::make($otp),
            'expires_at'   => now()->addMinutes(2),
            'used'         => false,
            'attempts'     => 0,
            'locked_until' => null,
            'ip_address'   => $request->ip(),
            'user_agent'   => substr($request->userAgent(), 0, 1000),
        ]);

        // Send SMS
        $message = "Your biometric verification OTP is: $otp. Expires in 2 minutes. Do not share this code.";
        (new NextSmsService())->sendSmsByNext(
            'SHULE APP',
            $this->formatPhoneNumber($user->phone),
            $message,
            'otp-' . uniqid()
        );

        Log::info('OTP sent for biometric registration', [
            'user_id' => $user->id,
            'ip'      => $request->ip()
        ]);

        return response()->json([
            'success' => true,
            'phone'   => substr($user->phone, -3)
        ]);
    }



    private function formatPhoneNumber($phone)
    {
        // Remove any non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Ensure the number starts with the country code (e.g., 255 for Tanzania)
        if (strlen($phone) == 9) {
            $phone = '255' . $phone;
        } elseif (strlen($phone) == 10 && substr($phone, 0, 1) == '0') {
            $phone = '255' . substr($phone, 1);
        }

        return $phone;
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'otp' => 'required|digits:5'
        ]);

        $user = User::where('email', $request->username)
                ->orWhere('phone', $request->username)
                ->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        // Tafuta OTP ya hivi karibuni isiyokuwa used na bado haija-expire
        $otpRecord = otps::where('user_id', $user->id)
                    ->where('used', false)
                    ->where('expires_at', '>', now())
                    ->latest() // tunachukua ya mwisho iliyotengenezwa
                    ->first();

        if (!$otpRecord || !Hash::check($request->otp, $otpRecord->otp)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired OTP'
            ], 401);
        }

        // Mark OTP kama imetumika
        $otpRecord->update(['used' => true]);

        return response()->json([
            'success' => true,
            'message' => 'OTP verified successfully'
        ]);
    }

    public function deleteCredentials(Request $request)
    {
        $user = User::where('email', $request->username)
                    ->orWhere('phone', $request->username)
                    ->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        // Futa credentials za webauthn
        $user->webauthnCredentials()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Biometric credentials deleted successfully'
        ]);
    }

    public function lockedOtps()
    {
        $lockedOtps = otps::with('user:id,name,email,phone')
            ->whereNotNull('locked_until')
            ->where('locked_until', '>', now())
            ->orderBy('locked_until', 'desc')
            ->get();

        return view('Schools.locked-otp', compact('lockedOtps'));
    }

}
