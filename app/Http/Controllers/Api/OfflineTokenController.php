<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FeeClearanceToken;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class OfflineTokenController extends Controller
{
    public function getAllTokens(Request $request)
    {
        $academicYear = $request->input('academic_year', date('Y'));
        $schoolId = $request->input('school_id');

        // Create a cache key based on parameters
        $cacheKey = 'offline_tokens_' . $academicYear;
        if ($schoolId) {
            $cacheKey .= '_school_' . $schoolId;
        }

        // Try to get from cache first (for performance)
        $cached = Cache::get($cacheKey);

        if ($cached) {
            return response()->json([
                'success' => true,
                'total' => $cached['total'] ?? 0,
                'last_sync' => $cached['last_sync'] ?? now()->toIso8601String(),
                'tokens' => $cached['tokens'] ?? [],
                'cached' => true,
                'academic_year' => $academicYear
            ]);
        }

        // If not cached, fetch from database
        $query = FeeClearanceToken::with(['student', 'student.class', 'installment'])
            ->where('status', 'active')
            ->where('expires_at', '>', now())
            ->where('academic_year', $academicYear);

        if ($schoolId) {
            $query->whereHas('student', function($q) use ($schoolId) {
                $q->where('school_id', $schoolId);
            });
        }

        $tokens = $query->get();

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
                'is_valid' => Carbon::parse($token->expires_at)->isFuture()
            ];
        }

        // Store in cache for 1 hour
        $cachedData = [
            'tokens' => $data,
            'total' => count($data),
            'last_sync' => now()->toIso8601String(),
        ];
        Cache::put($cacheKey, $cachedData, now()->addHours(1));

        return response()->json([
            'success' => true,
            'total' => count($data),
            'last_sync' => now()->toIso8601String(),
            'tokens' => $data,
            'cached' => false,
            'academic_year' => $academicYear
        ]);
    }
}
