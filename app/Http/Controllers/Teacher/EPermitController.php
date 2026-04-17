<?php
// app/Http/Controllers/Teacher/EPermitController.php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\EPermit;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\TodRoster;
use App\Services\EPermitService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
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
            abort(403, 'Unauthorized. You do not have permission to access e-Permit system.');
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

    protected function getPendingPermits($teacher)
    {
        $query = EPermit::with(['student', 'student.class']);

        switch ($teacher->role_id) {
            case 4: // Class Teacher
                $query->where('status', 'pending_class_teacher')
                    ->where('class_teacher_id', $teacher->id);
                break;
            case 3: // Academic Teacher
                $query->where(function ($q) {
                    $q->where('status', 'pending_academic')
                        ->orWhere('status', 'pending_duty_teacher');
                });
                break;
            case 2: // Head Teacher
                $query->where('status', 'pending_head')
                    ->where('head_teacher_id', $teacher->id);
                break;
        }

        return $query->orderBy('created_at', 'desc')->paginate(15);
    }

    protected function getHistoryPermits($teacher)
    {
        $query = EPermit::with(['student', 'student.class']);

        switch ($teacher->role_id) {
            case 4: // Class Teacher
                $query->where('class_teacher_id', $teacher->id)
                    ->whereIn('status', ['approved', 'rejected', 'completed']);
                break;
            case 3: // Academic Teacher
                $query->where(function ($q) use ($teacher) {
                    $q->where('academic_teacher_id', $teacher->id)
                        ->orWhere('duty_teacher_id', $teacher->id);
                })->whereIn('status', ['approved', 'rejected', 'completed']);
                break;
            case 2: // Head Teacher
                $query->where('head_teacher_id', $teacher->id)
                    ->whereIn('status', ['approved', 'rejected', 'completed']);
                break;
        }

        return $query->orderBy('created_at', 'desc')->paginate(15);
    }

    /**
     * Get permits based on teacher's role
     */
    protected function getPermitsByTeacherRole($teacher, $status, $search = null, $dateFrom = null, $dateTo = null)
    {
        $query = EPermit::with(['student', 'student.class', 'parent']);

        // Determine which permits to show based on teacher role
        switch ($teacher->role_id) {
            case 4: // Class Teacher - show permits where they are the assigned class teacher
                if ($status === 'pending') {
                    $query->where('status', 'pending_class_teacher')
                        ->where('class_teacher_id', $teacher->id);
                } elseif ($status === 'history') {
                    $query->where(function ($q) use ($teacher) {
                        $q->where('class_teacher_id', $teacher->id)
                            ->whereIn('status', ['approved', 'rejected', 'completed', 'pending_duty_teacher', 'pending_academic', 'pending_head']);
                    });
                }
                break;

            case 3: // Academic Teacher - show permits pending academic and can also see duty teacher permits
                if ($status === 'pending') {
                    $query->where(function ($q) use ($teacher) {
                        $q->where('status', 'pending_academic')
                            ->where('academic_teacher_id', $teacher->id)
                            ->orWhere(function ($q2) {
                                $q2->where('status', 'pending_duty_teacher');
                            });
                    });
                } elseif ($status === 'history') {
                    $query->where(function ($q) use ($teacher) {
                        $q->where('academic_teacher_id', $teacher->id)
                            ->orWhere('status', 'approved')
                            ->orWhere('status', 'rejected')
                            ->orWhere('status', 'completed');
                    });
                }
                break;

            case 2: // Head Teacher - show permits pending head teacher approval
                if ($status === 'pending') {
                    $query->where('status', 'pending_head')
                        ->where('head_teacher_id', $teacher->id);
                } elseif ($status === 'history') {
                    $query->where(function ($q) use ($teacher) {
                        $q->where('head_teacher_id', $teacher->id)
                            ->whereIn('status', ['approved', 'rejected', 'completed']);
                    });
                }
                break;
        }

        // Apply search filter
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('permit_number', 'like', "%{$search}%")
                    ->orWhereHas('student', function ($q) use ($search) {
                        $q->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('admission_number', 'like', "%{$search}%");
                    });
            });
        }

        // Apply date filters
        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        return $query->orderBy('created_at', 'desc')->paginate(15);
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
                $stats['pending'] = EPermit::where('status', 'pending_class_teacher')
                    ->where('class_teacher_id', $teacher->id)->count();
                $stats['approved'] = EPermit::where('class_teacher_id', $teacher->id)
                    ->where('status', 'approved')->count();
                $stats['rejected'] = EPermit::where('class_teacher_id', $teacher->id)
                    ->where('status', 'rejected')->count();
                $stats['completed'] = EPermit::where('class_teacher_id', $teacher->id)
                    ->where('status', 'completed')->count();
                break;

            case 3: // Academic Teacher
                $stats['pending'] = EPermit::where('status', 'pending_academic')
                    ->where('academic_teacher_id', $teacher->id)->count();
                $stats['approved'] = EPermit::where('academic_teacher_id', $teacher->id)
                    ->where('status', 'approved')->count();
                $stats['rejected'] = EPermit::where('academic_teacher_id', $teacher->id)
                    ->where('status', 'rejected')->count();
                $stats['completed'] = EPermit::where('academic_teacher_id', $teacher->id)
                    ->where('status', 'completed')->count();
                break;

            case 2: // Head Teacher
                $stats['pending'] = EPermit::where('status', 'pending_head')
                    ->where('head_teacher_id', $teacher->id)->count();
                $stats['approved'] = EPermit::where('head_teacher_id', $teacher->id)
                    ->where('status', 'approved')->count();
                $stats['rejected'] = EPermit::where('head_teacher_id', $teacher->id)
                    ->where('status', 'rejected')->count();
                $stats['completed'] = EPermit::where('head_teacher_id', $teacher->id)
                    ->where('status', 'completed')->count();
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
        $decoded = Hashids::decode($id);
        $teacher = $this->getTeacher();
        $permit = EPermit::with(['student', 'student.class', 'parent', 'classTeacher.user', 'dutyTeacher.user', 'academicTeacher.user', 'headTeacher.user', 'trackingLogs.teacher.user'])
            ->findOrFail($decoded[0]);

        if (!$this->canAccessPermit($teacher, $permit)) {
            abort(403, 'Unauthorized access to this permit.');
        }

        $timeline = $this->getWorkflowTimeline($permit);

        // Check if duty teacher exists for this permit's departure date
        $dutyTeachers = $this->ePermitService->findDutyTeachersForDate($permit->departure_date);
        $hasDutyTeacher = !empty($dutyTeachers);

        // Check if this permit is at class teacher stage and there's no duty teacher
        $autoSkipToAcademic = ($permit->status === 'pending_class_teacher' && !$hasDutyTeacher);
        $canApprove = $this->canAccessPermit($teacher, $permit);

        return view('teacher.e-permit.show', [
            'permit' => $permit,
            'timeline' => $timeline,
            'teacher' => $teacher,
            'canApprove' => $canApprove,
            'autoSkipToAcademic' => $autoSkipToAcademic,
            'hasDutyTeacher' => $hasDutyTeacher
        ]);
    }

    /**
     * Approve permit
     */
    public function approve(Request $request, $id): JsonResponse
    {
        $teacher = $this->getTeacher();
        $permit = EPermit::findOrFail($id);

        if (!$this->canAccessPermit($teacher, $permit)) {
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

        if (!$this->canAccessPermit($teacher, $permit)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to reject this permit.'
            ], 403);
        }

        $result = $this->ePermitService->rejectRequest($permit, $teacher, $request->input('reason'));

        return response()->json($result);
    }

    /**
     * Show return check-in form
     */
    public function returnForm(): View
    {
        $teacher = $this->getTeacher();

        // Only academic teachers (role_id=3) and head teachers (role_id=2) can check-in returning students
        if (!in_array($teacher->role_id, [2, 3])) {
            abort(403, 'Unauthorized. Only Academic Teacher and Head Teacher can check-in returning students.');
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
                    'name' => $permit->student->first_name . ' ' . $permit->student->last_name,
                    'admission_number' => $permit->student->admission_number,
                    'class' => $permit->student->class->class_name ?? 'N/A'
                ],
                'departure_date' => $permit->departure_date->format('d/m/Y'),
                'expected_return_date' => $permit->expected_return_date->format('d/m/Y'),
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
            abort(403, 'Unauthorized. Only Academic Teacher and Head Teacher can view reports.');
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
        return redirect()->back()->with('info', 'PDF export feature coming soon.');
    }

    /**
     * Export Excel report
     */
    public function exportExcel(Request $request)
    {
        return redirect()->back()->with('info', 'Excel export feature coming soon.');
    }

    /**
     * Print gatepass
     */
    public function printGatepass($id)
    {
        $teacher = $this->getTeacher();
        $permit = EPermit::with(['student', 'student.class', 'parent'])
            ->findOrFail($id);

        // Only approved permits can be printed
        if ($permit->status !== 'approved') {
            abort(403, 'Only approved permits can be printed.');
        }

        return view('teacher.e-permit.print', [
            'permit' => $permit,
            'teacher' => $teacher
        ]);
    }

    /**
     * Check if teacher can access the permit
     */
    protected function canAccessPermit($teacher, $permit): bool
    {
        switch ($teacher->role_id) {
            case 4: // Class Teacher
                return $permit->class_teacher_id === $teacher->id;
            case 3: // Academic Teacher - can access duty teacher and academic stages
                return $permit->academic_teacher_id === $teacher->id ||
                    $permit->duty_teacher_id === $teacher->id ||
                    $permit->status === 'pending_duty_teacher';
            case 2: // Head Teacher
                return $permit->head_teacher_id === $teacher->id;
            default:
                return false;
        }
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
            'person' => $permit->guardian_name,
            'time' => $permit->created_at,
            'icon' => 'fa-paper-plane',
            'color' => 'blue'
        ];

        // Class Teacher
        if ($permit->class_teacher_approved_at) {
            $timeline[] = [
                'stage' => 'Mwalimu wa Darasa',
                'status' => $permit->class_teacher_action,
                'person' => $permit->classTeacher?->user?->name,
                'time' => $permit->class_teacher_approved_at,
                'comment' => $permit->class_teacher_comment,
                'icon' => 'fa-chalkboard-user',
                'color' => $permit->class_teacher_action === 'approved' ? 'green' : 'red'
            ];
        }

        // Duty Teacher - only if exists and approved
        if ($permit->duty_teacher_approved_at) {
            $timeline[] = [
                'stage' => 'Mwalimu wa Zamu',
                'status' => $permit->duty_teacher_action,
                'person' => $permit->dutyTeacher?->user?->name,
                'time' => $permit->duty_teacher_approved_at,
                'comment' => $permit->duty_teacher_comment,
                'icon' => 'fa-clock',
                'color' => $permit->duty_teacher_action === 'approved' ? 'green' : 'red'
            ];
        } elseif ($permit->status === 'pending_academic' && !$permit->duty_teacher_approved_at && $permit->class_teacher_approved_at) {
            // If no duty teacher was assigned, show as skipped
            $timeline[] = [
                'stage' => 'Mwalimu wa Zamu',
                'status' => 'skipped',
                'person' => 'Hakuna mwalimu wa zamu kwa tarehe hii',
                'time' => $permit->class_teacher_approved_at,
                'comment' => 'Ombi limeenda moja kwa moja kwa Mwalimu wa Taaluma kwa sababu hakuna duty roster kwa tarehe hii.',
                'icon' => 'fa-forward',
                'color' => 'orange'
            ];
        }

        // Academic Teacher
        if ($permit->academic_teacher_approved_at) {
            $timeline[] = [
                'stage' => 'Mwalimu wa Taaluma',
                'status' => $permit->academic_teacher_action,
                'person' => $permit->academicTeacher?->user?->name,
                'time' => $permit->academic_teacher_approved_at,
                'comment' => $permit->academic_teacher_comment,
                'icon' => 'fa-book',
                'color' => $permit->academic_teacher_action === 'approved' ? 'green' : 'red'
            ];
        }

        // Head Teacher
        if ($permit->head_teacher_approved_at) {
            $timeline[] = [
                'stage' => 'Mwalimu Mkuu',
                'status' => $permit->head_teacher_action,
                'person' => $permit->headTeacher?->user?->name,
                'time' => $permit->head_teacher_approved_at,
                'comment' => $permit->head_teacher_comment,
                'icon' => 'fa-user-tie',
                'color' => $permit->head_teacher_action === 'approved' ? 'green' : 'red'
            ];
        }

        // Return/Complete
        if ($permit->verified_at) {
            $timeline[] = [
                'stage' => 'Kurudi Shuleni',
                'status' => 'completed',
                'person' => $permit->verifier?->user?->name,
                'time' => $permit->verified_at,
                'icon' => 'fa-check-double',
                'color' => 'green'
            ];
        }

        return $timeline;
    }
}
