<?php
// app/Console/Commands/AutoApproveEPermits.php

namespace App\Console\Commands;

use App\Models\EPermit;
use App\Models\Teacher;
use App\Services\EPermitService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AutoApproveEPermits extends Command
{
    protected $signature = 'e-permit:auto-approve
                            {--dry-run : Simulate approval without actually updating database}';

    protected $description = 'Auto-approve e-permits ONLY at Class Teacher and Duty Teacher stages (Academic and Head remain manual)';

    protected $ePermitService;
    protected $dryRun = false;

    public function __construct(EPermitService $ePermitService)
    {
        parent::__construct();
        $this->ePermitService = $ePermitService;
    }

    public function handle()
    {
        $this->dryRun = $this->option('dry-run');

        if ($this->dryRun) {
            $this->warn('⚠️  DRY-RUN MODE - No actual changes will be made');
        }

        $this->info('═══════════════════════════════════════════════════════════');
        $this->info('     E-PERMIT AUTO-APPROVAL PROCESS');
        $this->info('═══════════════════════════════════════════════════════════');
        $this->info('');
        $this->info('✅ Auto-approve: Class Teacher and Duty Teacher');
        $this->info('❌ Manual only: Academic Teacher and Head Teacher');
        $this->info('');

        $classTeacherApproved = 0;
        $dutyTeacherApproved = 0;
        $errors = [];

        // ============================================================
        // 1. AUTO-APPROVE CLASS TEACHER PERMITS ONLY
        // ============================================================
        $this->info('📌 [1] Processing CLASS TEACHER stage permits...');
        $this->info('    (These will be AUTO-APPROVED)');
        $this->line('');

        $classTeacherPermits = EPermit::where('status', 'pending_class_teacher')
            ->whereNull('class_teacher_approved_at')
            ->get();

        $this->info("    Found: {$classTeacherPermits->count()} permit(s)");

        foreach ($classTeacherPermits as $permit) {
            try {
                $classTeacher = $this->ePermitService->findClassTeacher($permit->student);

                if (!$classTeacher) {
                    $this->warn("    ⚠ Permit {$permit->permit_number}: No class teacher found - SKIPPED");
                    $errors[] = "Permit {$permit->permit_number}: No class teacher found";
                    continue;
                }

                $teacherName = $classTeacher->user->name ?? $classTeacher->user->first_name ?? 'Unknown';
                $this->info("    → Auto-approving: {$permit->permit_number} by {$teacherName}");

                if (!$this->dryRun) {
                    $result = $this->autoApproveClassTeacher($permit, $classTeacher);

                    if ($result['success']) {
                        $classTeacherApproved++;
                        $this->info("      ✓ SUCCESS");

                        // Log::info("[AUTO-APPROVE] Permit {$permit->permit_number} approved at CLASS TEACHER stage", [
                        //     'permit_id' => $permit->id,
                        //     'teacher_id' => $classTeacher->id,
                        //     'teacher_name' => $teacherName,
                        //     'student' => $permit->student->first_name . ' ' . $permit->student->last_name
                        // ]);
                    } else {
                        $this->error("      ✗ FAILED: {$result['message']}");
                        $errors[] = "Permit {$permit->permit_number}: {$result['message']}";
                    }
                } else {
                    $this->info("      [DRY-RUN] Would approve");
                    $classTeacherApproved++;
                }
            } catch (\Exception $e) {
                $this->error("    ✗ Error: {$permit->permit_number} - " . $e->getMessage());
                $errors[] = "Permit {$permit->permit_number}: " . $e->getMessage();
            }
        }

        $this->line('');

        // ============================================================
        // 2. AUTO-APPROVE DUTY TEACHER PERMITS ONLY
        // ============================================================
        $this->info('📌 [2] Processing DUTY TEACHER stage permits...');
        $this->info('    (These will be AUTO-APPROVED if duty teacher exists)');
        $this->line('');

        $dutyTeacherPermits = EPermit::where('status', 'pending_duty_teacher')
            ->whereNull('duty_teacher_approved_at')
            ->get();

        $this->info("    Found: {$dutyTeacherPermits->count()} permit(s)");

        foreach ($dutyTeacherPermits as $permit) {
            try {
                $dutyTeachers = $this->ePermitService->findDutyTeachersForDate($permit->departure_date);

                if (empty($dutyTeachers)) {
                    $this->warn("    ⚠ Permit {$permit->permit_number}: No duty teacher on roster for {$permit->departure_date->format('Y-m-d')}");
                    $this->info("      → Moving to ACADEMIC stage (manual approval required)");

                    if (!$this->dryRun) {
                        // Just update status to academic, no approval
                        $permit->update(['status' => 'pending_academic']);
                        $this->info("      ✓ Moved to Academic Teacher stage (will wait for MANUAL approval)");
                    } else {
                        $this->info("      [DRY-RUN] Would move to Academic Teacher stage");
                    }
                    continue;
                }

                $dutyTeacher = $dutyTeachers[0];
                $teacherName = $dutyTeacher->user->name ?? $dutyTeacher->user->first_name ?? 'Unknown';
                $this->info("    → Auto-approving: {$permit->permit_number} by {$teacherName}");

                if (!$this->dryRun) {
                    $result = $this->autoApproveDutyTeacher($permit, $dutyTeacher);

                    if ($result['success']) {
                        $dutyTeacherApproved++;
                        $this->info("      ✓ SUCCESS");

                        // Log::info("[AUTO-APPROVE] Permit {$permit->permit_number} approved at DUTY TEACHER stage", [
                        //     'permit_id' => $permit->id,
                        //     'teacher_id' => $dutyTeacher->id,
                        //     'teacher_name' => $teacherName,
                        //     'student' => $permit->student->first_name . ' ' . $permit->student->last_name,
                        //     'departure_date' => $permit->departure_date->format('Y-m-d')
                        // ]);
                    } else {
                        $this->error("      ✗ FAILED: {$result['message']}");
                        $errors[] = "Permit {$permit->permit_number}: {$result['message']}";
                    }
                } else {
                    $this->info("      [DRY-RUN] Would approve");
                    $dutyTeacherApproved++;
                }
            } catch (\Exception $e) {
                $this->error("    ✗ Error: {$permit->permit_number} - " . $e->getMessage());
                $errors[] = "Permit {$permit->permit_number}: " . $e->getMessage();
            }
        }

        $this->line('');

        // ============================================================
        // 3. SUMMARY - NO ACADEMIC OR HEAD AUTO-APPROVE
        // ============================================================
        $this->info('═══════════════════════════════════════════════════════════');
        $this->info('                    PROCESS SUMMARY');
        $this->info('═══════════════════════════════════════════════════════════');
        $this->info('');
        $this->info('✅ AUTO-APPROVED:');
        $this->info("   • Class Teacher stage:  {$classTeacherApproved} permit(s)");
        $this->info("   • Duty Teacher stage:   {$dutyTeacherApproved} permit(s)");
        $this->info('');
        $this->info('⏳ MANUAL APPROVAL REQUIRED:');
        $this->info('   • Academic Teacher stage (role_id = 3) - NOT auto-approved');
        $this->info('   • Head Teacher stage (role_id = 2) - NOT auto-approved');
        $this->info('');

        if (!empty($errors)) {
            $this->warn('⚠️  ERRORS / WARNINGS: ' . count($errors));
            foreach (array_slice($errors, 0, 10) as $error) {
                $this->error("   • {$error}");
            }
        }

        $this->info('');
        $this->info('✅ Auto-approval process completed!');

        return Command::SUCCESS;
    }

    /**
     * Auto-approve at class teacher stage
     */
    private function autoApproveClassTeacher($permit, $teacher)
    {
        DB::beginTransaction();

        try {
            // Determine next status
            $nextStatus = $permit->duty_teacher_id ? 'pending_duty_teacher' : 'pending_academic';

            $permit->update([
                'class_teacher_id' => $teacher->id,
                'class_teacher_approved_at' => now(),
                'class_teacher_action' => 'approved',
                'class_teacher_comment' => 'Auto-approved by system on ' . now()->format('d/m/Y H:i'),
                'status' => $nextStatus
            ]);

            $metadata = [
                'auto_approved' => true,
                'auto_approved_at' => now()->toDateTimeString(),
                'approved_by' => $teacher->user->name ?? 'System'
            ];

            // Use consistent stage name 'class_teacher' not 'class_teacher_auto'
            $this->ePermitService->logTracking(
                $permit,
                $teacher->id,
                'approved',
                'class_teacher',  // ← Changed from 'class_teacher_auto'
                'Auto-approved by system',
                $metadata
            );

            DB::commit();
            return ['success' => true];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Auto-approval failed for permit {$permit->permit_number}: " . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Auto-approve at duty teacher stage
     */
    private function autoApproveDutyTeacher($permit, $teacher)
    {
        DB::beginTransaction();

        try {
            $permit->update([
                'duty_teacher_id' => $teacher->id,
                'duty_teacher_approved_at' => now(),
                'duty_teacher_action' => 'approved',
                'duty_teacher_comment' => 'Auto-approved by system on ' . now()->format('d/m/Y H:i'),
                'status' => 'pending_academic'
            ]);

            $metadata = [
                'auto_approved' => true,
                'auto_approved_at' => now()->toDateTimeString(),
                'approved_by' => $teacher->user->name ?? 'System'
            ];

            // Use consistent stage name 'duty_teacher' not 'duty_teacher_auto'
            $this->ePermitService->logTracking(
                $permit,
                $teacher->id,
                'approved',
                'duty_teacher',  // ← Changed from 'duty_teacher_auto'
                'Auto-approved by system',
                $metadata
            );

            DB::commit();
            return ['success' => true];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Auto-approval failed for permit {$permit->permit_number}: " . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}
