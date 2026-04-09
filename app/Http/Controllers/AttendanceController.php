<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Class_teacher;
use App\Models\Grade;
use App\Models\Parents;
use App\Models\school;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Vinkla\Hashids\Facades\Hashids;

class AttendanceController extends Controller
{

    public function index($class, Request $request)
    {
        $id = Hashids::decode($class);
        $user = Auth::user();
        $teacherLoggedIn = Teacher::where('user_id', '=', $user->id)->firstOrFail();

        $myClass = Class_teacher::findOrFail($id[0]);
        $teacher = Teacher::findOrFail($myClass->teacher_id);

        // Hakikisha mwalimu aliyeingia ndiye class teacher
        if ($teacherLoggedIn->id != $teacher->id) {
            Alert()->toast('You are not the class teacher for this class', 'error');
            return back();
        }

        $student_class = Grade::findOrFail($myClass->class_id);

        // Chagua tarehe kutoka kwa request, default iwe leo
        $selectedDate = $request->input('attendance_date', Carbon::now()->format('Y-m-d'));

        // Angalia kama attendance ipo tayari kwa hiyo tarehe
        $attendanceExists = Attendance::where('class_id', $student_class->id)
            ->where('class_group', $myClass->group)
            ->where('attendance_date', $selectedDate)
            ->where('school_id', $user->school_id)
            ->exists();

        // Pata wanafunzi lakini usiwafiche kabisa hata kama attendance ipo
        $studentList = Student::query()
            ->join('grades', 'grades.id', '=', 'students.class_id')
            ->select('students.*', 'grades.class_code')
            ->where('class_id', '=', $student_class->id)
            ->where('group', '=', $myClass->group)
            ->where('students.status', '=', 1)
            ->where('students.school_id', '=', $user->school_id)
            ->where('graduated', 0)
            ->orderBy('gender', 'ASC')
            ->orderBy('first_name', 'ASC')
            ->get();

        return view('Attendance.index', [
            'myClass' => $myClass,
            'teacher' => $teacher,
            'student_class' => $student_class,
            'studentList' => $studentList,
            'class' => $class,
            'attendanceExists' => $attendanceExists,
            'selectedDate' => $selectedDate,
        ]);
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
        $id = Hashids::decode($student_class);
        // Check if the class exists
        $user = Auth::user();

        $class = Grade::findOrFail($id[0]);
        $class_id = $class->id;
        $attendanceDate = $request->input('attendance_date');
        $logged_user = Auth::user();
        $teacher = Teacher::where('user_id', '=', $logged_user->id)->firstOrFail();

        //check if the teacher logged in is the class teacher
        $class_teacher = Class_teacher::where('class_id', '=', $class_id)->where('teacher_id', '=', $teacher->id)->first();
        if (!$class_teacher) {
            // Alert::error('Error', 'You are not the class teacher for this class.');
            Alert()->toast('You are not the class teacher for this class', 'error');
            return back();
        }

        // Get the students in the class
        $students = Student::where('class_id', '=', $class_id)->where('school_id', $user->school_id)->where('graduated', 0)->where('status', 1)->get();
        if ($students->isEmpty()) {
            // Alert::error('Error', 'No students found in this class.');
            Alert()->toast('No students found in this class', 'error');
            return back();
        }

        // Validate the request data
        $validator = Validator::make($request->all(), [
            'student_id' => 'required|array',
            'student_id.*' => 'required|integer|exists:students,id',
            'attendance_date' => 'required|date_format:Y-m-d',
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
            ->pluck('student_id')
            ->toArray();

        if (!empty($existingAttendance)) {
            // Alert::error('Error', 'Attendance already taken and Submitted.');
            Alert()->toast('Attendance already taken and Submitted', 'error');
            // return redirect()->route('get.student.list', $student_class);
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

        // Alert::success('Success', 'Attendance Submitted and Saved successfully');
        Alert()->toast('Attendance Submitted and Saved successfully', 'success');
        // return redirect()->route('home');
        return redirect()->back();
    }

    public function show($student, $year)
    {
        $decoded = Hashids::decode($student);

        $students = Student::find($decoded[0]);
        $user = Auth::user();
        $parent = Parents::where('user_id', '=', $user->id)->firstOrFail();
        $attendanceQuery = Attendance::query()
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
                'students.parent_id',
                'students.middle_name as student_middlename',
                'students.last_name as student_lastname',
                'students.status',
                'students.graduated'
            )
            ->whereYear('attendances.attendance_date', $year)
            ->where('attendances.student_id', '=', $students->id)
            ->where('attendances.school_id', $user->school_id)
            ->where('students.parent_id', $parent->id)
            ->whereIn('students.status', [0, 1, 2])
            ->orderBy('attendances.attendance_date', 'DESC');

        // Paginate the raw data
        $perPage = 22;
        $page = request()->get('page', 1);
        $rawData = $attendanceQuery->paginate($perPage, ['*'], 'page', $page);

        // Group the paginated data by week
        $grouped = $rawData->getCollection()->groupBy(function ($date) {
            return \Carbon\Carbon::parse($date->attendance_date)->format('W'); // grouping by week number
        });

        // Create a LengthAwarePaginator instance
        $groupedData = new LengthAwarePaginator(
            $grouped,
            $rawData->total(),
            $rawData->perPage(),
            $rawData->currentPage(),
            ['path' => request()->url(), 'query' => request()->query()]
        );

        $firstRecord = $rawData->first();

        return view('Attendance.show', compact('groupedData', 'students', 'firstRecord'));
    }

    //group attendance by year ===========================
    public function attendanceYear($student)
    {
        // $studentId = Student::findOrFail($student->id);
        $decoded = Hashids::decode($student);

        $students = Student::find($decoded[0]);
        $user = Auth::user();
        $parent = Parents::where('user_id', '=', $user->id)->firstOrFail();
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
                'students.admission_number',
                'students.middle_name as student_middlename',
                'students.status',
                'students.last_name as student_lastname',
                'students.parent_id',
            )
            ->where('attendances.student_id', '=', $students->id)
            ->where('attendances.school_id', $user->school_id)
            ->where('students.parent_id', $parent->id)
            ->whereIn('students.status', [0, 1, 2])
            ->orderBy('attendances.attendance_date', 'DESC')
            ->get();
        $groupedAttendance = $attendances->groupBy(function ($item) {
            return Carbon::parse($item->attendance_date)->format('Y');
        })->map(function ($yearGroup) {
            return $yearGroup->groupBy('student_id');
        });

        return view('Attendance.parent_grouped', compact('groupedAttendance', 'attendances', 'students'));
    }

    public function getFormReport($class)
    {
        // return $class;
        $id = Hashids::decode($class);
        $user = Auth::user();
        $teacher = Teacher::where('user_id', '=', $user->id)->firstOrFail();
        $classTeacher = Class_teacher::query()->join('grades', 'grades.id', '=', 'class_teachers.class_id')
            ->select('class_teachers.*', 'grades.id as class_id', 'grades.class_name')
            ->findOrFail($id[0]);
        // return $classTeacher;
        return view('Attendance.teacher_attendace', ['classTeacher' => $classTeacher]);
    }

    // generating teachers attendance report ===============================

    public function generateReport(Request $request, $classTeacher)
    {
        $id = Hashids::decode($classTeacher);
        // Validate the request
        $request->validate([
            'start' => 'required|date_format:Y-m-d',
            'end' => 'required|date_format:Y-m-d'
        ]);

        // Retrieve the class teacher's details
        $class_teacher = Class_teacher::findOrFail($id[0]);
        $classId = $class_teacher->class_id;
        $group = $class_teacher->group;

        // Retrieve the logged-in user ID
        $userId = Auth::user()->id;

        // Define the date range for the report
        $startDate = Carbon::parse($request->input('start'))->startOfDay();
        $endDate = Carbon::parse($request->input('end'))->endOfDay();

        // Query the attendance data
        $attendances = Attendance::join('students', 'students.id', '=', 'attendances.student_id')
            ->join('teachers', 'teachers.id', '=', 'attendances.teacher_id')
            ->join('users', 'users.id', '=', 'teachers.user_id')
            ->join('grades', 'grades.id', '=', 'attendances.class_id')
            ->leftJoin('schools', 'schools.id', '=', 'students.school_id')
            ->select(
                'attendances.*', // Select all columns from the attendances table
                'students.id as studentID',
                'grades.class_name',
                'grades.class_code',
                'students.first_name',
                'students.middle_name',
                'students.last_name',
                'students.gender',
                'students.group',
                'students.admission_number',
                'users.first_name as teacher_firstname',
                'users.last_name as teacher_lastname',
                'students.status',
                'users.phone as teacher_phone',
                'users.gender as teacher_gender',
                'schools.school_reg_no',
            )
            ->where('attendances.class_id', $classId)
            ->where('students.group', $group) // Compare 'group' directly
            ->whereBetween('attendances.attendance_date', [$startDate, $endDate])
            ->where('attendances.school_id', Auth::user()->school_id)
            ->orderBy('attendances.attendance_date', 'ASC')
            // ->where('students.status', 1)
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
        $datas = $attendances->groupBy(function ($item) {
            return Carbon::parse($item->attendance_date)->format('Y-m');
        });
        $message = "There is no attendance record for the selected time duration! thank you";

        if ($datas->isEmpty()) {
            return view('Attendance.teacher', compact('message', 'classId'));
        }

        // Generate PDF
        $pdf = \PDF::loadView('Attendance.teacher_report', compact('datas', 'maleSummary', 'femaleSummary'));

        // Generate a filename with a timestamp
        $timestamp = Carbon::now()->timestamp;
        $fileName = "class_teacher_attendance_{$timestamp}.pdf";
        $folderPath = public_path('attendances');

        // Make sure the directory exists
        if (!File::exists($folderPath)) {
            File::makeDirectory($folderPath, 0755, true);
        }

        // Save the PDF file to the 'attendances' folder
        $pdf->save($folderPath . '/' . $fileName);

        // URL to the file for embedding in iframe
        $fileUrl = url('attendances/' . $fileName);

        return view('Attendance.teacher_pdf_generated_report', compact('fileUrl', 'id'));
    }

    public function getField()
    {
        $classes = Grade::where('school_id', Auth::user()->school_id)->where('status', 1)->orderBy('class_code')->get();
        return view('Attendance.general_attendance', ['classes' => $classes]);
    }

    public function generateClassReport(Request $request)
    {
        try {
            ini_set('memory_limit', '1024M'); // optional safety

            // ✅ Validate request
            $validator = Validator::make($request->all(), [
                'class' => 'required|exists:grades,id',
                'start' => 'required|date_format:Y-m-d',
                'end' => 'required|date_format:Y-m-d',
                'stream' => 'nullable|in:a,b,c,all',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $startDate = Carbon::parse($request->start)->startOfDay();
            $endDate   = Carbon::parse($request->end)->endOfDay();
            $stream    = strtolower($request->input('stream', 'all'));
            $arrayStream = ['a', 'b', 'c'];

            // ✅ Limit date range (IMPORTANT)
            if ($startDate->diffInDays($endDate) > 31) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please select a date range of 31 days or less'
                ]);
            }

            // ✅ School
            $school = school::find(Auth::user()->school_id);
            if (!$school) {
                return response()->json([
                    'success' => false,
                    'message' => 'School not found'
                ], 404);
            }

            // ✅ Collect data using chunk (memory safe)
            $attendances = collect();

            Attendance::query()
                ->join('students', 'students.id', '=', 'attendances.student_id')
                ->join('grades', 'grades.id', '=', 'attendances.class_id')
                ->join('teachers', 'teachers.id', '=', 'attendances.teacher_id')
                ->leftJoin('users', 'users.id', '=', 'teachers.user_id')
                ->leftJoin('schools', 'schools.id', '=', 'students.school_id')
                ->select(
                    'attendances.attendance_date',
                    'attendances.attendance_status',
                    'students.id as studentId',
                    'students.first_name',
                    'students.middle_name',
                    'students.last_name',
                    'students.gender',
                    'students.group',
                    'students.admission_number',
                    'grades.class_name'
                )
                ->where('attendances.class_id', $request->class)
                ->whereBetween('attendances.attendance_date', [$startDate, $endDate])
                ->where('attendances.school_id', Auth::user()->school_id)
                ->where(function ($query) use ($stream, $arrayStream) {
                    if ($stream === 'all') {
                        $query->whereIn('students.group', $arrayStream);
                    } else {
                        $query->where('students.group', $stream);
                    }
                })
                ->orderBy('attendances.attendance_date', 'ASC')
                ->chunk(500, function ($rows) use (&$attendances) {
                    foreach ($rows as $row) {
                        $attendances->push($row);
                    }
                });

            if ($attendances->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No attendance records found'
                ]);
            }

            // ✅ Group by date (lightweight)
            $datas = $attendances->groupBy(function ($item) {
                return Carbon::parse($item->attendance_date)->format('Y-m-d');
            });

            // ✅ Generate HTML (preview)
            $html = $this->generateAttendanceHtmlOptimized($datas);

            // ✅ PDF (ONLY if data is small)
            $fileUrl = null;

            if ($attendances->count() <= 2000) {
                try {
                    $pdf = \PDF::loadView('Attendance.all_report', [
                        'datas' => $datas,
                        'startDate' => $startDate,
                        'endDate' => $endDate,
                        'school' => $school
                    ])->setPaper('a4', 'landscape');

                    $fileName = 'attendance_' . time() . '.pdf';
                    $folderPath = public_path('attendances');

                    if (!File::exists($folderPath)) {
                        File::makeDirectory($folderPath, 0755, true);
                    }

                    $pdf->save($folderPath . '/' . $fileName);
                    $fileUrl = asset('attendances/' . $fileName);
                } catch (\Exception $e) {
                    Log::error('PDF Error: ' . $e->getMessage());
                }
            }

            return response()->json([
                'success' => true,
                'html' => $html,
                'pdf_url' => $fileUrl,
                'total_records' => $attendances->count()
            ]);
        } catch (\Exception $e) {
            Log::error('Attendance Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Server error'
            ], 500);
        }
    }

    private function generateAttendanceHtmlOptimized($datas)
    {
        $html = '<table border="1" cellpadding="5" cellspacing="0">';
            $html .= '<tr>
                <th>#</th>
                <th>Name</th>
                <th>Sex</th>
                <th>Stream</th>
                <th>Date</th>
                <th>Status</th>
            </tr>';

        $index = 1;

        foreach ($datas as $date => $records) {
            foreach ($records as $row) {

                $name = $row->first_name . ' ' . $row->last_name;
                $gender = $row->gender ?? '-';
                $group = $row->group ?? '-';
                $status = strtoupper(substr($row->attendance_status, 0, 1));

                $html .= "<tr>
                <td>{$index}</td>
                <td>{$name}</td>
                <td>{$gender}</td>
                <td>{$group}</td>
                <td>{$date}</td>
                <td>{$status}</td>
            </tr>";

                $index++;
            }
        }

        $html .= '</table>';

        return $html;
    }
}
