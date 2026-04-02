<?php

namespace App\Console\Commands;

use App\Models\Student;
use App\Services\FeeClearanceService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SyncTokensAfterCorrection extends Command
{
    protected $signature = 'tokens:sync-after-correction
                            {--student-id= : Sync specific student only}
                            {--dry-run : Run without making changes}';

    protected $description = 'Sync all tokens after payment corrections';

    public function handle()
    {
        $this->info('🔄 Syncing tokens after payment corrections...');

        $query = Student::whereHas('payments');

        if ($this->option('student-id')) {
            $query->where('id', $this->option('student-id'));
        }

        $students = $query->get();
        $dryRun = $this->option('dry-run');
        $service = new FeeClearanceService();

        $stats = [
            'processed' => 0,
            'updated' => 0,
            'expired' => 0,
            'created' => 0,
            'errors' => 0
        ];

        foreach ($students as $student) {
            $stats['processed']++;

            try {
                if ($dryRun) {
                    $this->line("Would sync token for: {$student->first_name} {$student->last_name}");
                    continue;
                }

                $result = $service->syncTokenAfterPaymentCorrection($student);

                if ($result === null) {
                    $stats['expired']++;
                } elseif ($result->wasRecentlyCreated) {
                    $stats['created']++;
                } else {
                    $stats['updated']++;
                }
            } catch (\Exception $e) {
                $stats['errors']++;
                Log::error('Token sync failed', [
                    'student_id' => $student->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        $this->info('📈 ========== SUMMARY ==========');
        $this->info("✅ Processed: {$stats['processed']}");
        $this->info("✅ Tokens updated: {$stats['updated']}");
        $this->info("✅ Tokens expired: {$stats['expired']}");
        $this->info("✅ New tokens created: {$stats['created']}");
        $this->info("❌ Errors: {$stats['errors']}");

        return 0;
    }
}
