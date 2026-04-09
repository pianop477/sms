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
                'students.status',
                'students.graduated',
                'students.admission_number',
                'users.first_name as teacher_firstname',
                'users.last_name as teacher_lastname',
                'students.status',
                'users.phone as teacher_phone',
                'users.gender as teacher_gender',
                'schools.school_reg_no',
            )
            ->where('attendances.class_id', $classId)
            ->where('students.status', '!=', 2) // Exclude graduated students
            ->where('students.graduated', 0) // Ensure graduated students are excluded
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
            ini_set('memory_limit', '1024M'); // safety

            // Validate request
            $validator = Validator::make($request->all(), [
                'class' => 'required|exists:grades,id',
                'start' => 'required|date_format:Y-m-d',
                'end' => 'required|date_format:Y-m-d',
                'stream' => 'nullable|in:a,b,c,all',
                'format' => 'nullable|in:html,pdf,both', // new flexibility
                'page' => 'nullable|integer|min:1', // for pagination
                'per_page' => 'nullable|integer|min:10|max:500' // chunk size
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $startDate = Carbon::parse($request->input('start'))->startOfDay();
            $endDate   = Carbon::parse($request->input('end'))->endOfDay();
            $stream    = strtolower($request->input('stream', 'all'));
            $format    = $request->input('format', 'both');
            $page      = $request->input('page', 1);
            $perPage   = $request->input('per_page', 500);
            $arrayStream = ['a', 'b', 'c'];

            // ✅ Limit range
            if ($startDate->diffInDays($endDate) > 31) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please select maximum of 31 days'
                ]);
            }

            // School
            $school = school::find(Auth::user()->school_id);
            if (!$school) {
                return response()->json([
                    'success' => false,
                    'message' => 'School not found'
                ], 404);
            }

            // ✅ Get total count first for optimization decisions
            $totalCount = Attendance::query()
                ->where('class_id', $request->class)
                ->whereBetween('attendance_date', [$startDate, $endDate])
                ->where('school_id', Auth::user()->school_id)
                ->count();

            if ($totalCount === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No attendance records found'
                ]);
            }

            // ✅ Optimized query with pagination support
            $query = Attendance::query()
                ->join('students', 'students.id', '=', 'attendances.student_id')
                ->join('grades', 'grades.id', '=', 'attendances.class_id')
                ->join('teachers', 'teachers.id', '=', 'attendances.teacher_id')
                ->leftJoin('users', 'users.id', '=', 'teachers.user_id')
                ->leftJoin('schools', 'schools.id', '=', 'students.school_id')
                ->select(
                    'attendances.*',
                    'students.id as studentId',
                    'students.first_name',
                    'students.middle_name',
                    'students.last_name',
                    'students.gender',
                    'students.group',
                    'students.class_id as student_class',
                    'students.admission_number',
                    'students.status',
                    'students.graduated',
                    'grades.id as class_id',
                    'grades.class_name',
                    'grades.class_code',
                    'users.first_name as teacher_firstname',
                    'users.last_name as teacher_lastname',
                    'schools.school_reg_no',
                )
                ->whereIn('students.status', [0, 1])
                ->where('students.status', '!=', 2) // Exclude graduated students
                ->where('students.graduated', 0) // Ensure graduated students are excluded
                ->where('attendances.class_id', $request->class)
                ->whereBetween('attendances.attendance_date', [$startDate, $endDate])
                ->where(function ($query) use ($stream, $arrayStream) {
                    if ($stream == 'all') {
                        $query->whereIn('students.group', $arrayStream);
                    } else {
                        $query->where('students.group', $stream);
                    }
                })
                ->where('attendances.school_id', Auth::user()->school_id)
                ->orderBy('attendances.attendance_date', 'ASC')
                ->orderBy('students.group', 'ASC')
                ->orderBy('students.gender', 'DESC')
                ->orderBy('students.first_name', 'ASC');

            // ✅ Memory-efficient processing based on data size
            $isLargeDataset = $totalCount > 2000;

            if ($isLargeDataset && $format !== 'pdf') {
                // For large datasets, use streaming approach
                $html = $this->generateAttendanceHtmlStreaming($query, $school, $startDate, $endDate, $perPage);
                $fileUrl = null;
            } else {
                // For smaller datasets, use collection approach (maintains template style)
                $attendances = $this->fetchAttendancesEfficiently($query, $isLargeDataset ? 1000 : $perPage);

                if ($attendances->isEmpty()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No attendance records found'
                    ]);
                }

                // Group by date
                $datas = $attendances->groupBy(function ($item) {
                    return Carbon::parse($item->attendance_date)->format('Y-m-d');
                });

                // ✅ Generate HTML (always using original style for smaller datasets)
                $html = $this->generateAttendanceHtml($datas, $attendances, $school);

                // ✅ Generate PDF only if requested and data size is manageable
                $fileUrl = null;
                if (($format === 'pdf' || $format === 'both') && $totalCount <= 1500) {
                    $fileUrl = $this->generatePdf($datas, $attendances, $startDate, $endDate, $school);
                }
            }

            return response()->json([
                'success' => true,
                'html' => $html,
                'pdf_url' => $fileUrl,
                'total_records' => $totalCount,
                'is_large_dataset' => $isLargeDataset ?? false
            ]);
        } catch (\Exception $e) {
            Log::error('Attendance Report Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . ($e->getMessage())
            ], 500);
        }
    }

    /**
     * Memory-efficient fetching with chunking
     */
    private function fetchAttendancesEfficiently($query, $chunkSize = 500)
    {
        $attendances = collect();

        $query->chunk($chunkSize, function ($rows) use (&$attendances) {
            foreach ($rows as $row) {
                $attendances->push($row);
            }
        });

        return $attendances;
    }

    /**
     * Streaming HTML generation for large datasets (maintains style)
     */
    private function generateAttendanceHtmlStreaming($query, $school, $startDate, $endDate, $chunkSize = 500)
    {
        $html = '<div class="attendance-report">';

        // Get date range and group by month using a separate query
        $datesQuery = clone $query;
        $dateRange = $datesQuery->select('attendances.attendance_date')
            ->distinct()
            ->orderBy('attendance_date', 'ASC')
            ->get()
            ->pluck('attendance_date')
            ->map(function ($date) {
                return Carbon::parse($date)->format('Y-m-d');
            });

        if ($dateRange->isEmpty()) {
            return '<div class="attendance-report">No data available</div>';
        }

        // Group dates by month
        $months = [];
        foreach ($dateRange as $date) {
            $monthYear = Carbon::parse($date)->format('Y-m');
            if (!isset($months[$monthYear])) {
                $months[$monthYear] = [];
            }
            $months[$monthYear][] = $date;
        }

        // Get class id and name from first record
        $firstRecordQuery = clone $query;
        $firstRecord = $firstRecordQuery->first();
        $classId = $firstRecord->class_id ?? $firstRecord->student_class ?? null;
        $className = $firstRecord->class_name ?? 'N/A';
        $streamFilter = request()->input('stream', 'all');

        // Process each month separately to save memory
        foreach ($months as $monthYear => $datesInMonth) {
            $monthName = Carbon::parse($monthYear . '-01')->format('F Y');
            $startOfMonth = Carbon::parse($datesInMonth[0])->startOfDay();
            $endOfMonth = Carbon::parse(end($datesInMonth))->endOfDay();

            // Get students for this class - EXCLUDE GRADUATED STUDENTS
            $studentsQuery = \App\Models\Student::query()
                ->where('class_id', $classId)
                ->where('school_id', Auth::user()->school_id)
                ->where(function ($query) {
                    $query->whereIn('status', [0, 1]);  // Exclude status 2 (graduated)
                });

            // Apply stream filter
            if ($streamFilter !== 'all') {
                $studentsQuery->where('group', $streamFilter);
            } else {
                $studentsQuery->whereIn('group', ['a', 'b', 'c']);
            }

            $studentsList = $studentsQuery->orderBy('group', 'ASC')
                ->orderBy('gender', 'DESC')
                ->orderBy('first_name', 'ASC')
                ->get();

            if ($studentsList->isEmpty()) {
                continue;
            }

            // Initialize students array
            $students = [];
            foreach ($studentsList as $student) {
                $students[$student->id] = [
                    'id' => $student->id,
                    'admission_number' => $student->admission_number,
                    'name' => ucwords(strtolower($student->first_name . ' ' .
                        ($student->middle_name ? $student->middle_name . ' ' : '') .
                        $student->last_name)),
                    'gender' => $student->gender[0] ?? 'U',
                    'group' => $student->group ?? 'N/A',
                    'attendances' => []
                ];
            }

            // Get attendance records for this month - ONLY FOR NON-GRADUATED STUDENTS
            $attendanceRecords = Attendance::query()
                ->select('attendances.student_id', 'attendances.attendance_date', 'attendances.attendance_status')
                ->join('students', 'students.id', '=', 'attendances.student_id')
                ->where('attendances.class_id', $classId)
                ->where('attendances.school_id', Auth::user()->school_id)
                ->whereBetween('attendances.attendance_date', [$startOfMonth, $endOfMonth])
                ->whereIn('attendances.student_id', array_keys($students))
                ->where(function ($query) {
                    $query->where('students.status', '!=', 2)
                        ->where('students.graduated', '!=', 1);
                })
                ->orderBy('attendances.attendance_date', 'ASC')
                ->get();

            // Populate attendance data
            foreach ($attendanceRecords as $record) {
                $dateKey = Carbon::parse($record->attendance_date)->format('Y-m-d');
                if (isset($students[$record->student_id])) {
                    $students[$record->student_id]['attendances'][$dateKey] = $record->attendance_status;
                }
            }

            // Calculate statistics
            $totalRecords = $attendanceRecords->count();
            $presentRecords = $attendanceRecords->where('attendance_status', 'present')->count();
            $attendanceRate = $totalRecords > 0 ? round(($presentRecords / $totalRecords) * 100) : 0;

            // Create display record
            $displayRecord = new \stdClass();
            $displayRecord->class_name = $className;

            $stats = [
                'total_records' => $totalRecords,
                'present_records' => $presentRecords,
                'attendance_rate' => $attendanceRate
            ];

            // Generate HTML for this month
            $html .= $this->generateMonthHtml($students, $datesInMonth, $monthName, $stats, $displayRecord);

            // Free memory
            unset($students, $studentsList, $attendanceRecords);
        }

        $html .= '</div>';
        return $html;
    }


    /**
     * Calculate monthly statistics efficiently
     */
    private function calculateMonthStatsStreaming($attendanceRecords, $datesInMonth)
    {
        $totalRecords = $attendanceRecords->count();
        $presentRecords = $attendanceRecords->where('attendance_status', 'present')->count();
        $attendanceRate = $totalRecords > 0 ? round(($presentRecords / $totalRecords) * 100) : 0;

        return [
            'total_records' => $totalRecords,
            'present_records' => $presentRecords,
            'attendance_rate' => $attendanceRate
        ];
    }

    /**
     * Get first record for month
     */
    private function getFirstRecordForMonth($query)
    {
        $clone = clone $query;
        return $clone->first();
    }

    /**
     * Generate HTML for a single month (reusable)
     */
    private function generateMonthHtml($students, $datesInMonth, $monthName, $stats, $firstRecord)
    {
        sort($datesInMonth);

        $html = '
        <div class="month-section">
            <div class="time-duration-header">
                Attendance Report for: <strong>' . $monthName . '</strong>
                | Attendance Rate: <strong>' . $stats['attendance_rate'] . '%</strong>
            </div>

            <div class="summary-section">
                <div class="summary-content">
                    <div class="course-details">
                        <p style="text-transform: uppercase"><span class="bold">Class:</span> ' . ($firstRecord->class_name ?? 'N/A') . '</p>
                        <p><span class="bold">Report Date:</span>
                            ' . Carbon::parse($datesInMonth[0])->format('d/m/Y') . ' -
                            ' . Carbon::parse($datesInMonth[count($datesInMonth) - 1])->format('d/m/Y') . '
                        </p>
                        <p><span class="bold">Total Students:</span> ' . count($students) . '</p>
                    </div>

                    <div class="grade-summary">
                        <p class="title">Attendance Rate</p>
                        <div style="text-align: center; font-size: 18px; font-weight: bold; color: #28a745;">
                            ' . $stats['attendance_rate'] . '%
                        </div>
                        <div class="progress mt-3">
                            <div class="progress-bar" style="width: ' . $stats['attendance_rate'] . '%;"></div>
                        </div>
                    </div>
                </div>
            </div>

            <h5 class="summary-header">Student Attendance Records - ' . $monthName . '</h5>
            <div class="table-container">
                <table class="attendance-table">
                    <thead>
                        <tr>
                            <th class="col-number">#</th>
                            <th class="col-name">Student\'s Name</th>
                            <th class="col-gender">Gender</th>
                            <th class="col-stream">Stream</th>';

        foreach ($datesInMonth as $date) {
            $html .= '<th class="col-date">' . Carbon::parse($date)->format('d') . '</th>';
        }

        $html .= '                </td>
                    </thead>
                    <tbody>';

        $index = 0;
        foreach ($students as $student) {
            $index++;
            $html .= '<tr>
                        <td class="col-number">' . $index . '</td>
                        <td class="col-name">' . $student['name'] . '</td>
                        <td class="col-gender">' . strtoupper($student['gender']) . '</td>
                        <td class="col-stream">' . strtoupper($student['group']) . '</td>';

            foreach ($datesInMonth as $date) {
                $status = $student['attendances'][$date] ?? 'A';

                if ($status === 'present') {
                    $symbol = 'P';
                    $class = 'attendance-present';
                } elseif ($status === 'absent') {
                    $symbol = 'A';
                    $class = 'attendance-absent';
                } elseif ($status === 'permission') {
                    $symbol = '*';
                    $class = 'attendance-permission';
                } else {
                    $symbol = '?';
                    $class = 'attendance-absent';
                }

                $html .= '<td class="' . $class . '">' . $symbol . '</td>';
            }

            $html .= '</tr>';
        }

        $html .= '            </tbody>
            </table>
            </div>

            <div class="legend">
                <span class="legend-item"><span class="attendance-present">P</span> = Present</span>
                <span class="legend-item"><span class="attendance-absent">A</span> = Absent</span>
                <span class="legend-item"><span class="attendance-permission">*</span> = Permission</span>
            </div>
        </div>';

        return $html;
    }

    /**
     * Generate PDF (extracted for reusability)
     */
    private function generatePdf($datas, $attendances, $startDate, $endDate, $school)
    {
        try {
            $pdf = \PDF::loadView('Attendance.all_report', [
                'datas' => $datas,
                'attendances' => $attendances,
                'startDate' => $startDate,
                'endDate' => $endDate,
                'school' => $school
            ])->setPaper('a4', 'landscape');

            $fileName = "attendance_" . time() . ".pdf";
            $folderPath = public_path('attendances');

            if (!File::exists($folderPath)) {
                File::makeDirectory($folderPath, 0755, true);
            }

            $pdf->save($folderPath . '/' . $fileName);
            return asset('attendances/' . $fileName);
        } catch (\Exception $e) {
            Log::error('PDF Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Generate original styled HTML attendance report
     */
    private function generateAttendanceHtml($datas, $attendances, $school)
    {
        $html = '<div class="attendance-report">';

        // Group by month
        $monthlyData = [];
        foreach ($datas as $date => $dateAttendances) {
            $monthYear = Carbon::parse($date)->format('Y-m');
            if (!isset($monthlyData[$monthYear])) {
                $monthlyData[$monthYear] = [];
            }
            $monthlyData[$monthYear][$date] = $dateAttendances;
        }

        ksort($monthlyData);

        foreach ($monthlyData as $monthYear => $monthAttendances) {
            $monthName = Carbon::parse($monthYear . '-01')->format('F Y');
            $datesInMonth = array_keys($monthAttendances);
            sort($datesInMonth);

            // Get all students for this month
            $students = [];
            foreach ($monthAttendances as $dateAttendances) {
                foreach ($dateAttendances as $attendance) {
                    $studentId = $attendance->studentId;
                    if (!isset($students[$studentId])) {
                        $students[$studentId] = [
                            'id' => $studentId,
                            'admission_number' => $attendance->admission_number,
                            'name' => ucwords(strtolower($attendance->first_name . ' ' .
                                ($attendance->middle_name ? $attendance->middle_name . ' ' : '') .
                                $attendance->last_name)),
                            'gender' => $attendance->gender[0] ?? 'U',
                            'group' => $attendance->group ?? 'N/A',
                            'attendances' => []
                        ];
                    }
                }
            }

            // Populate attendance data
            foreach ($monthAttendances as $date => $dateAttendances) {
                foreach ($dateAttendances as $attendance) {
                    $studentId = $attendance->studentId;
                    if (isset($students[$studentId])) {
                        $students[$studentId]['attendances'][$date] = $attendance->attendance_status;
                    }
                }
            }

            // Calculate statistics
            $totalRecords = 0;
            $presentRecords = 0;
            foreach ($monthAttendances as $dateAttendances) {
                $totalRecords += $dateAttendances->count();
                $presentRecords += $dateAttendances->where('attendance_status', 'present')->count();
            }
            $attendanceRate = $totalRecords > 0 ? round(($presentRecords / $totalRecords) * 100) : 0;
            $firstRecord = reset($monthAttendances)[0];

            $html .= '
        <div class="month-section">
            <div class="time-duration-header">
                Attendance Report for: <strong>' . $monthName . '</strong>
                | Attendance Rate: <strong>' . $attendanceRate . '%</strong>
            </div>

            <div class="summary-section">
                <div class="summary-content">
                    <div class="course-details">
                        <p style="text-transform: uppercase"><span class="bold">Class:</span> ' . ($firstRecord->class_name ?? 'N/A') . '</p>
                        <p><span class="bold">Report Date:</span>
                            ' . Carbon::parse($datesInMonth[0])->format('d/m/Y') . ' -
                            ' . Carbon::parse($datesInMonth[count($datesInMonth) - 1])->format('d/m/Y') . '
                        </p>
                        <p><span class="bold">Total Students:</span> ' . count($students) . '</p>
                    </div>

                    <div class="grade-summary">
                        <p class="title">Attendance Rate</p>
                        <div style="text-align: center; font-size: 18px; font-weight: bold; color: #28a745;">
                            ' . $attendanceRate . '%
                        </div>
                        <div class="progress mt-3">
                            <div class="progress-bar" style="width: ' . $attendanceRate . '%;"></div>
                        </div>
                    </div>
                </div>
            </div>

            <h5 class="summary-header">Student Attendance Records - ' . $monthName . '</h5>
            <div class="table-container">
                <table class="attendance-table">
                    <thead>
                        <tr>
                            <th class="col-number">#</th>
                            <th class="col-name">Student\'s Name</th>
                            <th class="col-gender">Gender</th>
                            <th class="col-stream">Stream</th>';

            foreach ($datesInMonth as $date) {
                $html .= '<th class="col-date">' . Carbon::parse($date)->format('d') . '</th>';
            }

            $html .= '            </tr>
                        </thead>
                        <tbody>';

            foreach ($students as $index => $student) {
                $html .= '<tr>
                <td class="col-number">' . ($index + 1) . '</td>
                <td class="col-name">' . ucwords(strtolower($student['name'])) . '</td>
                <td class="col-gender">' . strtoupper($student['gender']) . '</td>
                <td class="col-stream">' . strtoupper($student['group']) . '</td>';

                foreach ($datesInMonth as $date) {
                    $status = $student['attendances'][$date] ?? 'A';

                    if ($status === 'present') {
                        $symbol = 'P';
                        $class = 'attendance-present';
                    } elseif ($status === 'absent') {
                        $symbol = 'A';
                        $class = 'attendance-absent';
                    } elseif ($status === 'permission') {
                        $symbol = '*';
                        $class = 'attendance-permission';
                    } else {
                        $symbol = '?';
                        $class = 'attendance-absent';
                    }

                    $html .= '<td class="' . $class . '">' . $symbol . '</td>';
                }

                $html .= '</tr>';
            }

            $html .= '        </tbody>
                </table>
            </div>

            <div class="legend">
                <span class="legend-item"><span class="attendance-present">P</span> = Present</span>
                <span class="legend-item"><span class="attendance-absent">A</span> = Absent</span>
                <span class="legend-item"><span class="attendance-permission">*</span> = Permission</span>
            </div>
        </div>';
        }

        $html .= '</div>';

        return $html;
    }

    /**
     * Lightweight fallback HTML for large datasets
     */
    private function generateAttendanceHtmlOptimized($datas)
    {
        $html = '<div class="attendance-report optimized">';

        foreach ($datas as $date => $attendances) {
            $html .= '<div class="date-section">';
            $html .= '<h4>' . Carbon::parse($date)->format('F j, Y') . '</h4>';
            $html .= '<table class="attendance-table compact">';
            $html .= '<thead><tr><th>Student</th><th>Status</th></tr></thead><tbody>';

            foreach ($attendances as $attendance) {
                $status = $attendance->attendance_status;
                $statusText = ucfirst($status);
                $statusClass = $status === 'present' ? 'present' : ($status === 'absent' ? 'absent' : 'permission');

                $studentName = ucwords(strtolower(
                    $attendance->first_name . ' ' .
                        ($attendance->middle_name ? $attendance->middle_name . ' ' : '') .
                        $attendance->last_name
                ));

                $html .= '<tr>';
                $html .= '<td>' . $studentName . '</td>';
                $html .= '<td class="attendance-' . $statusClass . '">' . $statusText . '</td>';
                $html .= '</tr>';
            }

            $html .= '</tbody></table></div>';
        }

        $html .= '</div>';
        return $html;
    }
}
