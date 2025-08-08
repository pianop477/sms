<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Services\BeemSmsService;
use App\Services\NextSmsService;
use Illuminate\Support\Facades\Cache;

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

        // Generate OTP (6 digits)
        $otp = rand(10000, 99999);
        Cache::put('bio_otp_'.$user->id, $otp, now()->addMinutes(2));

        // Send OTP via SMS (implement your SMS service here)
        $nextSmsService = new NextSmsService();
        $sender_id = "SHULE APP";
        $message = "Your OTP is: $otp use it for biometric verification. Expires in 2 minutes.";
        $payload = [
            'from' => $sender_id,
            'to' => $this->formatPhoneNumber($user->phone),
            'text' => $message,
            'reference' => 'otp-'.uniqid(),
        ];

        $response = $nextSmsService->sendSmsByNext($payload['from'], $payload['to'], $payload['text'], $payload['reference']);

        $beemSmsService = new BeemSmsService();
        $sender = "shuleApp";
        $recipient_id = 1;
        $message = "Your OTP is: $otp use it for biometric verification. Expires in 2 minutes.";
        $recipients[] = [
            'recipient_id' => $recipient_id++,
            'dest_addr' => $this->formatPhoneNumber($user->phone),
        ];

        // $response = $beemSmsService->sendSms($sender, $message, $recipients);

        return response()->json([
            'success' => true,
            'phone' => substr($user->phone, -4) // Last 3 digits for display
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

        $storedOtp = Cache::get('bio_otp_'.$user->id);

        if ($request->otp != $storedOtp) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid OTP'
            ], 401);
        }

        // OTP is valid
        Cache::forget('bio_otp_'.$user->id);

        return response()->json(['success' => true]);
    }

}
