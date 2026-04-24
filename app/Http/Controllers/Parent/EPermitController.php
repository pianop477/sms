<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\EPermit;
use App\Models\school;
use App\Models\Student;
use App\Models\Teacher;
use App\Services\EPermitService;
use App\Services\NextSmsService;
use App\Traits\formatPhoneTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Vinkla\Hashids\Facades\Hashids;

class EPermitController extends Controller
{
    protected $ePermitService;
    use formatPhoneTrait;

    public function __construct(EPermitService $ePermitService)
    {
        $this->ePermitService = $ePermitService;
    }

    /**
     * Show student ID entry form
     */
    public function showStudentForm()
    {
        return view('parent.e-permit.student-form');
    }

    /**
     * Verify student and return JSON response
     */
    public function verifyStudent(Request $request): JsonResponse
    {
        $request->validate([
            'student_id' => 'required|string'
        ]);

        // Find student by admission number or id - ONLY status 1 (active)
        $student = Student::with(['class', 'parents', 'schools'])
            ->where(function ($query) use ($request) {
                $query->where('admission_number', $request->student_id);
            })
            ->where('status', 1)  // ← MAJIBU: Filter active students only
            ->first();

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Mwanafunzi hayupo. Tafadhali hakikisha umeingiza ID sahihi.'
            ], 404);
        }

        // Check if student has active permit
        $hasActivePermit = $this->ePermitService->hasActivePermit($student);

        if ($hasActivePermit) {
            $activePermit = $this->ePermitService->getActivePermit($student);
            return response()->json([
                'success' => false,
                'has_active_permit' => true,
                'message' => 'Mwanafunzi huyu tayari ana ombi la ruhusa linalosubiri kuthibitishwa. Tafadhali subiri ombi lako la sasa likamilike.',
                'permit' => [
                    'number' => $activePermit->permit_number,
                    'status' => $activePermit->status,
                    'date' => $activePermit->created_at->format('d/m/Y')
                ]
            ]);
        }

        // Return student data
        return response()->json([
            'success' => true,
            'student' => [
                'id' => Hashids::encode($student->id),
                'admission_number' => $student->admission_number,
                'first_name' => $student->first_name,
                'middle_name' => $student->middle_name,
                'last_name' => $student->last_name,
                'gender' => $student->gender,
                'image' => $student->image,
                'class' => $student->class ? [
                    'id' => $student->class->id,
                    'class_name' => $student->class->class_name
                ] : null,
                'group' => $student->group,
                'stream' => $student->group
            ]
        ]);
    }

    /**
     * Show multi-step request form
     */
    public function showRequestForm($studentId)
    {
        $decoded = Hashids::decode($studentId);
        $student = Student::with(['class', 'parents'])
            ->where('id', $decoded[0])
            ->firstOrFail();

        // Check for active permit
        if ($this->ePermitService->hasActivePermit($student)) {
            return redirect()->route('parent.e-permit.student-form')
                ->with('error', 'Mwanafunzi huyu tayari ana ombi la ruhusa linalosubiri.');
        }

        return view('parent.e-permit.request-form', [
            'student' => $student
        ]);
    }

    /**
     * Submit the e-permit request
     */
    public function submitRequest(Request $request, $studentId): JsonResponse
    {
        $decoded = Hashids::decode($studentId);
        $student = Student::findOrFail($decoded[0]);

        // Double check no active request
        if ($this->ePermitService->hasActivePermit($student)) {
            return response()->json([
                'success' => false,
                'message' => 'Mwanafunzi huyu tayari ana ombi la ruhusa linalosubiri.'
            ], 422);
        }

        // Validate request data
        $validated = $request->validate([
            'guardian_name' => 'required|string|max:255',
            'guardian_phone' => 'required|string|max:20',
            'guardian_type' => 'required|in:parent,guardian',
            'relationship' => 'required|string|max:50',
            'reason' => 'required|in:medical,family_matter,other',
            'other_reason' => 'required_if:reason,other|nullable|string|max:500',
            'departure_date' => 'required|date|after_or_equal:today',
            'expected_return_date' => 'required|date|after_or_equal:departure_date'
        ]);

        // Get ALL class teachers for this student (to check existence and for notifications)
        $allClassTeachers = $this->ePermitService->getClassTeachers($student);

        if ($allClassTeachers->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Hakuna mwalimu wa darasa aliyebainishwa kwa mwanafunzi huyu. Tafadhali wasiliana na ofisi ya shule.'
            ], 422);
        }

        // Get FIRST class teacher for STORAGE REFERENCE only (we store one teacher ID as reference)
        // ANY class teacher can approve later, but we need one for initial record keeping
        $firstClassTeacher = $allClassTeachers->first();

        // Get first duty teacher for the departure date from tod_roster (for assignment)
        $firstDutyTeacher = $this->ePermitService->getFirstDutyTeacherForDate($validated['departure_date']);

        // Get first academic teacher for reference (ANY academic teacher can approve later)
        $academicTeachers = $this->ePermitService->getAcademicTeachers($student->school_id);
        $firstAcademicTeacher = $academicTeachers->isNotEmpty() ? $academicTeachers->first() : null;

        // Get first head teacher for reference (ANY head teacher can approve later)
        $headTeachers = $this->ePermitService->getHeadTeachers($student->school_id);
        $firstHeadTeacher = $headTeachers->isNotEmpty() ? $headTeachers->first() : null;

        // Determine initial status
        $initialStatus = 'pending_class_teacher';

        // Create e-permit request
        $ePermit = EPermit::create([
            'permit_number' => $this->ePermitService->generatePermitNumber(),
            'student_id' => $student->id,
            'parent_id' => $student->parent_id,
            'guardian_name' => $validated['guardian_name'],
            'guardian_phone' => $validated['guardian_phone'],
            'guardian_type' => $validated['guardian_type'],
            'relationship' => $validated['relationship'],
            'reason' => $validated['reason'],
            'other_reason' => $validated['other_reason'] ?? null,
            'departure_date' => $validated['departure_date'],
            'departure_time' => now(),
            'expected_return_date' => $validated['expected_return_date'],
            'status' => $initialStatus,
            'class_teacher_id' => $firstClassTeacher->id,
            'duty_teacher_id' => $firstDutyTeacher?->id,
            'academic_teacher_id' => $firstAcademicTeacher?->id,
            'head_teacher_id' => $firstHeadTeacher?->id
        ]);

        // Log tracking
        $this->ePermitService->logTracking(
            $ePermit,
            null,
            'submitted',
            'submission',
            'Ombi la ruhusa limewasilishwa na mzazi/mlezi'
        );

        // ============ SEND NOTIFICATIONS TO ALL CLASS TEACHERS ============
        $this->sendNotificationsToClassTeachers($allClassTeachers, $ePermit);

        // Update message to inform parent that ANY class teacher can handle
        $message = 'Ombi lako limewasilishwa kwa kikamilifu. Mwalumu wa darasa atalifanyia kazi.';

        return response()->json([
            'success' => true,
            'message' => $message,
            'permit_number' => $ePermit->permit_number
        ]);
    }

    /**
     * Send notifications to all class teachers about new permit request
     */
    protected function sendNotificationsToClassTeachers($classTeachers, $ePermit): void
    {
        foreach ($classTeachers as $classTeacher) {
            // Database notification

            $this->notifyClassTeacherBySms($classTeacher, $ePermit);
        }
    }

    private function notifyClassTeacherBySms($classTeacher, $ePermit)
    {
        $nextSmsService = new NextSmsService();

        $school = school::find($classTeacher->school_id);
        $teacher = Teacher::with('User')->find($classTeacher->id);

        $formattedPhone = $this->formatPhoneNumberForSms($teacher->user->phone);
        // Log::info("Formatted Phone number ". $formattedPhone);
        // Log::info("School id ". $school);

        if(! $school) {
            Log::info("School info not found");
        }

        $message = "Habari, Ombi la Ruhusa Na. {$ePermit->permit_number} limetumwa kwako kwa ajili ya {$ePermit->student->first_name} {$ePermit->student->last_name}. Tafadhali ingia kwenye mfumo kushughulikia ombi hilo. Asante!";
        $response = $nextSmsService->sendSmsByNext(
            $school->sender_id ?? 'SHULE APP',
            $formattedPhone,
            $message,
            uniqid(),
        );
        // Log::info('Sending Sms to '.$formattedPhone. 'Sms '. $message);
    }
}
