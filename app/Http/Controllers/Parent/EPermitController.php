<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\EPermit;
use App\Models\Student;
use App\Services\EPermitService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Vinkla\Hashids\Facades\Hashids;

class EPermitController extends Controller
{
    protected $ePermitService;

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

        // Find student by admission number or id
        $student = Student::with(['class', 'parents', 'schools'])
            ->where(function ($query) use ($request) {
                $query->where('admission_number', $request->student_id)
                    ->orWhere('id', $request->student_id);
            })
            ->first();

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Student ID haijapatikana. Tafadhali hakikisha umeingiza sahihi.'
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

        // Find class teacher using class_id and group from class_teachers table
        $classTeacher = $this->ePermitService->findClassTeacher($student);

        if (!$classTeacher) {
            return response()->json([
                'success' => false,
                'message' => 'Hakuna mwalimu wa darasa aliyebainishwa kwa mwanafunzi huyu. Tafadhali wasiliana na ofisi ya shule.'
            ], 422);
        }

        // Find duty teacher(s) for the departure date from tod_roster
        $dutyTeachers = $this->ePermitService->findDutyTeachersForDate($validated['departure_date']);
        $firstDutyTeacher = !empty($dutyTeachers) ? $dutyTeachers[0] : null;

        // Find academic teacher (role_id = 3)
        $academicTeacher = $this->ePermitService->findAcademicTeacher($student->school_id);

        // Find head teacher (role_id = 2)
        $headTeacher = $this->ePermitService->findHeadTeacher($student->school_id);

        // Determine initial status - if no duty teacher, skip to academic
        $initialStatus = $firstDutyTeacher ? 'pending_class_teacher' : 'pending_class_teacher';

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
            'class_teacher_id' => $classTeacher->id,
            'duty_teacher_id' => $firstDutyTeacher?->id,
            'academic_teacher_id' => $academicTeacher?->id,
            'head_teacher_id' => $headTeacher?->id
        ]);

        // Log tracking
        $this->ePermitService->logTracking(
            $ePermit,
            null,
            'submitted',
            'submission',
            'Ombi la ruhusa limewasilishwa na mzazi/mlezi'
        );

        $message = 'Ombi lako limewasilishwa kwa mafanikio.';
        // if (!$firstDutyTeacher) {
        //     $message .= ' Kumbuka: Hakuna mwalimu wa zamu kwa tarehe uliyochagua, hivyo ombi litaenda moja kwa moja kwa Mwalimu wa Taaluma baada ya Mwalimu wa Darasa.';
        // }

        return response()->json([
            'success' => true,
            'message' => $message,
            'permit_number' => $ePermit->permit_number
        ]);
    }
}
