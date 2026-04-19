<?php
// app/Http/Controllers/Teacher/EPermitController.php

namespace App\Http\Controllers\Teacher;

use App\Exports\EPermitReportExport;
use App\Http\Controllers\Controller;
use App\Models\EPermit;
use App\Models\school;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\TodRoster;
use App\Services\EPermitService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Maatwebsite\Excel\Facades\Excel;
use Vinkla\Hashids\Facades\Hashids;

class EPermitController extends Controller
{
    protected $ePermitService;

    public function __construct(EPermitService $ePermitService)
    {
        $this->ePermitService = $ePermitService;
        $this->middleware('auth');
    }

    /**
     * Get the authenticated teacher
     */
    protected function getTeacher()
    {
        $user = Auth::user();
        return Teacher::with('user')->where('user_id', $user->id)->first();
    }

    /**
     * Display teacher dashboard
     */
    public function dashboard(Request $request): View
    {
        $teacher = $this->getTeacher();

        if (!$teacher) {
            abort(403, 'Unauthorized. Teacher record not found.');
        }

        // Only role_id 2 (Head), 3 (Academic), 4 (Class Teacher) can access
        if (!in_array($teacher->role_id, [2, 3, 4])) {
            // abort(403, 'Unauthorized. You do not have permission to access e-Permit system.');
            return redirect()->to_route('error.page');
        }

        // Get pending permits based on role
        $pendingPermits = $this->getPendingPermits($teacher);
        $historyPermits = $this->getHistoryPermits($teacher);

        // Get stats
        $stats = $this->getDashboardStats($teacher);

        return view('teacher.e-permit.dashboard', [
            'pendingPermits' => $pendingPermits,
            'historyPermits' => $historyPermits,
            'stats' => $stats,
            'teacher' => $teacher
        ]);
    }

    /**
     * Get pending permits based on teacher role
     * ANY assigned class teacher, ANY academic teacher, ANY head teacher can see relevant permits
     */
    protected function getPendingPermits($teacher)
    {
        switch ($teacher->role_id) {
            case 4: // Class Teacher
                // Get all pending_class_teacher permits
                $allPending = EPermit::where('status', 'pending_class_teacher')->get();

                // Filter to only those where this teacher is a class teacher for that student
                $filtered = $allPending->filter(function ($permit) use ($teacher) {
                    return $this->ePermitService->isClassTeacher($permit->student, $teacher);
                });

                // Paginate manually
                $page = request()->get('page', 1);
                $perPage = 15;
                return new \Illuminate\Pagination\LengthAwarePaginator(
                    $filtered->forPage($page, $perPage),
                    $filtered->count(),
                    $perPage,
                    $page,
                    ['path' => request()->url()]
                );

            case 3: // Academic Teacher
                // ANY academic teacher can see pending academic and duty teacher permits
                return EPermit::where(function ($q) {
                    $q->where('status', 'pending_academic')
                        ->orWhere('status', 'pending_duty_teacher');
                })->orderBy('created_at', 'desc')->paginate(15);

            case 2: // Head Teacher
                // ANY head teacher can see pending head permits
                return EPermit::where('status', 'pending_head')
                    ->orderBy('created_at', 'desc')
                    ->paginate(15);

            default:
                return collect();
        }
    }

    /**
     * Get history permits based on teacher role
     */
    protected function getHistoryPermits($teacher)
    {
        switch ($teacher->role_id) {
            case 4: // Class Teacher
                // Get all history permits where this teacher is a class teacher
                $allHistory = EPermit::whereIn('status', ['approved', 'rejected', 'completed'])->get();

                $filtered = $allHistory->filter(function ($permit) use ($teacher) {
                    return $this->ePermitService->isClassTeacher($permit->student, $teacher);
                });

                $page = request()->get('page', 1);
                $perPage = 15;
                return new \Illuminate\Pagination\LengthAwarePaginator(
                    $filtered->forPage($page, $perPage),
                    $filtered->count(),
                    $perPage,
                    $page,
                    ['path' => request()->url()]
                );

            case 3: // Academic Teacher
                // ANY academic teacher can see all history
                return EPermit::whereIn('status', ['approved', 'rejected', 'completed'])
                    ->orderBy('created_at', 'desc')
                    ->paginate(15);

            case 2: // Head Teacher
                // ANY head teacher can see all history
                return EPermit::whereIn('status', ['approved', 'rejected', 'completed'])
                    ->orderBy('created_at', 'desc')
                    ->paginate(15);

            default:
                return collect();
        }
    }

    /**
     * Get dashboard statistics
     */
    protected function getDashboardStats($teacher): array
    {
        $stats = [
            'pending' => 0,
            'approved' => 0,
            'rejected' => 0,
            'completed' => 0,
            'total' => 0
        ];

        switch ($teacher->role_id) {
            case 4: // Class Teacher
                $allPending = EPermit::where('status', 'pending_class_teacher')->get();
                $stats['pending'] = $allPending->filter(function ($permit) use ($teacher) {
                    return $this->ePermitService->isClassTeacher($permit->student, $teacher);
                })->count();

                $allApproved = EPermit::where('status', 'approved')->get();
                $stats['approved'] = $allApproved->filter(function ($permit) use ($teacher) {
                    return $this->ePermitService->isClassTeacher($permit->student, $teacher);
                })->count();

                $allRejected = EPermit::where('status', 'rejected')->get();
                $stats['rejected'] = $allRejected->filter(function ($permit) use ($teacher) {
                    return $this->ePermitService->isClassTeacher($permit->student, $teacher);
                })->count();

                $allCompleted = EPermit::where('status', 'completed')->get();
                $stats['completed'] = $allCompleted->filter(function ($permit) use ($teacher) {
                    return $this->ePermitService->isClassTeacher($permit->student, $teacher);
                })->count();
                break;

            case 3: // Academic Teacher
                $stats['pending'] = EPermit::whereIn('status', ['pending_academic', 'pending_duty_teacher'])->count();
                $stats['approved'] = EPermit::where('status', 'approved')->count();
                $stats['rejected'] = EPermit::where('status', 'rejected')->count();
                $stats['completed'] = EPermit::where('status', 'completed')->count();
                break;

            case 2: // Head Teacher
                $stats['pending'] = EPermit::where('status', 'pending_head')->count();
                $stats['approved'] = EPermit::where('status', 'approved')->count();
                $stats['rejected'] = EPermit::where('status', 'rejected')->count();
                $stats['completed'] = EPermit::where('status', 'completed')->count();
                break;
        }

        $stats['total'] = array_sum($stats);

        return $stats;
    }

    /**
     * Show single permit details
     */
    public function show($id): View
    {
        // Decode the hashed ID
        $decoded = Hashids::decode($id);
        $permitId = is_array($decoded) && !empty($decoded) ? $decoded[0] : $id;

        $teacher = $this->getTeacher();
        $permit = EPermit::with(['student', 'student.class', 'parent', 'classTeacher.user', 'dutyTeacher.user', 'academicTeacher.user', 'headTeacher.user', 'trackingLogs.teacher.user'])
            ->findOrFail($permitId);

        if (!$this->canAccessPermit($teacher, $permit)) {
            // abort(403, 'Unauthorized access to this permit.');
            return redirect()->to_route('error.page');
        }

        $timeline = $this->getWorkflowTimeline($permit);

        // Check if duty teacher exists for this permit's departure date
        $dutyTeachers = $this->ePermitService->findDutyTeachersForDate($permit->departure_date);
        $hasDutyTeacher = !empty($dutyTeachers);

        // Check if current user can approve
        $canApprove = $this->canApprovePermit($teacher, $permit);

        return view('teacher.e-permit.show', [
            'permit' => $permit,
            'timeline' => $timeline,
            'teacher' => $teacher,
            'canApprove' => $canApprove,
            'hasDutyTeacher' => $hasDutyTeacher
        ]);
    }

    /**
     * Check if teacher can approve the permit at current stage
     * ANY assigned class teacher, ANY academic teacher, ANY head teacher can approve
     */
    protected function canApprovePermit($teacher, $permit): bool
    {
        switch ($permit->status) {
            case 'pending_class_teacher':
                // ANY class teacher assigned to this student can approve
                return $this->ePermitService->isClassTeacher($permit->student, $teacher);

            case 'pending_duty_teacher':
                // Duty teacher OR any academic teacher can approve
                return $this->ePermitService->isDutyTeacherForDate($teacher, $permit->departure_date) ||
                    $this->ePermitService->isAcademicTeacher($teacher);

            case 'pending_academic':
                // ANY academic teacher can approve
                return $this->ePermitService->isAcademicTeacher($teacher);

            case 'pending_head':
                // ANY head teacher can approve
                return $this->ePermitService->isHeadTeacher($teacher);

            default:
                return false;
        }
    }

    /**
     * Approve permit
     */
    public function approve(Request $request, $id): JsonResponse
    {
        $teacher = $this->getTeacher();
        $permit = EPermit::findOrFail($id);

        if (!$this->canApprovePermit($teacher, $permit)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to approve this permit.'
            ], 403);
        }

        $result = $this->ePermitService->approveRequest(
            $permit,
            $teacher,
            $request->input('comment')
        );

        return response()->json($result);
    }

    /**
     * Reject permit
     */
    public function reject(Request $request, $id): JsonResponse
    {
        $request->validate([
            'reason' => 'required|string|min:5|max:500'
        ]);

        $teacher = $this->getTeacher();
        $permit = EPermit::findOrFail($id);

        if (!$this->canApprovePermit($teacher, $permit)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to reject this permit.'
            ], 403);
        }

        $result = $this->ePermitService->rejectRequest($permit, $teacher, $request->input('reason'));

        return response()->json($result);
    }

    /**
     * Check if teacher can access the permit (view only)
     */
    protected function canAccessPermit($teacher, $permit): bool
    {
        switch ($teacher->role_id) {
            case 4: // Class Teacher
                // Check if this teacher is ANY class teacher for this student
                return $this->ePermitService->isClassTeacher($permit->student, $teacher);

            case 3: // Academic Teacher
                // ANY academic teacher can access
                return $this->ePermitService->isAcademicTeacher($teacher);

            case 2: // Head Teacher
                // ANY head teacher can access all permits
                return $this->ePermitService->isHeadTeacher($teacher);

            default:
                return false;
        }
    }

    /**
     * Show return check-in form
     */
    public function returnForm(): View
    {
        $teacher = $this->getTeacher();

        // Only academic teachers (role_id=3) and head teachers (role_id=2) can check-in returning students
        if (!in_array($teacher->role_id, [2, 3])) {
            // abort(403, 'Unauthorized. Only Academic Teacher and Head Teacher can check-in returning students.');
            return redirect()->to_route('error.page');
        }

        return view('teacher.e-permit.return-form', [
            'teacher' => $teacher
        ]);
    }

    /**
     * Search for permit to confirm return
     */
    public function searchReturn(Request $request): JsonResponse
    {
        $request->validate([
            'search' => 'required|string'
        ]);

        $search = $request->search;

        $permit = EPermit::with(['student', 'student.class'])
            ->where('permit_number', 'like', "%{$search}%")
            ->orWhereHas('student', function ($q) use ($search) {
                $q->where('admission_number', 'like', "%{$search}%")
                    ->orWhere('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%");
            })
            ->where('status', 'approved')
            ->whereNull('verified_at')
            ->first();

        if (!$permit) {
            return response()->json([
                'success' => false,
                'message' => 'No approved permit found for this student. Please check and try again.'
            ]);
        }

        // Check if return date has passed
        $isLate = now()->startOfDay() > $permit->expected_return_date;

        return response()->json([
            'success' => true,
            'permit' => [
                'id' => $permit->id,
                'permit_number' => $permit->permit_number,
                'student' => [
                    'id' => $permit->student->id,
                    'name' => ucwords(strtolower($permit->student->first_name . ' ' . $permit->student->last_name)),
                    'admission_number' => strtoupper($permit->student->admission_number),
                    'class' => strtoupper($permit->student->class->class_name ?? 'N/A'),
                    'image' => $permit->student->image
                ],
                'guardian_name' => ucwords(strtolower($permit->guardian_name)),
                'guardian_phone' => $permit->guardian_phone,
                'departure_date' => $permit->departure_date->format('Y-m-d'),
                'expected_return_date' => $permit->expected_return_date->format('Y-m-d'),
                'is_late' => $isLate
            ]
        ]);
    }

    /**
     * Confirm student return
     */
    public function confirmReturn(Request $request, $id): JsonResponse
    {
        $request->validate([
            'returned_alone' => 'required|boolean',
            'accompanied_by_name' => 'required_if:returned_alone,0|nullable|string|max:255',
            'guardian_type' => 'required_if:returned_alone,0|nullable|string|in:parent,guardian',
            'relationship' => 'required_if:returned_alone,0|nullable|string|max:50',
            'late_reason' => 'required_if:is_late,1|nullable|string|max:500'
        ]);

        $teacher = $this->getTeacher();
        $permit = EPermit::findOrFail($id);

        // Verify permit is approved and not yet verified
        if ($permit->status !== 'approved') {
            return response()->json([
                'success' => false,
                'message' => 'Only approved permits can be verified for return.'
            ], 422);
        }

        if ($permit->verified_at) {
            return response()->json([
                'success' => false,
                'message' => 'This permit has already been verified for return.'
            ], 422);
        }

        $returnData = [
            'actual_return_date' => now(),
            'returned_alone' => $request->returned_alone,
            'accompanied_by_name' => $request->returned_alone ? null : $request->accompanied_by_name,
            'guardian_type' => $request->returned_alone ? null : $request->guardian_type,
            'relationship' => $request->returned_alone ? null : $request->relationship,
            'late_reason' => $request->late_reason ?? null,
            'is_late' => $request->is_late ?? false
        ];

        $result = $this->ePermitService->completeReturn($permit, $teacher, $returnData);

        return response()->json($result);
    }

    /**
     * Reports page - Only for Academic (role_id=3) and Head Teacher (role_id=2)
     */
    public function reports(Request $request): View
    {
        $teacher = $this->getTeacher();

        // Only academic teacher and head teacher can view reports
        if (!in_array($teacher->role_id, [2, 3])) {
            // abort(403, 'Unauthorized. Only Academic Teacher and Head Teacher can view reports.');
            return redirect()->to_route('error.page');
        }

        $query = EPermit::with(['student', 'student.class']);

        // Apply filters
        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        if ($request->status && $request->status !== 'all') {
            $query->where('status', $request->status);
        }
        if ($request->class_id) {
            $query->whereHas('student', function ($q) use ($request) {
                $q->where('class_id', $request->class_id);
            });
        }

        $permits = $query->orderBy('created_at', 'desc')->paginate(20);

        // Statistics
        $stats = [
            'total' => EPermit::count(),
            'approved' => EPermit::where('status', 'approved')->count(),
            'rejected' => EPermit::where('status', 'rejected')->count(),
            'completed' => EPermit::where('status', 'completed')->count(),
            'pending' => EPermit::whereIn('status', ['pending_class_teacher', 'pending_duty_teacher', 'pending_academic', 'pending_head'])->count()
        ];

        return view('teacher.e-permit.reports', [
            'permits' => $permits,
            'stats' => $stats,
            'filters' => $request->all(),
            'teacher' => $teacher
        ]);
    }

    /**
     * Export PDF report
     */
    public function exportPDF(Request $request)
    {
        $teacher = $this->getTeacher();

        // Only academic teacher and head teacher can export reports
        if (!in_array($teacher->role_id, [2, 3])) {
            return redirect()->back()->with('error', 'Unauthorized. Only Academic Teacher and Head Teacher can export reports.');
        }

        $query = EPermit::with(['student', 'student.class', 'classTeacher.user', 'academicTeacher.user', 'headTeacher.user']);

        // Apply filters
        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        if ($request->status && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $permits = $query->orderBy('created_at', 'desc')->get();

        // Statistics
        $stats = [
            'total' => $permits->count(),
            'approved' => $permits->where('head_teacher_action', 'approved')->count(),
            'rejected' => $permits->where('status', 'rejected')->count(),
            'completed' => $permits->where('status', 'completed')->count(),
            'pending' => $permits->whereIn('status', ['pending_class_teacher', 'pending_duty_teacher', 'pending_academic', 'pending_head'])->count()
        ];

        $filters = [
            'date_from' => $request->date_from,
            'date_to' => $request->date_to,
            'status' => $request->status ?? 'all'
        ];

        $pdf = Pdf::loadView('teacher.e-permit.reports-pdf', [
            'permits' => $permits,
            'stats' => $stats,
            'filters' => $filters
        ]);

        $pdf->setPaper('A4', 'landscape');

        return $pdf->download('e-permit-report-' . now()->format('Y-m-d-H-i-s') . '.pdf');
    }

    /**
     * Export Excel report
     */
    public function exportExcel(Request $request)
    {
        $teacher = $this->getTeacher();

        // Only academic teacher and head teacher can export reports
        if (!in_array($teacher->role_id, [2, 3])) {
            return redirect()->back()->with('error', 'Unauthorized. Only Academic Teacher and Head Teacher can export reports.');
        }

        $query = EPermit::with(['student', 'student.class', 'classTeacher.user', 'academicTeacher.user', 'headTeacher.user']);

        // Apply filters
        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        if ($request->status && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $permits = $query->orderBy('created_at', 'desc')->get();

        $filters = [
            'date_from' => $request->date_from,
            'date_to' => $request->date_to,
            'status' => $request->status ?? 'all'
        ];

        return Excel::download(new EPermitReportExport($permits, $filters), 'e-permit-report-' . now()->format('Y-m-d-H-i-s') . '.xlsx');
    }

    /**
     * Print gatepass
     */
    public function printGatepass($id)
    {
        $decoded = Hashids::decode($id);
        $teacher = $this->getTeacher();
        $permit = EPermit::with(['student', 'student.class', 'parent'])
            ->findOrFail($decoded[0]);

        // Only approved permits can be printed
        if ($permit->status !== 'approved') {
            abort(403, 'Only approved permits can be printed.');
        }
        $student = Student::findOrFail($permit->student_id);
        $school = school::findOrFail($student->school_id);

        return view('teacher.e-permit.print', [
            'permit' => $permit,
            'teacher' => $teacher,
            'school' => $school
        ]);
    }

    /**
     * Get reports data as JSON
     */
    public function getReportsData(Request $request): JsonResponse
    {
        $teacher = $this->getTeacher();
        if (!in_array($teacher->role_id, [2, 3])) {
            return response()->json(['data' => []]);
        }

        $query = EPermit::with(['student', 'student.class']);

        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        if ($request->status && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $permits = $query->orderBy('created_at', 'desc')->get()->map(function ($permit) {
            return [
                'permit_number' => $permit->permit_number,
                'student_name' => $permit->student->first_name . ' ' . $permit->student->last_name,
                'admission_number' => $permit->student->admission_number,
                'class_name' => $permit->student->class->class_name ?? 'N/A',
                'guardian_name' => $permit->guardian_name,
                'guardian_phone' => $permit->guardian_phone,
                'departure_date' => $permit->departure_date->format('d/m/Y'),
                'expected_return_date' => $permit->expected_return_date->format('d/m/Y'),
                'status' => $permit->status,
                'created_date' => $permit->created_at->format('d/m/Y')
            ];
        });

        return response()->json(['data' => $permits]);
    }

    /**
     * Get workflow timeline
     */
    protected function getWorkflowTimeline($permit): array
    {
        $timeline = [];

        // Submission
        $timeline[] = [
            'stage' => 'Ombi Limewasilishwa',
            'status' => 'submitted',
            'person' => ucwords(strtolower($permit->guardian_name)),
            'time' => $permit->created_at,
            'icon' => 'fa-paper-plane',
            'color' => 'blue'
        ];

        // Class Teacher
        if ($permit->class_teacher_approved_at) {
            $isRejected = $permit->class_teacher_action === 'rejected';
            $timeline[] = [
                'stage' => 'Mwalimu wa Darasa',
                'status' => $isRejected ? 'rejected' : $permit->class_teacher_action,
                'person' => ucwords(strtolower($permit->classTeacher?->user?->first_name . ' ' . $permit->classTeacher?->user?->last_name)),
                'time' => $permit->class_teacher_approved_at,
                'comment' => ucfirst($permit->class_teacher_comment),
                'icon' => 'fa-chalkboard-user',
                'color' => $isRejected ? 'red' : ($permit->class_teacher_action === 'approved' ? 'green' : 'orange')
            ];
        }

        // Duty Teacher - only if exists and approved
        if ($permit->duty_teacher_approved_at) {
            $isRejected = $permit->duty_teacher_action === 'rejected';
            $timeline[] = [
                'stage' => 'Mwalimu wa Zamu',
                'status' => $isRejected ? 'rejected' : $permit->duty_teacher_action,
                'person' => ucwords(strtolower($permit->dutyTeacher?->user?->first_name . ' ' . $permit->dutyTeacher?->user?->last_name)),
                'time' => $permit->duty_teacher_approved_at,
                'comment' => ucfirst($permit->duty_teacher_comment),
                'icon' => 'fa-clock',
                'color' => $isRejected ? 'red' : ($permit->duty_teacher_action === 'approved' ? 'green' : 'orange')
            ];
        } elseif ($permit->status === 'pending_academic' && !$permit->duty_teacher_approved_at && $permit->class_teacher_approved_at) {
            // If no duty teacher was assigned, show as skipped
            $timeline[] = [
                'stage' => 'Mwalimu wa Zamu',
                'status' => 'skipped',
                'person' => 'Hakuna mwalimu wa zamu',
                'time' => $permit->class_teacher_approved_at,
                'comment' => 'Ombi limeenda moja kwa moja kwa Mwalimu wa Taaluma.',
                'icon' => 'fa-forward',
                'color' => 'orange'
            ];
        }

        // Academic Teacher
        if ($permit->academic_teacher_approved_at) {
            $isRejected = $permit->academic_teacher_action === 'rejected';
            $timeline[] = [
                'stage' => 'Mwalimu wa Taaluma',
                'status' => $isRejected ? 'rejected' : $permit->academic_teacher_action,
                'person' => ucwords(strtolower($permit->academicTeacher?->user?->first_name . ' ' . $permit->academicTeacher?->user?->last_name)),
                'time' => $permit->academic_teacher_approved_at,
                'comment' => ucfirst($permit->academic_teacher_comment),
                'icon' => 'fa-book',
                'color' => $isRejected ? 'red' : ($permit->academic_teacher_action === 'approved' ? 'green' : 'orange')
            ];
        }

        // Head Teacher
        if ($permit->head_teacher_approved_at) {
            $isRejected = $permit->head_teacher_action === 'rejected';
            $timeline[] = [
                'stage' => 'Mwalimu Mkuu',
                'status' => $isRejected ? 'rejected' : $permit->head_teacher_action,
                'person' => ucwords(strtolower($permit->headTeacher?->user?->first_name . ' ' . $permit->headTeacher?->user?->last_name)),
                'time' => $permit->head_teacher_approved_at,
                'comment' => ucfirst($permit->head_teacher_comment),
                'icon' => 'fa-user-tie',
                'color' => $isRejected ? 'red' : ($permit->head_teacher_action === 'approved' ? 'green' : 'orange')
            ];
        }

        // ============ REMOVED: Rejection summary from timeline ============
        // Tuna rejection card tofauti, hivyo hatuitaji hii tena kwenye timeline
        // Ili kuepuka kuonyesha mara mbili

        // Return/Complete
        if ($permit->verified_at) {
            $timeline[] = [
                'stage' => 'Kurudi Shuleni',
                'status' => 'completed',
                'person' => ucwords(strtolower($permit->verifier?->user?->first_name . ' ' . $permit->verifier?->user?->last_name)),
                'time' => $permit->verified_at,
                'icon' => 'fa-check-double',
                'color' => 'green'
            ];
        }

        return $timeline;
    }
}
