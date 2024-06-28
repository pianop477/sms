<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Class_teacher;
use App\Models\Grade;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\PDF;
use Carbon\Carbon;

class AttendanceController extends Controller
{

    public function index($class)
    {
        $myClass = Class_teacher::findOrFail($class);
        // return $myClass->teacher_id;
        $teacher = Teacher::findOrFail($myClass->teacher_id);
        // return $teacher->id;
        $student_class = Grade::findOrFail($myClass->class_id);
        // return $student_class->id;
        $studentList = Student::where('class_id', '=', $student_class->id)
                                ->where('group','=', $myClass->group)
                                ->where('status', '=', 1)
                                ->where('school_id', '=', Auth::user()->school_id)
                                // ->orderBy('first_name', 'ASC')
                                ->orderBy('gender', 'ASC')
                                ->orderBy('first_name', 'ASC')
                                ->get();
        return view('Attendance.index', ['myClass' => $myClass, 'teacher' => $teacher, 'student_class' => $student_class, 'studentList' => $studentList]);
    }
    /**
     * Show the form for creating the resource.
     */
    public function create(): never
    {
        abort(404);
    }

    /**
     * Store the newly created resource in storage.
     */
    public function store(Request $request, $student_class)
{
    // Check if the class exists
    $class = Grade::findOrFail($student_class);
    $class_id = $class->id;
    $attendanceDate = date('Y-m-d');
    $logged_user = Auth::user();
    $teacher = Teacher::where('user_id', '=', $logged_user->id)->firstOrFail();

    // Get the students in the class
    $students = Student::where('class_id', '=', $student_class)->get();
    if ($students->isEmpty()) {
        Alert::error('Error', 'No students found in this class.');
        return back();
    }

    // Validate the request data
    $validator = Validator::make($request->all(), [
        'student_id' => 'required|array',
        'student_id.*' => 'required|integer|exists:students,id',
        'attendance_status' => 'required|array',
        'attendance_status.*' => 'required|in:present,absent,permission',
    ], [
        'attendance_status.*.required' => 'Each student must have an attendance status selected.',
    ]);

    $student_ids = $request->input('student_id');
    $attendance_status = $request->input('attendance_status');
    $class_group = $request->input('group');

    // Ensure each student has a status
    foreach ($student_ids as $student_id) {
        if (!isset($attendance_status[$student_id])) {
            $validator->errors()->add('attendance_status.' . $student_id, 'The attendance status for student ' . $student_id . ' is required.');
        }
    }

    if ($validator->fails()) {
        return redirect()->back()
                         ->withErrors($validator)
                         ->withInput();
    }

    // Check if attendance already exists for each student
    $existingAttendance = Attendance::whereIn('student_id', $student_ids)
                                    ->where('attendance_date', '=', $attendanceDate)
                                    ->where('teacher_id', '=', $teacher->id)
                                    ->pluck('student_id')
                                    ->toArray();

    if (!empty($existingAttendance)) {
        Alert::error('Error', 'Attendance already taken and Submitted.');
        return back();
    }

    // Save the attendance data
    $attendanceData = [];
    foreach ($student_ids as $studentId) {
        $attendanceData[] = [
            'student_id' => $studentId,
            'class_id' => $class_id,
            'teacher_id' => $teacher->id,
            'school_id' => $logged_user->school_id,
            'class_group' => $class_group[$studentId] ?? null,
            'attendance_status' => $attendance_status[$studentId],
            'attendance_date' => $attendanceDate,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    Attendance::insert($attendanceData);

    Alert::success('Success', 'Attendance Submitted and Saved successfully');
    return back();
}




    /**
     * Display the resource.
     */
    public function show(Student $student, $year)
    {
        // $students = Student::findOrFail($student);
        $attendance = Attendance::query()
            ->join('students', 'students.id', '=', 'attendances.student_id')
            ->join('teachers', 'teachers.id', '=', 'attendances.teacher_id')
            ->leftJoin('users', 'users.id', '=', 'teachers.user_id')
            ->join('grades', 'grades.id', '=', 'attendances.class_id')
            ->select(
                'attendances.*',
                'users.first_name as teacher_firstname',
                'users.last_name as teacher_lastname',
                'users.phone as teacher_phone',
                'students.first_name as student_firstname',
                'students.middle_name as student_middlename',
                'students.last_name as student_lastname'
            )
            ->whereYear('attendances.attendance_date', $year)
            ->where('attendances.student_id', '=', $student->id)
            ->orderBy('attendances.attendance_date', 'ASC')
            ->get()
            ->groupBy(function($date) {
                return \Carbon\Carbon::parse($date->attendance_date)->format('W'); // grouping by week number
            });

        return view('Attendance.show', compact('attendance', 'student'));
    }

    //group attendance by year ===========================
    public function attendanceYear (Student $student)
    {
        $attendances = Attendance::query()->join('students', 'students.id', '=', 'attendances.student_id')
                                            ->join('teachers', 'teachers.id', '=', 'attendances.teacher_id')
                                            ->leftJoin('users', 'users.id', '=', 'teachers.user_id')
                                            ->join('grades', 'grades.id', '=', 'attendances.class_id')
                                            ->select(
                                                'attendances.*',
                                                'users.first_name as teacher_firstname',
                                                'users.last_name as teacher_lastname',
                                                'users.phone as teacher_phone',
                                                'students.first_name as student_firstname',
                                                'students.middle_name as student_middlename',
                                                'students.last_name as student_lastname'
                                            )
                                            ->where('attendances.student_id', '=', $student->id)
                                            ->orderBy('attendances.attendance_date', 'ASC')
                                            ->get();
        $groupedAttendance = $attendances->groupBy(function ($item) {
            return Carbon::parse($item->attendance_date)->format('Y');
        })->map(function ($yearGroup) {
            return $yearGroup->groupBy('student_id');
        });

        return view('Attendance.parent_grouped', compact('groupedAttendance', 'attendances', 'student'));
    }

    public function getFormReport($class)
    {
        // return $class;
        $classTeacher = Class_teacher::query()->join('grades', 'grades.id', '=', 'class_teachers.class_id')
                                            ->select('class_teachers.*', 'grades.id as class_id', 'grades.class_name')
                                            ->findOrFail($class);
        // return $classTeacher;
        return view('Attendance.teacher_attendace', ['classTeacher' => $classTeacher]);
    }

    // generating teachers attendance report ===============================

    public function generateReport(Request $request, $classTeacher)
    {
        // Validate the request
        $request->validate([
            'start' => 'required|date_format:Y-m',
            'end' => 'required|date_format:Y-m'
        ]);

        // Retrieve the class teacher's details
        $class_teacher = Class_teacher::findOrFail($classTeacher);
        $classId = $class_teacher->class_id;
        $group = $class_teacher->group;

        // Retrieve the logged-in user ID
        $userId = Auth::user()->id;

        // Define the date range for the report
        $startOfMonth = Carbon::parse($request->input('start'))->startOfMonth();
        $endOfMonth = Carbon::parse($request->input('end'))->endOfMonth();

        // Query the attendance data
        $attendances = Attendance::join('students', 'students.id', '=', 'attendances.student_id')
            ->join('teachers', 'teachers.id', '=', 'attendances.teacher_id')
            ->join('users', 'users.id', '=', 'teachers.user_id')
            ->join('grades', 'grades.id', '=', 'attendances.class_id')
            ->select(
                'attendances.*', // Select all columns from the attendances table
                'students.id as studentID',
                'grades.class_name', 'grades.class_code',
                'students.first_name',
                'students.middle_name',
                'students.last_name',
                'students.gender',
                'students.group',
                'users.first_name as teacher_firstname',
                'users.last_name as teacher_lastname',
                'users.phone as teacher_phone',
                'users.gender as teacher_gender'
            )
            ->where('attendances.class_id', $classId)
            ->where('students.group', $group) // Compare 'group' directly
            ->whereBetween('attendances.attendance_date', [$startOfMonth, $endOfMonth])
            ->orderBy('attendances.attendance_date', 'ASC')
            ->orderBy('students.gender', 'DESC')
            ->orderBy('students.first_name', 'ASC')
            ->get();

        // Initialize summary arrays
        $maleSummary = [];
        $femaleSummary = [];

        foreach ($attendances as $attendance) {
            $month = Carbon::parse($attendance->attendance_date)->format('Y-m');

            // Initialize summary arrays for each month
            if (!isset($maleSummary[$month])) {
                $maleSummary[$month] = [
                    'present' => 0,
                    'absent' => 0,
                    'permission' => 0,
                ];
            }

            if (!isset($femaleSummary[$month])) {
                $femaleSummary[$month] = [
                    'present' => 0,
                    'absent' => 0,
                    'permission' => 0,
                ];
            }

            // Update summary counts
            $gender = $attendance->gender;
            $status = $attendance->attendance_status;
            if ($gender === 'male') {
                $maleSummary[$month][$status]++;
            } else {
                $femaleSummary[$month][$status]++;
            }
        }

        // Group the data by month
        $datas = $attendances->groupBy(function($item) {
            return Carbon::parse($item->attendance_date)->format('Y-m');
        });

        // Return the view with the report data
        return view('attendance.teacher_report', compact('datas', 'maleSummary', 'femaleSummary'));
    }



    /**
     * Show the form for editing the resource.
     */


    public function teacherAttendance($class)
    {
        $attendanceData = Attendance::findOrFail($class);
        // return $id;
        $class = Grade::findOrFail($attendanceData->class_id); //this is an object;
        // return $class->id;
        $teacher = Teacher::findOrFail($attendanceData->teacher_id);//this is an object
        $today = date('Y-m-d');
        // return $teacher;
        $attendanceRecords = Attendance::query()->join('students', 'students.id', '=', 'attendances.student_id')
                                                ->join('grades', 'grades.id', '=', 'attendances.class_id')
                                                ->join('teachers', 'teachers.id', '=', 'attendances.teacher_id')
                                                ->leftJoin('users', 'users.id', '=', 'teachers.user_id')
                                                ->select(
                                                    'attendances.*', 'students.first_name', 'students.middle_name', 'students.last_name', 'students.gender', 'students.group', 'students.id as studentId',
                                                    'users.first_name as teacher_firstname', 'users.last_name as teacher_lastname', 'users.gender as teacher_gender',
                                                    'users.phone as teacher_phone', 'grades.class_name', 'grades.class_code'
                                                )
                                                ->where('attendances.class_id', '=', $class->id)
                                                ->where('attendances.teacher_id', '=', $teacher->id)
                                                ->where('attendances.attendance_date', '=', $today)
                                                ->get();
        // return $attendanceRecords;
        return view('Attendance.pdfreport', ['attendanceRecords' => $attendanceRecords]);
    }


    //download attendance records----------------
    public function downloadAttendancePDF($class)
    {
        $attendanceData = Attendance::findOrFail($class);
        $class = Grade::findOrFail($attendanceData->class_id);
        $teacher = Teacher::findOrFail($attendanceData->teacher_id);

        $attendanceRecords = Attendance::query()
            ->join('students', 'students.id', '=', 'attendances.student_id')
            ->join('grades', 'grades.id', '=', 'attendances.class_id')
            ->join('teachers', 'teachers.id', '=', 'attendances.teacher_id')
            ->leftJoin('users', 'users.id', '=', 'teachers.user_id')
            ->select(
                'attendances.*',
                'students.first_name',
                'students.middle_name',
                'students.last_name',
                'students.gender',
                'users.first_name as teacher_firstname',
                'users.last_name as teacher_lastname',
                'users.gender as teacher_gender',
                'users.phone as teacher_phone',
                'grades.class_name',
                'grades.class_code'
            )
            ->where('attendances.class_id', '=', $class->id)
            ->where('attendances.teacher_id', '=', $teacher->id)
            ->orderBy('attendances.attendance_date', 'asc')
            ->get();

        $pdf = PDF::loadView('Attendance.pdfreport', compact('attendanceRecords'));
        return $pdf->download('attendance_records.pdf');
    }

    public function todayAttendance($student_class)
    {
        $user = Auth::user()->id;
        $teacher = Teacher::where('user_id', '=', $user)->firstOrFail();
        // return $teacher;
        $class = Grade::findOrFail($student_class); //this is a CLASS object;
        $classTeacher = Class_teacher::where('class_id', '=', $class->id)->firstOrFail();
        // return $classTeacher;
        $teacher_id = $classTeacher->teacher_id;
        $teacherGroup = $classTeacher->group;
        // return $teacher_id . $teacherGroup;

        $today = date('Y-m-d');

        $attendanceRecords = Attendance::query()->join('students', 'students.id', '=', 'attendances.student_id')
                                        ->join('grades', 'grades.id', '=', 'attendances.class_id')
                                        ->join('teachers', 'teachers.id', '=', 'attendances.teacher_id')
                                        ->leftJoin('users', 'users.id', '=', 'teachers.user_id')
                                        ->select(
                                            'attendances.*',
                                            'students.id as studentId', 'students.first_name', 'students.middle_name',
                                            'students.last_name', 'students.group', 'students.class_id', 'students.gender',
                                            'grades.class_name', 'grades.class_code', 'grades.id as class_id',
                                            'users.first_name as teacher_firstname', 'users.last_name as teacher_lastname',
                                            'users.phone as teacher_phone', 'users.gender as teacher_gender'
                                        )
                                        ->where('attendances.attendance_date', '=', $today)
                                        ->where('attendances.teacher_id', '=', $teacher->id)
                                        ->where('attendances.school_id', '=', Auth::user()->school_id)
                                        ->orderBy('students.gender', 'DESC')
                                        ->orderBy('students.first_name', 'ASC')
                                        ->get();
                        // Initialize counters
                        $malePresent = 0;
                        $maleAbsent = 0;
                        $malePermission = 0;
                        $femalePresent = 0;
                        $femaleAbsent = 0;
                        $femalePermission = 0;

        // Count the attendance based on gender and status
        foreach ($attendanceRecords as $record) {
            if ($record->gender == 'male') {
                if ($record->attendance_status == 'present') {
                    $malePresent++;
                } elseif ($record->attendance_status == 'absent') {
                    $maleAbsent++;
                } elseif ($record->attendance_status == 'permission') {
                    $malePermission++;
                }
            } elseif ($record->gender == 'female') {
                if ($record->attendance_status == 'present') {
                    $femalePresent++;
                } elseif ($record->attendance_status == 'absent') {
                    $femaleAbsent++;
                } elseif ($record->attendance_status == 'permission') {
                    $femalePermission++;
                }
            }
        }

        // Pass the counters to the view
        return view('Attendance.pdfreport', [
            'attendanceRecords' => $attendanceRecords,
            'malePresent' => $malePresent,
            'maleAbsent' => $maleAbsent,
            'malePermission' => $malePermission,
            'femalePresent' => $femalePresent,
            'femaleAbsent' => $femaleAbsent,
            'femalePermission' => $femalePermission,
        ]);
    }

    public function getField() {
        $classes = Grade::where('school_id', Auth::user()->school_id)->get();
        return view('Attendance.general_attendance', ['classes' => $classes]);
    }

    public function genaralAttendance(Request $request)
    {
        $request->validate([
            'class' => 'required|exists:grades,id',
            'start' => 'required|date_format:Y-m',
            'end' => 'required|date_format:Y-m'
        ]);

        $startOfMonth = Carbon::parse($request->input('start'))->startOfMonth();
        $endOfMonth = Carbon::parse($request->input('end'))->endOfMonth();

        $attendances = Attendance::query()
            ->join('students', 'students.id', '=', 'attendances.student_id')
            ->join('grades', 'grades.id', '=', 'attendances.class_id')
            ->join('teachers', 'teachers.id', '=', 'attendances.teacher_id')
            ->leftJoin('users', 'users.id', '=', 'teachers.user_id')
            ->select(
                'attendances.*',
                'students.id as studentId', 'students.first_name', 'students.middle_name', 'students.last_name', 'students.gender',
                'students.group', 'students.class_id as student_class',
                'grades.id as class_id', 'grades.class_name', 'grades.class_code',
                'users.first_name as teacher_firstname', 'users.last_name as teacher_lastname',
                'users.gender as teacher_gender', 'users.phone as teacher_phone'
            )
            ->where('attendances.class_id', $request->class)
            ->whereBetween('attendances.attendance_date', [$startOfMonth, $endOfMonth])
            ->where('attendances.school_id', Auth::user()->school_id)
            ->orderBy('attendances.attendance_date', 'ASC')
            ->orderBy('students.gender', 'DESC')
            ->orderBy('students.first_name', 'ASC')
            ->get();

        $maleSummary = [];
        $femaleSummary = [];

        foreach($attendances as $attendance) {
            $date = Carbon::parse($attendance->attendance_date)->format('Y-m-d');

            if(!isset($maleSummary[$date])) {
                $maleSummary[$date] = [
                    'present' => 0,
                    'absent' => 0,
                    'permission' => 0
                ];
            }
            if (!isset($femaleSummary[$date])) {
                $femaleSummary[$date] = [
                    'present' => 0,
                    'absent' => 0,
                    'permission' => 0,
                ];
            }
            $gender = $attendance->gender;
            $status = $attendance->attendance_status;
            if($gender === 'male') {
                $maleSummary[$date][$status]++;
            } else {
                $femaleSummary[$date][$status]++;
            }
        }

        // Group by attendance date and then by teacher ID
        $datas = $attendances->groupBy(function($item) {
            return Carbon::parse($item->attendance_date)->format('Y-m-d');
        })->map(function($dayGroup) {
            return $dayGroup->groupBy('teacher_id');
        });

        return view('Attendance.all_report', compact('datas', 'maleSummary', 'femaleSummary', 'attendances'));
    }

}
