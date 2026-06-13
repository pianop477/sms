<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FeeClearanceToken;
use Carbon\Carbon;

class OfflineTokenController extends Controller
{
    public function getAllTokens()
    {
        $tokens = FeeClearanceToken::with(['student', 'student.class', 'installment'])
            ->where('status', 'active')
            ->where('expires_at', '>', now())
            ->get();

        $data = [];
        foreach ($tokens as $token) {
            $student = $token->student;
            if (!$student) continue;

            $data[] = [
                'token' => $token->token,
                'formatted_token' => substr($token->token, 0, 3) . '-' . substr($token->token, 3, 3),
                'student' => [
                    'id' => $student->id,
                    'admission_number' => $student->admission_number,
                    'first_name' => $student->first_name,
                    'last_name' => $student->last_name,
                    'full_name' => $student->first_name . ' ' . $student->last_name,
                    'class_name' => $student->class->class_name ?? 'N/A',
                    'has_transport' => !is_null($student->transport_id),
                    'image' => $student->image,
                ],
                'installment' => [
                    'name' => $token->installment->name ?? 'School Fees',
                    'order' => $token->installment->order ?? 1
                ],
                'expires_at' => Carbon::parse($token->expires_at)->toIso8601String(),
                'academic_year' => $token->academic_year,
            ];
        }

        return response()->json([
            'success' => true,
            'total' => count($data),
            'last_sync' => now()->toIso8601String(),
            'tokens' => $data
        ]);
    }
}
