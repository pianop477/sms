<?php

namespace App\Http\Controllers;

use App\Models\daily_report_attendance;
use App\Models\daily_report_details;
use App\Models\DailyReport;
use App\Models\DailyReportAttendance;
use App\Models\Grade;
use App\Models\school;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\TodRoster;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\View\Components\Alert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TodRosterController extends Controller
{
    /**
     * Assign teachers to duty roster
     */
    public function assignTeachers(Request $request)
    {
        $validated = $request->validate([
            'teacher_ids' => 'required|array|min:1',
            'teacher_ids.*' => 'exists:teachers,id',
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
        ],
        [
            'teacher_ids.required' => 'Please select at least one teacher.',
            'teacher_ids.array' => 'Invalid teacher selection.',
            'teacher_ids.*.exists' => 'Selected teacher does not exist.',
            'start_date.required' => 'Start date is required.',
            'end_date.required' => 'End date is required.',
            'end_date.after_or_equal' => 'End date must be after or equal to start date.',
        ]);

        $assignments = [];
        $authUser = Auth::user();
        // return $authUser;

        $school = school::findOrFail($authUser->school_id);
        $randomNumber = rand(0, 1000);
        $rosterId = $school->abbriv_code. '-'.  date('Y') . '-'. str_pad($randomNumber, 4, '0', STR_PAD_LEFT);
        foreach ($validated['teacher_ids'] as $teacherId) {
                $assignments[] = TodRoster::create([
                    'roster_id' => $rosterId,
                    'teacher_id' => $teacherId,
                    'start_date' => $validated['start_date'],
                    'end_date'   => $validated['end_date'],
                    'created_by' => $authUser->first_name . ' ' . $authUser->last_name,
                    'updated_by' => $authUser->first_name . ' ' . $authUser->last_name,
                ]);
        }

        Alert()->toast('Teachers assigned successfully!', 'success');
        return redirect()->route('tod.roster.index');
    }

    public function index()
    {
        $rosters = TodRoster::query()
                            ->join('teachers', 'teachers.id', '=', 'tod_rosters.teacher_id')
                            ->leftJoin('users', 'users.id', '=', 'teachers.user_id')
                            ->select('tod_rosters.*', 'users.first_name', 'users.last_name', 'users.email', 'teachers.id as teacher_id')
                            ->orderBy('tod_rosters.start_date', 'asc')
                            ->get()
                            ->groupBy('start_date');
        // return $rosters;

        $teachers = Teacher::query()
                            ->join('users', 'users.id', '=', 'teachers.user_id')
                            ->select('teachers.id', 'users.first_name', 'users.last_name')
                            ->whereIn('role_id', [1, 4])
                            ->orderBy('users.first_name')
                            ->get();
        return view('duty_roster.index', compact('rosters', 'teachers'));
    }

    public function destroy($id)
    {
        $roster = TodRoster::findOrFail($id);
        $rosterId = $roster->roster_id;
        $allRostersWithSameId = TodRoster::where('roster_id', $rosterId)->get();

        foreach ($allRostersWithSameId as $row) {
            // Delete each roster with the same roster_id
            $row->delete();
        }
        Alert()->toast('Duty roster deleted successfully!', 'success');
        return redirect()->route('tod.roster.index');
    }


    public function activate($id)
    {
        try {
            $roster = TodRoster::findOrFail($id);
            $rosterId = $roster->roster_id;
            $allRostersWithSameId = TodRoster::where('roster_id', $rosterId)->get();
            foreach ($allRostersWithSameId as $row) {
                // Update each roster with the same roster_id
                $row->status = 'active';
                $row->updated_by = Auth::user()->first_name . ' ' . Auth::user()->last_name;
                $row->save();
            }

            Alert()->toast('Duty roster activated successfully!', 'success');
        } catch (Exception $e) {
            Alert()->toast('Error activating duty roster: ' . $e->getMessage(), 'error');
        }

        return redirect()->route('tod.roster.index');
    }

    public function create()
    {
        $today = Carbon::today()->format('Y-m-d');
        $reports = daily_report_details::where('report_date', $today)->get();
        return view('duty_roster.daily_report_particulars', compact('reports'));
    }

    public function fetchAttendance()
    {
        $authUser = Auth::user();
        $schoolId = $authUser->school_id;
        $today = Carbon::today()->format('Y-m-d');

        // Step 1: Pata classes zote (grades) na group ya students, hata kama registered=0
        $classes = DB::table('grades')
            ->leftJoin('students', function($join) use ($schoolId) {
                $join->on('students.class_id', '=', 'grades.id')
                    ->where('students.school_id', $schoolId)
                    ->where('students.status', 1);
            })
            ->select(
                'grades.id as class_id',
                'grades.class_code',
                'students.group as stream',
                DB::raw('SUM(CASE WHEN students.gender = "Male" THEN 1 ELSE 0 END) as registered_boys'),
                DB::raw('SUM(CASE WHEN students.gender = "Female" THEN 1 ELSE 0 END) as registered_girls')
            )
            ->groupBy('grades.id', 'grades.class_code', 'students.group')
            ->orderBy('grades.class_code')
            ->orderBy('stream')
            ->get();

        $response = [];
        $totals = [
            'registered_boys' => 0,
            'registered_girls' => 0,
            'attended_boys' => 0,
            'attended_girls' => 0,
            'absent_boys' => 0,
            'absent_girls' => 0,
            'permission_boys' => 0,
            'permission_girls' => 0,
        ];

        foreach ($classes as $class) {
            $classId = $class->class_id; // sasa ipo sahihi
            $classCode = $class->class_code;
            $stream = $class->stream ?? '';

            // Step 2: Attendance counts (present, absent, permission)
            $attended_boys = DB::table('attendances')
                ->join('students', 'attendances.student_id', '=', 'students.id')
                ->where('attendances.school_id', $schoolId)
                ->whereDate('attendances.attendance_date', $today)
                ->where('attendances.class_group', $stream)
                ->where('attendances.class_id', $classId)
                ->where('attendances.attendance_status', 'present')
                ->where('students.gender', 'Male')
                ->count();

            $attended_girls = DB::table('attendances')
                ->join('students', 'attendances.student_id', '=', 'students.id')
                ->where('attendances.school_id', $schoolId)
                ->whereDate('attendances.attendance_date', $today)
                ->where('attendances.class_group', $stream)
                ->where('attendances.class_id', $classId)
                ->where('students.gender', 'Female')
                ->where('attendances.attendance_status', 'present')
                ->count();

            $absent_boys = DB::table('attendances')
                ->join('students', 'attendances.student_id', '=', 'students.id')
                ->where('attendances.school_id', $schoolId)
                ->whereDate('attendances.attendance_date', $today)
                ->where('attendances.class_group', $stream)
                ->where('attendances.class_id', $classId)
                ->where('students.gender', 'Male')
                ->where('attendances.attendance_status', 'absent')
                ->count();

            $absent_girls = DB::table('attendances')
                ->join('students', 'attendances.student_id', '=', 'students.id')
                ->where('attendances.school_id', $schoolId)
                ->whereDate('attendances.attendance_date', $today)
                ->where('attendances.class_group', $stream)
                ->where('attendances.class_id', $classId)
                ->where('students.gender', 'Female')
                ->where('attendances.attendance_status', 'absent')
                ->count();

            $permission_boys = DB::table('attendances')
                ->join('students', 'attendances.student_id', '=', 'students.id')
                ->where('attendances.school_id', $schoolId)
                ->whereDate('attendances.attendance_date', $today)
                ->where('attendances.class_group', $stream)
                ->where('attendances.class_id', $classId)
                ->where('students.gender', 'Male')
                ->where('attendances.attendance_status', 'permission')
                ->count();

            $permission_girls = DB::table('attendances')
                ->join('students', 'attendances.student_id', '=', 'students.id')
                ->where('attendances.school_id', $schoolId)
                ->whereDate('attendances.attendance_date', $today)
                ->where('attendances.class_group', $stream)
                ->where('attendances.class_id', $classId)
                ->where('students.gender', 'Female')
                ->where('attendances.attendance_status', 'permission')
                ->count();

            $response[] = [
                'class_id' => $classId,
                'class_code' => $classCode,
                'stream' => $stream,
                'registered_boys' => $class->registered_boys ?? 0,
                'registered_girls' => $class->registered_girls ?? 0,
                'attended_boys' => $attended_boys,
                'attended_girls' => $attended_girls,
                'absent_boys' => $absent_boys,
                'absent_girls' => $absent_girls,
                'permission_boys' => $permission_boys,
                'permission_girls' => $permission_girls,
            ];

            // Update totals
            $totals['registered_boys'] += $class->registered_boys ?? 0;
            $totals['registered_girls'] += $class->registered_girls ?? 0;
            $totals['attended_boys'] += $attended_boys;
            $totals['attended_girls'] += $attended_girls;
            $totals['absent_boys'] += $absent_boys;
            $totals['absent_girls'] += $absent_girls;
            $totals['permission_boys'] += $permission_boys;
            $totals['permission_girls'] += $permission_girls;
        }

        // TOTAL row
        $response[] = [
            'class_code' => 'TOTAL',
            'stream' => '',
            'registered_boys' => $totals['registered_boys'],
            'registered_girls' => $totals['registered_girls'],
            'attended_boys' => $totals['attended_boys'],
            'attended_girls' => $totals['attended_girls'],
            'absent_boys' => $totals['absent_boys'],
            'absent_girls' => $totals['absent_girls'],
            'permission_boys' => $totals['permission_boys'],
            'permission_girls' => $totals['permission_girls'],
        ];

        return response()->json($response);
    }

    public function store(Request $request)
    {
        // ✅ validate inputs
        $request->validate([
            'report_date'          => 'required|date',
            'parade'               => 'required|string',
            'break_time'           => 'required|string',
            'lunch_time'           => 'required|string',
            'teachers_attendance'  => 'required|string',
            'tod_remarks'          => 'required|string',
            'attendance'           => 'nullable|array',
        ]);

        DB::beginTransaction();

        try {
            // 1️⃣ Pata active tod_roster ya mwalimu aliye login
            $user = Auth::user();
            if(!$user) {
                Alert()->toast('User not authorized.', 'error');
                return back();
            }

            $teacher = Teacher::where('user_id', $user->id)->first();
            if (!$teacher) {
                Alert()->toast('No teacher profile found for the logged-in user.', 'error');
                return back();
            }

            $todRoster = TodRoster::where('teacher_id', $teacher->id)->where('status', 'active')->first();
            if (!$todRoster) {
                Alert()->toast('No active Teacher On Duty roster found.', 'error');
                return back();
            }

            // Check for existing report for the same date and teacher
            $existingReport = daily_report_details::where('report_date', $request->report_date)
                ->where('tod_roster_id', $todRoster->id)
                ->exists();

            if ($existingReport) {
                Alert()->toast('A report for this date already submitted.', 'error');
                return back();
            }

            // 2️⃣ Hifadhi daily report particulars
            $report = daily_report_details::create([
                'tod_roster_id'        => $todRoster->id,
                'report_date'          => $request->report_date,
                'parade'               => $request->parade,
                'break_time'           => $request->break_time,
                'lunch_time'           => $request->lunch_time,
                'teachers_attendance'  => $request->teachers_attendance,
                'daily_new_event'      => $request->daily_new_event,
                'tod_remarks'          => $request->tod_remarks,
            ]);

            // 3️⃣ Hifadhi attendance records ikiwa zipo
            if ($request->has('attendance')) {
                // Log::info('Attendance data found: ' . json_encode($request->attendance));

                foreach ($request->attendance as $classId => $values) {
                    // Skip total row or invalid class IDs
                    if ($classId === 'total' || !is_numeric($classId)) {
                        // Log::info('Skipping class ID: ' . $classId);
                        continue;
                    }

                    // Log::info('Processing class ID: ' . $classId . ' with values: ' . json_encode($values));

                    try {
                        $attendance = daily_report_attendance::create([
                            'daily_report_id'  => $report->id,
                            'class_id'         => (int)$classId,
                            'group'            => $values['group'] ?? null,
                            'registered_boys'  => isset($values['registered_boys']) ? (int)$values['registered_boys'] : 0,
                            'registered_girls' => isset($values['registered_girls']) ? (int)$values['registered_girls'] : 0,
                            'present_boys'     => isset($values['present_boys']) ? (int)$values['present_boys'] : 0,
                            'present_girls'    => isset($values['present_girls']) ? (int)$values['present_girls'] : 0,
                            'absent_boys'      => isset($values['absent_boys']) ? (int)$values['absent_boys'] : 0,
                            'absent_girls'     => isset($values['absent_girls']) ? (int)$values['absent_girls'] : 0,
                            'permission_boys'  => isset($values['permission_boys']) ? (int)$values['permission_boys'] : 0,
                            'permission_girls' => isset($values['permission_girls']) ? (int)$values['permission_girls'] : 0,
                        ]);

                        // Log::info('Successfully saved attendance for class ID: ' . $classId);
                    } catch (\Exception $e) {
                        // Log::error('Error saving attendance for class ID ' . $classId . ': ' . $e->getMessage());
                        throw $e;
                    }
                }
            } else {
                // Log::info('No attendance data found in request');
                Alert()->toast('No attendance data provided.', 'info');
                return redirect()->route('tod.report.create');
            }

            DB::commit();

            Alert()->toast('Daily report submitted successfully.', 'success');
            return redirect()->route('tod.report.create');

        } catch (\Exception $e) {
            DB::rollBack();
            // Log::error('Failed to save report: ' . $e->getMessage());
            Alert()->toast('Failed to save report: ' . $e->getMessage(), 'error');
            return back();
        }
    }

    public function getSchoolReport()
    {
        $today = Carbon::today()->format('Y-m-d');
        $pendingReports = daily_report_details::where('status', 'pending')->count();
        $reports = daily_report_attendance::get();
        $totalRegistered = Student::where('status', 1)->count();
        $reportSummary = daily_report_attendance::query()
                                    ->join('dailY_report_details', 'daily_report_attendances.daily_report_id', '=', 'daily_report_details.id')
                                    ->select('daily_report_attendances.*', 'daily_report_details.report_date')
                                    ->where('daily_report_details.report_date', $today);
        return view('duty_roster.school_report',compact('reports', 'totalRegistered', 'pendingReports', 'today', 'reportSummary'));
    }

}
