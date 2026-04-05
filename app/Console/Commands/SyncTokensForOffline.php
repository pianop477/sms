<?php

namespace App\Console\Commands;

use App\Models\FeeClearanceToken;
use App\Models\Student;
use App\Models\school;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SyncTokensForOffline extends Command
{
    protected $signature = 'tokens:sync-offline
                            {--academic-year= : Academic year to sync (default: current year)}
                            {--school-id= : Sync tokens for specific school}
                            {--export= : Export tokens to file (json|csv)}
                            {--clean-expired : Clean up expired tokens}
                            {--notify : Send notifications for expiring tokens}';

    protected $description = 'Sync tokens for offline access and clean up expired tokens';

    /**
     * Get current academic year
     */
    private function getCurrentAcademicYear()
    {
        if ($this->option('academic-year')) {
            return $this->option('academic-year');
        }
        return date('Y');
    }

    public function handle()
    {
        $academicYear = $this->getCurrentAcademicYear();
        $schoolId = $this->option('school-id');
        $export = $this->option('export');
        $cleanExpired = $this->option('clean-expired');
        $notify = $this->option('notify');

        $this->info('🔄 Starting token sync for offline access...');
        $this->info("📅 Academic Year: {$academicYear}");
        $this->newLine();

        // 1. Clean expired tokens if requested
        if ($cleanExpired) {
            $this->cleanExpiredTokens();
        }

        // 2. Check tokens expiring soon
        $expiringSoon = $this->checkExpiringTokens($notify);

        // 3. Get active tokens for offline sync
        $query = FeeClearanceToken::with(['student', 'student.class', 'installment'])
            ->where('academic_year', $academicYear)
            ->where('status', 'active')
            ->where('expires_at', '>', now());

        if ($schoolId) {
            $query->whereHas('student', function($q) use ($schoolId) {
                $q->where('school_id', $schoolId);
            });
        }

        $tokens = $query->get();

        $this->info("📊 Active tokens found: {$tokens->count()}");
        $this->newLine();

        if ($tokens->isEmpty()) {
            $this->warn('⚠️  No active tokens found for the specified criteria.');
            return 0;
        }

        // 4. Prepare token data for offline sync
        $offlineData = $this->prepareOfflineData($tokens);

        // 5. Export to file if requested
        if ($export) {
            $this->exportToFile($offlineData, $export);
        }

        // 6. Store in cache for offline access
        $this->storeInCache($offlineData, $academicYear, $schoolId);

        // 7. Log sync completion
        // Log::info('Offline token sync completed', [
        //     'academic_year' => $academicYear,
        //     'school_id' => $schoolId,
        //     'total_tokens' => $tokens->count(),
        //     'expiring_soon' => $expiringSoon['count']
        // ]);

        $this->newLine();
        $this->info('✅ Token sync completed successfully!');

        if ($expiringSoon['count'] > 0) {
            $this->warn("⚠️  {$expiringSoon['count']} tokens expiring within 7 days");
        }

        return 0;
    }

    /**
     * Clean up expired tokens
     */
    private function cleanExpiredTokens()
    {
        $this->info('🧹 Cleaning expired tokens...');

        $expiredTokens = FeeClearanceToken::where('status', 'active')
            ->where('expires_at', '<', now())
            ->get();

        $count = $expiredTokens->count();

        if ($count > 0) {
            foreach ($expiredTokens as $token) {
                $token->update(['status' => 'expired']);
            }

            $this->info("   ✅ Expired tokens marked as expired: {$count}");

            // Log::info('Expired tokens cleaned', ['count' => $count]);
        } else {
            $this->info("   ✅ No expired tokens found");
        }

        $this->newLine();
    }

    /**
     * Check tokens expiring soon
     */
    private function checkExpiringTokens($notify = false)
    {
        $sevenDaysFromNow = now()->addDays(7);

        $expiringSoon = FeeClearanceToken::with(['student', 'student.parents.user'])
            ->where('status', 'active')
            ->where('expires_at', '>', now())
            ->where('expires_at', '<', $sevenDaysFromNow)
            ->get();

        $count = $expiringSoon->count();

        if ($count > 0) {
            $this->info("⚠️  Tokens expiring within 7 days: {$count}");

            if ($notify) {
                $this->sendExpiryNotifications($expiringSoon);
            }
        }

        return ['count' => $count, 'tokens' => $expiringSoon];
    }

    /**
     * Send notifications for expiring tokens
     */
    private function sendExpiryNotifications($tokens)
    {
        $this->info("📱 Sending expiry notifications...");

        foreach ($tokens as $token) {
            // Log expiry notification (can be extended to send actual SMS/Email)
            // Log::info('Token expiring soon', [
            //     'student_id' => $token->student_id,
            //     'token' => $token->token,
            //     'expires_at' => $token->expires_at,
            //     'days_left' => now()->diffInDays($token->expires_at)
            // ]);

            $this->line("   📝 Token for student ID {$token->student_id} expires on " .
                       Carbon::parse($token->expires_at)->format('d/m/Y'));
        }

        $this->info("   ✅ Notifications logged for {$tokens->count()} tokens");
    }

    /**
     * Prepare data for offline sync
     */
    private function prepareOfflineData($tokens)
    {
        $data = [];

        foreach ($tokens as $token) {
            $student = $token->student;

            if (!$student) {
                continue;
            }

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
                    'school_id' => $student->school_id
                ],
                'installment' => [
                    'name' => $token->installment->name ?? 'School Fees',
                    'order' => $token->installment->order ?? 1
                ],
                'expires_at' => Carbon::parse($token->expires_at)->toIso8601String(),
                'expires_date' => Carbon::parse($token->expires_at)->format('d/m/Y'),
                'academic_year' => $token->academic_year,
                'status' => $token->status,
                'is_valid' => Carbon::parse($token->expires_at)->isFuture()
            ];
        }

        return $data;
    }

    /**
     * Export data to file
     */
    private function exportToFile($data, $format)
    {
        $timestamp = now()->format('Ymd_His');
        $filename = "tokens_export_{$timestamp}";

        if ($format === 'json') {
            $fullPath = storage_path("app/exports/{$filename}.json");

            if (!file_exists(dirname($fullPath))) {
                mkdir(dirname($fullPath), 0755, true);
            }

            file_put_contents($fullPath, json_encode($data, JSON_PRETTY_PRINT));
            $this->info("📁 Exported to JSON: {$fullPath}");

        } elseif ($format === 'csv') {
            $fullPath = storage_path("app/exports/{$filename}.csv");

            if (!file_exists(dirname($fullPath))) {
                mkdir(dirname($fullPath), 0755, true);
            }

            $file = fopen($fullPath, 'w');

            // Add headers
            fputcsv($file, ['Token', 'Student Name', 'Admission', 'Class', 'Expires', 'Academic Year']);

            // Add data
            foreach ($data as $item) {
                fputcsv($file, [
                    $item['formatted_token'],
                    $item['student']['full_name'],
                    $item['student']['admission_number'],
                    $item['student']['class_name'],
                    $item['expires_date'],
                    $item['academic_year']
                ]);
            }

            fclose($file);
            $this->info("📁 Exported to CSV: {$fullPath}");
        }

        $this->newLine();
    }

    /**
     * Store token data in cache for offline access
     */
    private function storeInCache($data, $academicYear, $schoolId)
    {
        $cacheKey = 'offline_tokens_' . $academicYear;

        if ($schoolId) {
            $cacheKey .= '_school_' . $schoolId;
        }

        // Cache for 24 hours
        Cache::put($cacheKey, [
            'last_sync' => now()->toIso8601String(),
            'academic_year' => $academicYear,
            'total_tokens' => count($data),
            'tokens' => $data
        ], now()->addHours(24));

        $this->info("💾 Token data cached for offline access");
        $this->info("   Cache key: {$cacheKey}");

        // Also create a simplified lookup cache by token
        $tokenLookup = [];
        foreach ($data as $item) {
            $tokenLookup[$item['token']] = [
                'student_id' => $item['student']['id'],
                'student_name' => $item['student']['full_name'],
                'admission' => $item['student']['admission_number'],
                'expires_at' => $item['expires_at'],
                'is_valid' => $item['is_valid']
            ];
        }

        Cache::put('offline_token_lookup', $tokenLookup, now()->addHours(24));
    }

    /**
     * Get sync status (can be called by API)
     */
    public static function getSyncStatus($academicYear = null)
    {
        $academicYear = $academicYear ?? date('Y');
        $cacheKey = 'offline_tokens_' . $academicYear;

        $cached = Cache::get($cacheKey);

        if ($cached) {
            return [
                'status' => 'synced',
                'last_sync' => $cached['last_sync'],
                'academic_year' => $cached['academic_year'],
                'total_tokens' => $cached['total_tokens']
            ];
        }

        return [
            'status' => 'not_synced',
            'last_sync' => null,
            'academic_year' => $academicYear,
            'total_tokens' => 0
        ];
    }
}
