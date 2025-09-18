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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Vinkla\Hashids\Facades\Hashids;
use RealRashid\SweetAlert\Facades\Alert;

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
                            ->orderBy('tod_rosters.updated_at', 'DESC')
                            ->orderBy('tod_rosters.start_date', 'ASC')
                            ->get()
                            ->groupBy('start_date');
        // return $rosters;

        $teachers = Teacher::query()
                            ->join('users', 'users.id', '=', 'teachers.user_id')
                            ->select('teachers.id', 'users.first_name', 'users.last_name')
                            ->whereIn('role_id', [1, 4])
                            ->where('teachers.status', 1)
                            ->orderBy('users.first_name')
                            ->get();
        return view('duty_roster.index', compact('rosters', 'teachers'));
    }

    public function destroy($id)
    {
        $roster = TodRoster::findOrFail($id);

        if($roster->status == 'active') {
            Alert()->toast('Cannot delete an active duty roster.', 'error');
            return redirect()->route('tod.roster.index');
        }

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

            // ✅ Check if any roster in the table is already active
            $alreadyActive = TodRoster::where('status', 'active')->exists();

            if ($alreadyActive) {
                Alert()->toast('There is active roster, deactivate it first', 'warning');
                return redirect()->route('tod.roster.index');
            }

            // ✅ Since no active roster exists, activate all with the same roster_id
            $rosterId = $roster->roster_id;
            $allRostersWithSameId = TodRoster::where('roster_id', $rosterId)->get();

            foreach ($allRostersWithSameId as $row) {
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
                // \Log::info('Attendance data found: ' . json_encode($request->attendance));

                foreach ($request->attendance as $key => $values) {
                    // Tenga class_id na stream kutoka kwenye key
                    $keyParts = explode('_', $key);

                    // Hakikisha key ina sehemu mbili (class_id na stream)
                    if (count($keyParts) < 2) {
                        // \Log::info('Skipping invalid key format: ' . $key);
                        continue;
                    }

                    $classId = $keyParts[0];
                    $stream = $keyParts[1];

                    // Skip rows with invalid class_id
                    if ($classId === 'undefined' || $classId === 'total' || !is_numeric($classId)) {
                        // \Log::info('Skipping invalid class ID: ' . $classId);
                        continue;
                    }

                    // \Log::info('Processing class ID: ' . $classId . ', stream: ' . $stream . ' with values: ' . json_encode($values));

                    try {
                        $attendance = daily_report_attendance::create([
                            'daily_report_id'  => $report->id,
                            'class_id'         => (int)$classId,
                            'group'            => $stream, // Tumia stream from the key
                            'registered_boys'  => isset($values['registered_boys']) ? (int)$values['registered_boys'] : 0,
                            'registered_girls' => isset($values['registered_girls']) ? (int)$values['registered_girls'] : 0,
                            'present_boys'     => isset($values['present_boys']) ? (int)$values['present_boys'] : 0,
                            'present_girls'    => isset($values['present_girls']) ? (int)$values['present_girls'] : 0,
                            'absent_boys'      => isset($values['absent_boys']) ? (int)$values['absent_boys'] : 0,
                            'absent_girls'     => isset($values['absent_girls']) ? (int)$values['absent_girls'] : 0,
                            'permission_boys'  => isset($values['permission_boys']) ? (int)$values['permission_boys'] : 0,
                            'permission_girls' => isset($values['permission_girls']) ? (int)$values['permission_girls'] : 0,
                        ]);

                        // \Log::info('Successfully saved attendance for class ID: ' . $classId . ', stream: ' . $stream);
                    } catch (\Exception $e) {
                        // \Log::error('Error saving attendance for class ID ' . $classId . ', stream: ' . $stream . ': ' . $e->getMessage());
                        // Continue with other records instead of throwing error
                        continue;
                    }
                }
            } else {
                // \Log::info('No attendance data found in request');
                Alert()->toast('No attendance data provided.', 'warning');
                return back();
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
        $reports = daily_report_attendance::query()
                                ->join('daily_report_details', 'daily_report_attendances.daily_report_id', '=', 'daily_report_details.id')
                                ->leftJoin('tod_rosters', 'daily_report_details.tod_roster_id', '=', 'tod_rosters.id')
                                ->leftJoin('teachers', 'tod_rosters.teacher_id', '=', 'teachers.id')
                                ->leftJoin('users', 'teachers.user_id', '=', 'users.id')
                                ->selectRaw('
                                    daily_report_details.report_date,
                                    SUM(daily_report_attendances.registered_boys) as registered_boys,
                                    SUM(daily_report_attendances.registered_girls) as registered_girls,
                                    SUM(daily_report_attendances.present_boys) as present_boys,
                                    SUM(daily_report_attendances.present_girls) as present_girls,
                                    tod_rosters.roster_id,
                                    users.first_name,
                                    users.last_name,
                                    daily_report_details.status
                                ')
                                ->where('daily_report_details.status', 'pending')
                                ->groupBy('tod_rosters.roster_id', 'users.first_name', 'users.last_name', 'daily_report_details.report_date', 'daily_report_details.status')
                                ->orderBy('daily_report_details.report_date', 'desc')
                                ->get();

        // return $reports;
        $totalRegistered = Student::where('status', 1)->count();
        $reportSummary = daily_report_attendance::query()
                                    ->join('daily_report_details', 'daily_report_attendances.daily_report_id', '=', 'daily_report_details.id')
                                    ->select('daily_report_attendances.*', 'daily_report_details.report_date')
                                    ->where('daily_report_details.report_date', $today);
        return view('duty_roster.school_report',compact('reports', 'totalRegistered', 'pendingReports', 'today', 'reportSummary'));
    }


    public function reportByDate($date)
    {
        $reportDetails = daily_report_details::where('report_date', $date)->first();
        // return $reportDetails;
        $roster = TodRoster::findOrFail($reportDetails->tod_roster_id);
        // return $roster;

        $dailyAttendance = daily_report_attendance::query()
                            ->join('grades', 'grades.id', '=', 'daily_report_attendances.class_id')
                            ->select('daily_report_attendances.*', 'grades.class_code')
                            ->where('daily_report_id', $reportDetails->id)
                            ->orderBy('grades.class_code', 'ASC')
                            ->orderBY('daily_report_attendances.group', 'ASC')
                            ->get();
        $user = Auth::user();
        $school = school::findOrFail($user->school_id);
        return view('duty_roster.report_preview', compact('reportDetails', 'roster', 'dailyAttendance', 'school'));

    }

    public function destroyReport ($date)
    {
        $reportDetails = daily_report_details::where('report_date', $date)->first();
        // return $reportDetails;
        if(!$reportDetails) {
            Alert()->toast('Failed to get report details', 'error');
            return back();
        }
        $dailyAttendance = daily_report_attendance::where('daily_report_id', $reportDetails->id)->get();
        if($dailyAttendance) {
            foreach($dailyAttendance as $row) {
                $row->delete();
            }
        }

        $reportDetails->delete();

        Alert()->toast('Daily report deleted successfully', 'success');
        return redirect()->route('get.school.report');
    }

    public function updateDailyReport(Request $request, $id)
    {
        $decode = Hashids::decode($id);
        $report = daily_report_details::findOrFail($decode[0]);

        if(!$report) {
            Alert()->toast('Failed to get report details', 'error');
            return back();
        }

        $user = Auth::user();

        $this->validate($request, [
            'parade' => 'sometimes|string|max:255',
            'break_time' => 'sometimes|string|max:255',
            'lunch_time' => 'sometimes|string|max:255',
            'teachers_attendance' => 'sometimes|string|max:255',
            'daily_new_event' => 'nullable|sometimes|string|max:255',
            'tod_remarks' => 'sometimes|string|max:255',
            'headteacher_comment' => 'required|string|max:255',
        ]);

        $report->update([
            'parade' => $request->parade,
            'break_time' => $request->break_time,
            'lunch_time' => $request->lunch_time,
            'teacher_attendance' => $request->teachers_attendance,
            'daily_new_event' => $request->daily_new_event ?? '',
            'tod_remarks' => $request->tod_remarks,
            'headteacher_comment' => $request->headteacher_comment,
            'status' => 'approved',
            'approved_by' => $user->first_name . ' '. $user->last_name
        ]);

        Alert()->toast('Daily report has been approved and submitted successfully', 'success');
        return redirect()->route('get.school.report');
    }

    public function viewReport(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date'   => 'required|date'
        ]);

        // Pata report zote kwenye date range
        $reports = daily_report_details::query()
                    ->join('tod_rosters', 'tod_rosters.id', '=', 'daily_report_details.tod_roster_id')
                    ->whereBetween('report_date', [$request->start_date, $request->end_date])
                    ->where('daily_report_details.status', 'approved')
                    ->select(
                        'daily_report_details.*',
                        'tod_rosters.roster_id',
                        'tod_rosters.teacher_id',
                        'tod_rosters.start_date',
                        'tod_rosters.end_date'
                    )
                    ->orderBy('report_date', 'ASC')
                    ->get();

        if ($reports->isEmpty()) {
            Alert::info('Info', 'No records were found for the selected dates');
            // Alert()->toast('No records for the selected date range', 'info');
            return back();
        }

        // Group attendance per report_id
        $reportsWithAttendance = $reports->map(function ($report) {
            $attendance = daily_report_attendance::query()
                            ->join('grades', 'grades.id', '=', 'daily_report_attendances.class_id')
                            ->join('daily_report_details', 'daily_report_details.id', '=', 'daily_report_attendances.daily_report_id')
                            ->leftJoin('tod_rosters', 'tod_rosters.id', '=', 'daily_report_details.tod_roster_id')
                            ->leftJoin('teachers', 'teachers.id', '=', 'tod_rosters.teacher_id')
                            ->leftJoin('users', 'users.id', '=', 'teachers.user_id')
                            ->select(
                                'daily_report_attendances.*',
                                'grades.class_name', 'grades.class_code',
                                'tod_rosters.roster_id', 'tod_rosters.teacher_id',
                                'teachers.member_id', 'users.first_name', 'users.last_name'
                            )
                            ->where('daily_report_id', $report->id)
                            ->orderBy('class_id', 'ASC')
                            ->get();

            return [
                'report'     => $report,
                'attendance' => $attendance,
            ];
        });

        return view('duty_roster.general_report', compact('reportsWithAttendance'));
    }

}
