<?php
// app/Services/EPermitService.php

namespace App\Services;

use App\Models\EPermit;
use App\Models\Parents;
use App\Models\school;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\TodRoster;
use App\Traits\formatPhoneTrait;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EPermitService
{
    use formatPhoneTrait;
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
     * Get all class teachers assigned to a student's class and group
     * Returns collection of teachers - ANY of them can approve
     */
    public function getClassTeachers(Student $student): \Illuminate\Support\Collection
    {
        // Query class_teachers table using student's class_id and group
        $classTeacherRecords = DB::table('class_teachers')
            ->where('class_id', $student->class_id)
            ->where('group', $student->group)
            ->get();

        if ($classTeacherRecords->isEmpty()) {
            // Fallback: try without group (for backward compatibility)
            $classTeacherRecords = DB::table('class_teachers')
                ->where('class_id', $student->class_id)
                ->whereNull('group')
                ->get();
        }

        $teacherIds = $classTeacherRecords->pluck('teacher_id')->unique();

        return Teacher::with('user')->whereIn('id', $teacherIds)->get();
    }

    /**
     * Check if a teacher is assigned as class teacher for this student
     */
    public function isClassTeacher(Student $student, Teacher $teacher): bool
    {
        $classTeachers = $this->getClassTeachers($student);
        return $classTeachers->contains('id', $teacher->id);
    }

    /**
     * Get first class teacher for assignment (when creating permit)
     * This is used only for initial assignment to store one teacher ID
     */
    public function getFirstClassTeacher(Student $student): ?Teacher
    {
        $classTeachers = $this->getClassTeachers($student);
        return $classTeachers->isNotEmpty() ? $classTeachers->first() : null;
    }

    /**
     * Get all academic teachers in the school (role_id = 3)
     * ANY of them can approve academic stage permits
     */
    public function getAcademicTeachers($schoolId): \Illuminate\Support\Collection
    {
        return Teacher::with('user')
            ->where('role_id', 3)
            ->where('school_id', $schoolId)
            ->get();
    }

    /**
     * Check if a teacher is an academic teacher
     */
    public function isAcademicTeacher(Teacher $teacher): bool
    {
        return $teacher->role_id === 3;
    }

    /**
     * Get all head teachers in the school (role_id = 2)
     * ANY of them can approve head stage permits
     */
    public function getHeadTeachers($schoolId): \Illuminate\Support\Collection
    {
        return Teacher::with('user')
            ->where('role_id', 2)
            ->where('school_id', $schoolId)
            ->get();
    }

    /**
     * Check if a teacher is a head teacher
     */
    public function isHeadTeacher(Teacher $teacher): bool
    {
        return $teacher->role_id === 2;
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
     * Get first duty teacher for a date (for assignment)
     */
    public function getFirstDutyTeacherForDate($date = null): ?Teacher
    {
        $teachers = $this->findDutyTeachersForDate($date);
        return !empty($teachers) ? $teachers[0] : null;
    }

    /**
     * Check if a teacher is on duty for a specific date
     */
    public function isDutyTeacherForDate(Teacher $teacher, $date = null): bool
    {
        $dutyTeachers = $this->findDutyTeachersForDate($date);
        foreach ($dutyTeachers as $dt) {
            if ($dt->id === $teacher->id) {
                return true;
            }
        }
        return false;
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
     * ANY assigned class teacher, ANY academic teacher, ANY head teacher can approve
     */
    public function approveRequest(EPermit $request, Teacher $teacher, $comment = null)
    {
        DB::beginTransaction();

        try {
            $currentStatus = $request->status;
            $parent_id = $request->parent_id;
            $nextStatus = $this->getNextStatus($request);
            $stage = null;
            $fieldToUpdate = null;
            $timeField = null;
            $actionField = null;
            $commentField = null;

            switch ($currentStatus) {
                case 'pending_class_teacher':
                    // ANY class teacher assigned to this student's class can approve
                    if (!$this->isClassTeacher($request->student, $teacher)) {
                        throw new \Exception('Only class teachers assigned to this student can approve.');
                    }
                    $fieldToUpdate = 'class_teacher_id';
                    $timeField = 'class_teacher_approved_at';
                    $actionField = 'class_teacher_action';
                    $commentField = 'class_teacher_comment';
                    $stage = 'class_teacher';
                    break;

                case 'pending_duty_teacher':
                    // Check if teacher is on duty OR is an academic teacher (can act as backup)
                    $isDutyTeacher = $this->isDutyTeacherForDate($teacher, $request->departure_date);

                    if (!$isDutyTeacher && !$this->isAcademicTeacher($teacher)) {
                        throw new \Exception('Only duty teachers or academic teacher can approve at this stage.');
                    }
                    $fieldToUpdate = 'duty_teacher_id';
                    $timeField = 'duty_teacher_approved_at';
                    $actionField = 'duty_teacher_action';
                    $commentField = 'duty_teacher_comment';
                    $stage = $this->isAcademicTeacher($teacher) ? 'duty_teacher_by_academic' : 'duty_teacher';
                    break;

                case 'pending_academic':
                    // ANY academic teacher can approve
                    if (!$this->isAcademicTeacher($teacher)) {
                        throw new \Exception('Only academic teachers can approve at this stage.');
                    }
                    $fieldToUpdate = 'academic_teacher_id';
                    $timeField = 'academic_teacher_approved_at';
                    $actionField = 'academic_teacher_action';
                    $commentField = 'academic_teacher_comment';
                    $stage = 'academic';
                    break;

                case 'pending_head':
                    // ANY head teacher can approve
                    if (!$this->isHeadTeacher($teacher)) {
                        throw new \Exception('Only head teachers can approve at this stage.');
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
                // $this->generateEPermitPDF($request);
                $parent_info = Parents::with('User')->findOrFail($parent_id);
                $this->NotifyParentofTheStudentBySms($request, $schoolId = $parent_info->school_id, $parent_info);
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
    /**
     * Reject request with full tracking
     */
    public function rejectRequest(EPermit $request, Teacher $teacher, $reason)
    {
        DB::beginTransaction();

        try {
            $currentStatus = $request->status;
            $stage = null;
            $commentField = null;
            $actionField = null;
            $timestampField = null;
            $teacherIdField = null;

            switch ($currentStatus) {
                case 'pending_class_teacher':
                    if (!$this->isClassTeacher($request->student, $teacher)) {
                        throw new \Exception('Only class teachers assigned to this student can reject.');
                    }
                    $stage = 'class_teacher';
                    $commentField = 'class_teacher_comment';
                    $actionField = 'class_teacher_action';
                    $timestampField = 'class_teacher_approved_at';
                    $teacherIdField = 'class_teacher_id';
                    break;

                case 'pending_duty_teacher':
                    $isDutyTeacher = $this->isDutyTeacherForDate($teacher, $request->departure_date);

                    if (!$isDutyTeacher && !$this->isAcademicTeacher($teacher)) {
                        throw new \Exception('Only duty teacher or academic teacher can reject.');
                    }
                    $stage = $this->isAcademicTeacher($teacher) ? 'duty_teacher_by_academic' : 'duty_teacher';
                    $commentField = 'duty_teacher_comment';
                    $actionField = 'duty_teacher_action';
                    $timestampField = 'duty_teacher_approved_at';
                    $teacherIdField = 'duty_teacher_id';
                    break;

                case 'pending_academic':
                    if (!$this->isAcademicTeacher($teacher)) {
                        throw new \Exception('Only academic teachers can reject.');
                    }
                    $stage = 'academic';
                    $commentField = 'academic_teacher_comment';
                    $actionField = 'academic_teacher_action';
                    $timestampField = 'academic_teacher_approved_at';
                    $teacherIdField = 'academic_teacher_id';
                    break;

                case 'pending_head':
                    if (!$this->isHeadTeacher($teacher)) {
                        throw new \Exception('Only head teachers can reject.');
                    }
                    $stage = 'head';
                    $commentField = 'head_teacher_comment';
                    $actionField = 'head_teacher_action';
                    $timestampField = 'head_teacher_approved_at';
                    $teacherIdField = 'head_teacher_id';
                    break;

                default:
                    throw new \Exception('Cannot reject request in current status');
            }

            // Update the permit with complete rejection details
            $updateData = [
                'status' => 'rejected',
                'rejection_reason' => $reason,
                $commentField => $reason,
                $actionField => 'rejected',
                $timestampField => now(),
                $teacherIdField => $teacher->id,
            ];

            $request->update($updateData);

            // Log tracking
            $this->logTracking($request, $teacher->id, 'rejected', $stage, $reason);

            DB::commit();

            return [
                'success' => true,
                'message' => 'Request rejected successfully',
                'rejected_by' => $teacher->user->name ?? 'Unknown',
                'rejected_at' => now()->format('d/m/Y H:i'),
                'rejection_reason' => $reason,
                'stage' => $stage
            ];
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

    public function NotifyParentofTheStudentBySms(EPermit $request, $schoolId, $parent_info)
    {
        $student = Student::findOrFail($request->student_id);
        $school = school::findOrFail($schoolId);
        $headTeacher = Teacher::with('User')->findOrFail($request->head_teacher_id);
        $nextSmsService = new NextSmsService();

        $reasonText = match ($request->reason) {
            'medical' => 'Matibabu',
            'family_matter' => 'Jambo la Kifamilia',
            'other' => 'Sababu Nyingine',
            default => ucfirst($request->reason)
        };

        $message = "Habari {$parent_info->user->first_name} ";
        $message .= "Mtoto wako {$student->first_name} {$student->last_name} ";
        $message .= "Amepewa kibali Na.{$request->permit_number} cha ruhusa kuanzia " . Carbon::parse($request->head_teacher_approved_at)->format('d/m/Y') . " hadi " . Carbon::parse($request->expected_return_date)->format('d/m/Y');
        $message .= ". Ruhusa imeombwa na {$request->guardian_name}, Sababu ya ruhusa ni {$reasonText} ";
        $message .= "Kibali kimetolewa na {$headTeacher->user->first_name} {$headTeacher->user->last_name[0]} ";
        $message .= "Kama hutambui ombi hili tafadhali piga {$school->school_phone} /fika shuleni. Asante";

        $payload = [
            'from' => $school->sender_id ?? 'SHULE APP',
            'to' => $this->formatPhoneNumberForSms($parent_info->user->phone),
            'text' => $message,
            'reference' => uniqid(),
        ];

        // Log::info("Sending Sms to: " . $this->formatPhoneNumberForSms($parent_info->user->phone) . ": Message " . $message);
        $nextSmsService->sendSmsByNext($payload['from'], $payload['to'], $payload['text'], $payload['reference']);
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
