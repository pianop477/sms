<?php
// app/Services/EPermitService.php

namespace App\Services;

use App\Models\EPermit;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\TodRoster;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EPermitService
{
    /**
     * Generate unique permit number
     */
    public function generatePermitNumber(): string
    {
        $year = date('Y');
        $lastPermit = EPermit::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        $sequence = $lastPermit ? intval(substr($lastPermit->permit_number, -5)) + 1 : 1;

        return sprintf('EPRM/%s/%05d', $year, $sequence);
    }

    /**
     * Find class teacher for a student based on class_id and group
     * This is the CORRECT way - matching student's class and stream with class_teachers table
     */
    public function findClassTeacher(Student $student): ?Teacher
    {
        // Query class_teachers table using student's class_id and group
        $classTeacherRecord = DB::table('class_teachers')
            ->where('class_id', $student->class_id)
            ->where('group', $student->group)  // Match the stream/group
            ->first();

        if ($classTeacherRecord && $classTeacherRecord->teacher_id) {
            return Teacher::with('user')->find($classTeacherRecord->teacher_id);
        }

        // Fallback: try without group (for backward compatibility)
        $classTeacherRecord = DB::table('class_teachers')
            ->where('class_id', $student->class_id)
            ->whereNull('group')
            ->first();

        if ($classTeacherRecord && $classTeacherRecord->teacher_id) {
            return Teacher::with('user')->find($classTeacherRecord->teacher_id);
        }

        return null;
    }

    /**
     * Find duty teacher(s) for a specific date from tod_roster
     * Returns array of teachers on duty for that date
     */
    public function findDutyTeachersForDate($date = null): array
    {
        $date = $date ?? now();
        $dateFormatted = $date instanceof \DateTime ? $date->format('Y-m-d') : date('Y-m-d', strtotime($date));

        $rosters = TodRoster::where('start_date', '<=', $dateFormatted)
            ->where('end_date', '>=', $dateFormatted)
            ->where('status', 'active')
            ->get();

        $teachers = [];
        foreach ($rosters as $roster) {
            $teacher = Teacher::with('user')->find($roster->teacher_id);
            if ($teacher) {
                $teachers[] = $teacher;
            }
        }

        return $teachers;
    }

    /**
     * Find first duty teacher for a date (for assignment)
     */
    public function findFirstDutyTeacherForDate($date = null): ?Teacher
    {
        $teachers = $this->findDutyTeachersForDate($date);
        return !empty($teachers) ? $teachers[0] : null;
    }

    /**
     * Find academic teacher (role_id = 3)
     */
    public function findAcademicTeacher($schoolId): ?Teacher
    {
        return Teacher::with('user')
            ->where('role_id', 3)
            ->where('school_id', $schoolId)
            ->first();
    }

    /**
     * Find head teacher (role_id = 2)
     */
    public function findHeadTeacher($schoolId): ?Teacher
    {
        return Teacher::with('user')
            ->where('role_id', 2)
            ->where('school_id', $schoolId)
            ->first();
    }

    /**
     * Check if student has active permit request
     */
    public function hasActivePermit(Student $student): bool
    {
        return EPermit::where('student_id', $student->id)
            ->whereIn('status', [
                'pending_class_teacher',
                'pending_duty_teacher',
                'pending_academic',
                'pending_head',
                'approved'
            ])
            ->exists();
    }

    /**
     * Get active permit for student
     */
    public function getActivePermit(Student $student): ?EPermit
    {
        return EPermit::where('student_id', $student->id)
            ->whereIn('status', [
                'pending_class_teacher',
                'pending_duty_teacher',
                'pending_academic',
                'pending_head',
                'approved'
            ])
            ->first();
    }

    /**
     * Log tracking information
     */
    public function logTracking(EPermit $ePermit, $teacherId, $action, $stage, $comment = null, $metadata = [])
    {
        return $ePermit->trackingLogs()->create([
            'teacher_id' => $teacherId,
            'action' => $action,
            'stage' => $stage,
            'comment' => $comment,
            'metadata' => $metadata,
            'ip_address' => request()->ip()
        ]);
    }

    /**
     * Get the next status based on current status
     */
    protected function getNextStatus(EPermit $permit): string
    {
        switch ($permit->status) {
            case 'pending_class_teacher':
                // After class teacher, go to duty teacher if exists, otherwise academic
                if ($permit->duty_teacher_id) {
                    return 'pending_duty_teacher';
                }
                return 'pending_academic';

            case 'pending_duty_teacher':
                return 'pending_academic';

            case 'pending_academic':
                return 'pending_head';

            case 'pending_head':
                return 'approved';

            default:
                return $permit->status;
        }
    }

    /**
     * Approve request at different stages
     */
    public function approveRequest(EPermit $request, Teacher $teacher, $comment = null)
    {
        DB::beginTransaction();

        try {
            $currentStatus = $request->status;
            $nextStatus = $this->getNextStatus($request);
            $stage = null;
            $fieldToUpdate = null;
            $timeField = null;
            $actionField = null;
            $commentField = null;

            switch ($currentStatus) {
                case 'pending_class_teacher':
                    // Verify this is the correct class teacher for this student
                    $expectedClassTeacher = $this->findClassTeacher($request->student);
                    if (!$expectedClassTeacher || $expectedClassTeacher->id !== $teacher->id) {
                        throw new \Exception('Only the assigned class teacher for this student can approve.');
                    }
                    $fieldToUpdate = 'class_teacher_id';
                    $timeField = 'class_teacher_approved_at';
                    $actionField = 'class_teacher_action';
                    $commentField = 'class_teacher_comment';
                    $stage = 'class_teacher';
                    break;

                case 'pending_duty_teacher':
                    // Verify this teacher is on duty for the request date
                    $dutyTeachers = $this->findDutyTeachersForDate($request->departure_date);
                    $isDutyTeacher = false;
                    foreach ($dutyTeachers as $dt) {
                        if ($dt->id === $teacher->id) {
                            $isDutyTeacher = true;
                            break;
                        }
                    }
                    // Academic teacher can also approve on behalf
                    if (!$isDutyTeacher && $teacher->role_id !== 3) {
                        throw new \Exception('Only duty teachers or academic teacher can approve at this stage.');
                    }
                    $fieldToUpdate = 'duty_teacher_id';
                    $timeField = 'duty_teacher_approved_at';
                    $actionField = 'duty_teacher_action';
                    $commentField = 'duty_teacher_comment';
                    $stage = $teacher->role_id === 3 ? 'duty_teacher_by_academic' : 'duty_teacher';
                    break;

                case 'pending_academic':
                    if ($teacher->role_id !== 3) {
                        throw new \Exception('Only academic teacher can approve at this stage.');
                    }
                    $fieldToUpdate = 'academic_teacher_id';
                    $timeField = 'academic_teacher_approved_at';
                    $actionField = 'academic_teacher_action';
                    $commentField = 'academic_teacher_comment';
                    $stage = 'academic';
                    break;

                case 'pending_head':
                    if ($teacher->role_id !== 2) {
                        throw new \Exception('Only head teacher can approve at this stage.');
                    }
                    $fieldToUpdate = 'head_teacher_id';
                    $timeField = 'head_teacher_approved_at';
                    $actionField = 'head_teacher_action';
                    $commentField = 'head_teacher_comment';
                    $stage = 'head';
                    break;

                default:
                    throw new \Exception('Cannot approve request in current status: ' . $currentStatus);
            }

            $request->update([
                $fieldToUpdate => $teacher->id,
                $timeField => now(),
                $actionField => 'approved',
                $commentField => $comment,
                'status' => $nextStatus
            ]);

            $this->logTracking($request, $teacher->id, 'approved', $stage, $comment);

            // If final approval, generate PDF
            if ($nextStatus === 'approved') {
                $this->generateEPermitPDF($request);
            }

            DB::commit();

            return [
                'success' => true,
                'message' => 'Request approved successfully',
                'next_status' => $nextStatus
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('E-Permit approval failed: ' . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Reject request
     */
    public function rejectRequest(EPermit $request, Teacher $teacher, $reason)
    {
        DB::beginTransaction();

        try {
            $currentStatus = $request->status;
            $stage = null;
            $commentField = null;

            switch ($currentStatus) {
                case 'pending_class_teacher':
                    $expectedClassTeacher = $this->findClassTeacher($request->student);
                    if (!$expectedClassTeacher || $expectedClassTeacher->id !== $teacher->id) {
                        throw new \Exception('Only the assigned class teacher can reject.');
                    }
                    $stage = 'class_teacher';
                    $commentField = 'class_teacher_comment';
                    break;

                case 'pending_duty_teacher':
                    $dutyTeachers = $this->findDutyTeachersForDate($request->departure_date);
                    $isDutyTeacher = false;
                    foreach ($dutyTeachers as $dt) {
                        if ($dt->id === $teacher->id) {
                            $isDutyTeacher = true;
                            break;
                        }
                    }
                    if (!$isDutyTeacher && $teacher->role_id !== 3) {
                        throw new \Exception('Only duty teacher or academic teacher can reject.');
                    }
                    $stage = $teacher->role_id === 3 ? 'duty_teacher_by_academic' : 'duty_teacher';
                    $commentField = 'duty_teacher_comment';
                    break;

                case 'pending_academic':
                    if ($teacher->role_id !== 3) {
                        throw new \Exception('Only academic teacher can reject.');
                    }
                    $stage = 'academic';
                    $commentField = 'academic_teacher_comment';
                    break;

                case 'pending_head':
                    if ($teacher->role_id !== 2) {
                        throw new \Exception('Only head teacher can reject.');
                    }
                    $stage = 'head';
                    $commentField = 'head_teacher_comment';
                    break;

                default:
                    throw new \Exception('Cannot reject request in current status');
            }

            $request->update([
                'status' => 'rejected',
                'rejection_reason' => $reason,
                $commentField => $reason
            ]);

            $this->logTracking($request, $teacher->id, 'rejected', $stage, $reason);

            DB::commit();

            return ['success' => true, 'message' => 'Request rejected successfully'];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('E-Permit rejection failed: ' . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Generate PDF for approved e-permit
     */
    public function generateEPermitPDF(EPermit $request)
    {
        // Will be implemented with PDF package
        $pdfPath = storage_path("app/public/e-permits/e-permit_{$request->permit_number}.pdf");
        $request->update(['pdf_path' => $pdfPath]);
        return $pdfPath;
    }

    /**
     * Complete return check-in
     */
    public function completeReturn(EPermit $request, Teacher $teacher, array $returnData)
    {
        DB::beginTransaction();

        try {
            $isLate = $returnData['actual_return_date'] > $request->expected_return_date;

            $request->update([
                'actual_return_date' => $returnData['actual_return_date'],
                'is_late_return' => $isLate,
                'late_return_reason' => $isLate ? ($returnData['late_reason'] ?? null) : null,
                'returned_alone' => $returnData['returned_alone'],
                'return_accompanied_by' => $returnData['returned_alone'] ? null : $returnData['accompanied_by_name'],
                'return_guardian_type' => $returnData['returned_alone'] ? null : $returnData['guardian_type'],
                'return_relationship' => $returnData['returned_alone'] ? null : $returnData['relationship'],
                'verified_by' => $teacher->id,
                'verified_at' => now(),
                'status' => 'completed'
            ]);

            $this->logTracking($request, $teacher->id, 'completed', 'return', 'Student returned to school');

            DB::commit();

            return ['success' => true, 'message' => 'Return confirmed successfully'];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Return confirmation failed: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to confirm return'];
        }
    }
}
