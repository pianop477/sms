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

class BiometricController extends Controller
{
    public function sendOtp(Request $request)
    {
        $request->validate(['username' => 'required']);

        $user = User::where('email', $request->username)
                ->orWhere('phone', $request->username)
                ->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        $bio_exist = WebAuthnCredential::where('user_id', $user->id)->count();
        if($bio_exist >= 3) {
            return response()->json([
                'success' => false,
                'message' => 'You have already reached the maximum number of biometric registrations.'
            ], 429);
        }

        // Generate OTP (6 digits)
        $otp = rand(10000, 99999);

        // Delete/expire old OTPs
        otps::where('user_id', $user->id)
            ->where('used', false)
            ->where('expires_at', '>', now())
            ->update(['expires_at' => now()]); // mark as expired

        // Create new OTP
        otps::create([
            'user_id' => $user->id,
            'otp' => Hash::make($otp),
            'expires_at' => now()->addMinutes(5),
            'used' => false,
        ]);

        // Send SMS (mfano NextSmsService)
        $message = "Your biometric verification OTP is: $otp. Expires in 5 minutes. Do not share this code with anyone.";
        $nextSmsService = new NextSmsService();
        $response = $nextSmsService->sendSmsByNext(
            "SHULE APP",
            $this->formatPhoneNumber($user->phone),
            $message,
            'otp-'.uniqid()
        );

        if(!$response['success']) {
            Alert()->toast('SMS failed: '.$response['error'], 'error');
            return back();
        }

        $beemSmsService = new BeemSmsService();
        $sender = "shuleApp";
        $recipient_id = 1;
        $recipients[] = [
            'recipient_id' => $recipient_id++,
            'dest_addr' => $this->formatPhoneNumber($user->phone),
        ];

        // $response = $beemSmsService->sendSms($sender, $message, $recipients);

        return response()->json([
            'success' => true,
            'phone' => substr($user->phone, -3)
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

}
