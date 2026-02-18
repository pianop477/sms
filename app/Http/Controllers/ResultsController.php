<?php

namespace App\Http\Controllers;

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use App\Exports\ResultsExport;
use App\Models\compiled_results;
use App\Models\Examination;
use App\Models\Examination_result;
use App\Models\generated_reports;
use App\Models\Grade;
use App\Models\Parents;
use App\Models\school;
use App\Models\Student;
use App\Models\Subject;
use App\Models\temporary_results;
use App\Models\User;
use App\Services\BeemSmsService;
use App\Services\NextSmsService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use RealRashid\SweetAlert\Facades\Alert as FacadesAlert;
use RealRashid\SweetAlert\Facades\Alert;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Crypt;
use Endroid\QrCode\Builder\Builder;
use Maatwebsite\Excel\Facades\Excel;

class ResultsController extends Controller
{
    protected $beemSmsService;
    protected $nextSmsService;

    public function __construct(BeemSmsService $beemSmsService, NextSmsService $nextSmsService)
    {
        $this->beemSmsService = $beemSmsService;
        $this->nextSmsService = $nextSmsService;
    }

    public function index($student)
    {
        $decoded = Hashids::decode($student);

        $students = Student::query()
            ->join('grades', 'grades.id', '=', 'students.class_id')
            ->select('students.*', 'grades.class_name', 'grades.class_code')
            ->find($decoded[0]);
        if (! $students) {
            Alert()->toast('No such student was found', 'error');
            return back();
        }

        $user = Auth::user();
        $parent = Parents::where('user_id', $user->id)->first();

        if ($students->parent_id != $parent->id) {
            Alert()->toast('You are not authorized to access this page', 'error');
            return to_route('home');
        }

        $results = Examination_result::query()
            ->join('students', 'students.id', '=', 'examination_results.student_id')
            ->select('examination_results.*', 'students.parent_id')
            ->where('student_id', $students->id)
            ->where('students.parent_id', $parent->id)
            ->where('examination_results.school_id', $user->school_id)
            ->where('examination_results.status', 2) // Assuming 2 is the status for published results
            ->orderBy('examination_results.exam_date', 'DESC')
            ->get();

        // return $results;
        $groupedData = $results->groupBy(function ($item) {
            return Carbon::parse($item->exam_date)->format('Y');
        })->map(function ($yearGroup) {
            return $yearGroup->groupBy('exam_type');
        });

        return view('Results.parent_grouped_results', compact('groupedData', 'students', 'student'));
    }

    /**
     * Show the form for creating the resource.
     */


    public function resultByType($student, $year)
    {
        $decoded = Hashids::decode($student);

        $students = Student::findOrFail($decoded[0]);

        //make variables for compact logic

        if (! $students) {
            Alert()->toast('No such student was found', 'error');
            return back();
        }
        $user = Auth::user();
        $parent = Parents::where('user_id', $user->id)->first();

        if ($students->parent_id != $parent->id) {
            Alert()->toast('You are not authorized to access this page', 'error');
            return to_route('home');
        }
        $examTypes = Examination_result::query()
            ->join('examinations', 'examinations.id', '=', 'examination_results.exam_type_id')
            ->join('students', 'students.id', '=', 'examination_results.student_id')
            ->select('examinations.exam_type', 'examinations.id as exam_id', 'students.parent_id')
            ->distinct()
            ->whereYear('exam_date', $year)
            ->where('student_id', $students->id)
            ->where('examination_results.school_id', $students->school_id)
            ->where('students.parent_id', $parent->id)
            ->where('examination_results.status', 2)
            ->orderBy('examinations.exam_type', 'asc')
            ->get();

        $classIdsForYear = Examination_result::where('student_id', $students->id)
            ->where('school_id', $students->school_id)
            ->whereYear('exam_date', $year)
            ->pluck('class_id')
            ->unique()
            ->values();


        // Check for combined examination results
        $reports = generated_reports::where('school_id', $students->school_id)
            ->where('status', 1)
            ->whereYear('created_at', $year)
            ->whereIn('class_id', $classIdsForYear)
            ->orderBy('created_at', 'DESC')
            ->get();


        // Combine both $examTypes and $reports into one array
        $combinedItems = collect();

        // Add exam types
        foreach ($examTypes as $exam) {
            $combinedItems->push([
                'type' => 'exam',
                'id' => $exam->exam_id,
                'label' => $exam->exam_type
            ]);
        }

        // Add generated reports
        foreach ($reports as $report) {
            $combinedItems->push([
                'type' => 'report',
                'id' => $report->id,
                'label' => $report->title ?? 'Generated Report',
                'class_id' => $report->class_id,
            ]);
        }

        // Sort alphabetically by label
        $combined = $combinedItems->sortBy('label')->values();

        // ✅ Paginate manually
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 10;
        $currentItems = $combined->slice(($currentPage - 1) * $perPage, $perPage)->values();
        $paginated = new LengthAwarePaginator($currentItems, $combined->count(), $perPage, $currentPage, [
            'path' => request()->url(),
            'query' => request()->query(),
        ]);

        // Pass the combinedItems to the view
        return view('Results.result_type', compact('students', 'year', 'paginated'));
    }

    public function resultByMonth($student, $year, $exam_type)
    {
        $student_id = Hashids::decode($student);
        $exam_id = Hashids::decode($exam_type);

        $students = Student::find($student_id[0]);
        $user = Auth::user();
        $parent = Parents::where('user_id', $user->id)->first();

        if ($students->parent_id != $parent->id) {
            Alert()->toast('You are not authorized to access this page', 'error');
            return to_route('home');
        }
        // Pata matokeo yote ya mwanafunzi kwa mwaka husika na aina ya mtihani
        $results = Examination_result::query()
            ->join('students', 'students.id', '=', 'examination_results.student_id')
            ->selectRaw('exam_date') // Chukua tarehe kamili pekee
            ->whereYear('examination_results.exam_date', $year)
            ->where('examination_results.exam_type_id', $exam_id)
            ->where('examination_results.status', 2)
            ->where('examination_results.school_id', $user->school_id)
            ->where('students.parent_id', $parent->id)
            ->where('examination_results.student_id', $students->id)
            ->orderBy('examination_results.exam_date') // Sorting ya moja kwa moja kabla ya grouping
            ->get();

        // Grouping kwa mwezi, halafu grouping kwa tarehe ndani ya mwezi
        $months = $results->sortBy('exam_date')->groupBy(function ($item) {
            return Carbon::parse($item->exam_date)->format('F'); // Group by mwezi kamili (e.g., January)
        })->map(function ($monthData) {
            return $monthData->sortBy('exam_date')->groupBy(function ($item) {
                return Carbon::parse($item->exam_date)->format('d F Y'); // Group by tarehe kamili
            });
        });

        $examType = Examination::find($exam_id);

        return view('Results.result_months', compact('students', 'year', 'examType', 'exam_id', 'months'));
    }

    /**
     * Store the newly created resource in storage.
     */
    public function viewStudentResult($student, $year, $type, $month, $date)
    {
        $exam_id = Hashids::decode($type);
        $student_id = Hashids::decode($student);
        $studentId = Student::findOrFail($student_id[0]);
        $user = Auth::user();
        $parent = Parents::where('user_id', $user->id)->first();

        $results = Examination_result::query()
            ->join('students', 'students.id', '=', 'examination_results.student_id')
            ->join('subjects', 'subjects.id', '=', 'examination_results.course_id')
            ->join('grades', 'grades.id', '=', 'examination_results.class_id')
            ->join('examinations', 'examinations.id', '=', 'examination_results.exam_type_id')
            ->join('teachers', 'teachers.id', '=', 'examination_results.teacher_id')
            ->leftJoin('users', 'users.id', '=', 'teachers.user_id')
            ->join('schools', 'schools.id', '=', 'examination_results.school_id')
            ->select(
                'examination_results.*',
                'students.id as studentId',
                'students.first_name',
                'students.middle_name',
                'students.last_name',
                'students.admission_number',
                'students.group',
                'students.image',
                'students.gender',
                'students.admission_number',
                'students.parent_id',
                'subjects.course_name',
                'subjects.course_code',
                'grades.class_name',
                'grades.class_code',
                'examinations.exam_type',
                'users.first_name as teacher_first_name',
                'users.last_name as teacher_last_name',
                'schools.school_name',
                'schools.school_reg_no',
                'schools.postal_address',
                'schools.postal_name',
                'schools.logo',
                'schools.country',
            )
            ->where('examination_results.student_id', $studentId->id)
            ->where('examination_results.exam_type_id', $exam_id[0])
            ->whereDate('exam_date', Carbon::parse($date))
            ->where('examination_results.school_id', $user->school_id)
            ->where('examination_results.status', 2)
            ->where('students.parent_id', $parent->id)
            ->get();

        if ($results->isEmpty()) {
            Alert()->toast('No results found for this student', 'info');
            return redirect()->back();
        }

        // Get marking style
        $marking_style = $results->first()->marking_style ?? 1;

        // Calculate the sum of all scores
        $totalScore = $results->sum('score');
        $averageScore = $results->count() > 0 ? $totalScore / $results->count() : 0;

        // For marking style 3, calculate aggregate and division
        if ($marking_style == 3) {
            // Calculate aggregate points for division
            $aggregatePoints = 0;
            $gradePoints = [
                'A' => 1,
                'B' => 2,
                'C' => 3,
                'D' => 4,
                'F' => 5,
                'ABS' => 6
            ];

            foreach ($results as $result) {
                $courseGrade = $this->calculateGrade($result->score, $marking_style);
                $aggregatePoints += $gradePoints[$courseGrade] ?? 6;
            }

            // Calculate division based on aggregate points
            $division = $this->calculateDivisionForStyle3($aggregatePoints, $results->count());
        } else {
            $aggregatePoints = null;
            $division = null;
        }

        // ==== REKEDISHA: Kupata class_id ya mtihani husika ====
        $examClassId = null;
        if ($results->isNotEmpty()) {
            $examClassId = $results->first()->class_id;
        }

        // Calculate rankings - tumia examClassId badala ya student->class_id
        $rankings = Examination_result::query()
            ->where('examination_results.class_id', $examClassId)
            ->whereDate('exam_date', Carbon::parse($date))
            ->where('examination_results.exam_type_id', $exam_id[0])
            ->where('examination_results.school_id', $user->school_id)
            ->join('students', 'students.id', '=', 'examination_results.student_id')
            ->select('student_id', DB::raw('SUM(score) as total_score'))
            ->groupBy('student_id')
            ->orderByDesc('total_score')
            ->get();

        // Kutengeneza mfumo wa tie ranking
        $rank = 1;
        $previousScore = null;
        $ranks = [];

        foreach ($rankings as $key => $ranking) {
            if ($previousScore !== null && $ranking->total_score < $previousScore) {
                $rank = $key + 1;
            }
            $ranks[$ranking->student_id] = $rank;
            $previousScore = $ranking->total_score;
        }

        // Kupata rank ya mwanafunzi husika
        $studentRank = $ranks[$studentId->id] ?? null;

        // Add grades, remarks, and individual ranks to each result
        foreach ($results as $result) {
            // Add grade and remarks
            $gradeResult = $this->calculateGrade($result->score, $marking_style);
            $result->grade = $gradeResult;

            // Determine remarks based on grade and marking style
            if ($marking_style == 1) {
                if ($result->score >= 41) {
                    $result->remarks = 'Excellent';
                } elseif ($result->score >= 31) {
                    $result->remarks = 'Good';
                } elseif ($result->score >= 21) {
                    $result->remarks = 'Pass';
                } elseif ($result->score >= 11) {
                    $result->remarks = 'Poor';
                } else {
                    $result->remarks = 'Fail';
                }
            } elseif ($marking_style == 2) {
                if ($result->score >= 81) {
                    $result->remarks = 'Excellent';
                } elseif ($result->score >= 61) {
                    $result->remarks = 'Good';
                } elseif ($result->score >= 41) {
                    $result->remarks = 'Pass';
                } elseif ($result->score >= 21) {
                    $result->remarks = 'Poor';
                } else {
                    $result->remarks = 'Fail';
                }
            } else {
                // For marking style 3
                if ($gradeResult == 'A') {
                    $result->remarks = 'Excellent';
                } elseif ($gradeResult == 'B') {
                    $result->remarks = 'Good';
                } elseif ($gradeResult == 'C') {
                    $result->remarks = 'Pass';
                } elseif ($gradeResult == 'D') {
                    $result->remarks = 'Unsatisfactory';
                } elseif ($gradeResult == 'F') {
                    $result->remarks = 'Fail';
                } else {
                    $result->remarks = 'Absent';
                }
            }

            // ==== REKEDISHA: Course ranking - tumia examClassId ====
            $courseRankings = Examination_result::query()
                ->where('course_id', $result->course_id)
                ->where('examination_results.class_id', $examClassId)
                ->whereDate('exam_date', Carbon::parse($date))
                ->where('examination_results.exam_type_id', $exam_id[0])
                ->where('examination_results.school_id', $user->school_id)
                ->join('students', 'students.id', '=', 'examination_results.student_id')
                ->select('student_id', DB::raw('SUM(score) as total_score'))
                ->groupBy('student_id')
                ->orderByDesc('total_score')
                ->get();

            // Hakikisha wanafunzi wenye score sawa wanashirikiana rank
            $rank = 1;
            $previousScore = null;
            $courseRanks = [];

            foreach ($courseRankings as $key => $ranking) {
                if ($previousScore !== null && $ranking->total_score < $previousScore) {
                    $rank = $key + 1;
                }
                $courseRanks[$ranking->student_id] = $rank;
                $previousScore = $ranking->total_score;
            }

            // Kupata rank ya mwanafunzi husika kwa somo husika
            $result->courseRank = $courseRanks[$studentId->id] ?? null;
        }

        // ================= QR CODE VERIFICATION =================
        $verificationData = [
            'student_name' => trim($studentId->first_name . ' ' . $studentId->middle_name . ' ' . $studentId->last_name),
            'admission_number' => $studentId->admission_number,
            'class' => $results->first()->class_name ?? '-',
            'report_type' => $results->first()->exam_type . ' Assessment',
            'term' => $results->first()->Exam_term ?? '-',
            'school' => $results->first()->school_name ?? '-',
            'report_date' => Carbon::parse($date)->format('Y-m-d'),
            'report_id' => sha1($studentId->id . $exam_id[0] . $date),
            'issued_at' => now()->timestamp,
            'total_score' => $totalScore,
            'average_score' => $averageScore,
            'student_rank' => $studentRank,
            'total_students' => $rankings->count(),
            'marking_style' => $marking_style,
        ];

        // Add division info for marking style 3
        if ($marking_style == 3) {
            $verificationData['division'] = $division;
            $verificationData['aggregate_points'] = $aggregatePoints;
        }

        $verificationData['signature'] = hash_hmac('sha256', json_encode($verificationData), config('app.key'));
        $encryptedPayload = Crypt::encryptString(json_encode($verificationData));
        $verificationUrl = route('report.verify', ['payload' => $encryptedPayload]);

        $resultQr = Builder::create()
            ->writer(new PngWriter())
            ->data($verificationUrl)
            ->size(140)
            ->margin(4)
            ->build();

        $qrPng = base64_encode($resultQr->getString());

        // Generate the PDF
        $pdf = \PDF::loadView('Results.parent_results', compact(
            'results',
            'year',
            'qrPng',
            'studentId',
            'type',
            'student',
            'month',
            'date',
            'totalScore',
            'averageScore',
            'studentRank',
            'rankings',
            'marking_style',
            'aggregatePoints',
            'division'
        ));

        // Generate filename using timestamp
        $timestamp = Carbon::now()->timestamp;
        $fileName = "student_result_{$studentId->admission_number}_{$timestamp}.pdf";
        $folderPath = public_path('reports');

        // Make sure the directory exists
        if (!File::exists($folderPath)) {
            File::makeDirectory($folderPath, 0755, true);
        }

        // Save the PDF to the 'reports' folder
        $pdf->save($folderPath . '/' . $fileName);

        // Generate the URL for accessing the saved PDF
        $fileUrl = asset('reports/' . $fileName);

        // Return the view with the file URL to be used in the iframe
        return view('Results.parent_academic_reports', compact(
            'fileUrl',
            'fileName',
            'exam_id',
            'results',
            'year',
            'studentId',
            'type',
            'student',
            'month',
            'date',
            'marking_style'
        ));
    }

    //general results are intialized here ====================================
    public function general($school)
    {
        $id = Hashids::decode($school);
        // return $id;
        $user = Auth::user();

        $schools = school::find($id[0]);
        // return $schools;
        if ($user->school_id != $schools->id) {
            Alert()->toast('You are not authorized to view this page', 'error');
            return redirect()->route('error.page');
        }

        $results = Examination_result::query()
            ->join('students', 'students.id', '=', 'examination_results.student_id')
            ->join('grades', 'grades.id', '=', 'examination_results.class_id')
            ->select(
                'examination_results.*',
                'grades.id as class_id',
                'grades.class_name',
                'grades.class_code'
            )
            ->where('examination_results.school_id', $schools->id)
            ->orderBy('examination_results.exam_date', 'DESC')
            ->get();
        // return $results;
        $groupedData = $results->groupBy(function ($item) {
            return Carbon::parse($item->exam_date)->format('Y');
        })->map(function ($yearGroup) {
            return $yearGroup->groupBy('class_id');
        });

        return view('Results.general_year_result', compact('schools', 'results', 'groupedData'));
    }

    public function classesByYear($school, $year)
    {
        $id = Hashids::decode($school);
        $user = Auth::user();

        $schools = school::find($id[0]);

        if ($user->school_id != $schools->id) {
            Alert()->toast('You are not authorized to view this page', 'error');
            return redirect()->route('error.page');
        } else {
            $results = Examination_result::query()
                ->join('grades', 'grades.id', '=', 'examination_results.class_id')
                ->select(
                    'examination_results.*',
                    'grades.id as class_id',
                    'grades.class_name',
                    'grades.class_code'
                )
                ->where('examination_results.school_id', $schools->id)
                ->whereYear('examination_results.exam_date', $year)
                ->orderBy('grades.class_code', 'ASC')
                ->get();

            $groupedByClass = $results->groupBy('class_id');

            return view('Results.results_grouped_byYear', compact('schools', 'year', 'groupedByClass'));
        }
    }

    public function examTypesByClass($school, $year, $class)
    {
        $school_id = Hashids::decode($school);
        $class_id = Hashids::decode($class);
        $user = Auth::user();

        $schools = school::find($school_id[0]);
        $classes = Grade::find($class_id[0]);
        // return $classes;

        $results = Examination_result::query()
            ->join('examinations', 'examinations.id', '=', 'examination_results.exam_type_id')
            ->select(
                'examination_results.*',
                'examinations.id as exam_type_id',
                'examinations.exam_type'
            )
            ->where('examination_results.school_id', $schools->id)
            ->whereYear('examination_results.exam_date', $year)
            ->where('examination_results.class_id', $classes->id)
            ->get();

        $months = [
            'January' => 1,
            'February' => 2,
            'March' => 3,
            'April' => 4,
            'May' => 5,
            'June' => 6,
            'July' => 7,
            'August' => 8,
            'September' => 9,
            'October' => 10,
            'November' => 11,
            'December' => 12
        ];

        //query examination lists
        $exams = Examination::where('status', 1)->where('school_id', $user->school_id)->orderBy('exam_type')->get();

        //query examination_results by for the specific class which exists in the db table
        $monthsResult = Examination_result::query()
            ->join('examinations', 'examinations.id', '=', 'examination_results.exam_type_id')
            ->select('examination_results.*', 'examinations.exam_type', 'examinations.symbolic_abbr')
            ->where('class_id', $classes->id)
            ->whereYear('examination_results.exam_date', $year)
            ->where('examination_results.school_id', $schools->id)
            ->orderBy('examination_results.exam_date')
            ->get();

        $groupedByMonth = $monthsResult->groupBy(function ($item) {
            return Carbon::parse($item->exam_date)->format('Y-m-d');
        });

        //get compiled results
        $compiled_results = compiled_results::where('school_id', $schools->id)
            ->where('class_id', $classes->id)
            ->get();

        $groupedByExamType = $results->groupBy('exam_type_id'); // Group by exam type using results
        $compiledGroupByExam = $compiled_results->groupBy('report_name'); // Group by exam type using compiled results

        $reports = generated_reports::query()
            ->join('users', 'users.id', '=', 'generated_reports.created_by')
            ->select('generated_reports.*', 'users.first_name', 'users.last_name')
            ->where('generated_reports.school_id', $schools->id)
            ->where('generated_reports.class_id', $classes->id)
            ->whereYear('generated_reports.created_at', $year)
            ->orderBy('generated_reports.created_at', 'desc')
            ->paginate(5);

        return view('Results.general_result_type', compact('schools', 'reports', 'groupedByMonth', 'compiledGroupByExam', 'year', 'exams', 'classes', 'groupedByExamType'));
    }

    //function for displaying general results by term ***************************************
    public function monthsByExamType($school, $year, $class, $examType)
    {
        $school_id = Hashids::decode($school);
        $class_id = Hashids::decode($class);
        $exam_id = Hashids::decode($examType);

        $user = Auth::user();
        $schools = School::find($school_id[0]);

        if ($user->school_id != $schools->id) {
            Alert()->toast('You are not authorized to view this page', 'error');
            return redirect()->route('error.page');
        }

        $results = Examination_result::query()
            ->join('students', 'students.id', '=', 'examination_results.student_id')
            ->join('grades', 'grades.id', '=', 'examination_results.class_id')
            ->join('examinations', 'examinations.id', '=', 'examination_results.exam_type_id')
            ->select(
                'examination_results.*',
                'students.first_name',
                'students.middle_name',
                'students.last_name',
                'students.id as student_id',
                'students.admission_number',
                'grades.class_name',
                'examinations.exam_type'
            )
            ->where('examination_results.school_id', $schools->id)
            ->whereYear('examination_results.exam_date', $year)
            ->where('examination_results.class_id', $class_id[0])
            ->where('examination_results.exam_type_id', $exam_id[0])
            ->orderBy('examination_results.exam_date') // Panga kwa tarehe
            ->get();

        // Group by Month, then by Date
        $groupedByMonth = $results->groupBy(function ($item) {
            return Carbon::parse($item->exam_date)->format('F'); // Month name
        })->map(function ($monthGroup) {
            return $monthGroup->groupBy(function ($item) {
                return Carbon::parse($item->exam_date)->format('Y-m-d'); // Group by exact date
            })->map(function ($dateGroup) {
                // Amua status kwa kila tarehe
                $status = $dateGroup->first()->status; // Chukua status ya kwanza ya matokeo ya tarehe hiyo
                return [
                    'results' => $dateGroup,
                    'status' => $status, // Pita status kwenye view
                ];
            });
        });
        $classes = Grade::find($class_id[0]);
        $exams = Examination::find($exam_id[0]);

        return view('Results.months_by_exam_type', compact('schools', 'results', 'year', 'classes', 'exams', 'class_id', 'exam_id', 'groupedByMonth'));
    }

    public function getCoursesByTeacher(Request $request)
    {
        try {
            $request->validate([
                'teacher_id' => 'required',
                'school' => 'required',
                'class' => 'required',
                'examType' => 'required',
                'date' => 'required|date',
            ]);

            $teacherDecoded = Hashids::decode($request->teacher_id);
            $schoolDecoded  = Hashids::decode($request->school);
            $classDecoded   = Hashids::decode($request->class);
            $examDecoded    = Hashids::decode($request->examType);

            if (
                empty($teacherDecoded) ||
                empty($schoolDecoded) ||
                empty($classDecoded) ||
                empty($examDecoded)
            ) {
                return response()->json([], 400);
            }

            $teacher_id = $teacherDecoded[0];
            $school_id  = $schoolDecoded[0];
            $class_id   = $classDecoded[0];
            $exam_type  = $examDecoded[0];

            $courses = Examination_result::query()
                ->where('examination_results.school_id', $school_id)
                ->where('class_id', $class_id)
                ->where('exam_type_id', $exam_type)
                ->where('teacher_id', $teacher_id)
                ->whereDate('exam_date', $request->date)
                ->join('subjects', 'subjects.id', '=', 'examination_results.course_id')
                ->select('course_id', 'course_name', 'course_code')
                ->distinct()
                ->get();

            return response()->json(
                $courses->map(fn($c) => [
                    'id' => Hashids::encode($c->course_id),
                    'name' => $c->course_name . ' (' . $c->course_code . ')'
                ])
            );
        } catch (\Throwable $e) {
            Log::error('getCoursesByTeacher error', [
                'msg' => $e->getMessage(),
                'line' => $e->getLine()
            ]);

            return response()->json([], 500);
        }
    }

    public function resultsByMonth($school, $year, $class, $examType, $month, $date, Request $request)
    {
        // Decode the IDs
        $school_id = Hashids::decode($school);
        $class_id = Hashids::decode($class);
        $exam_id = Hashids::decode($examType);

        // Get authenticated user
        $user = Auth::user();

        // Get school
        $schools = School::find($school_id[0]);

        // Authorization check
        if ($user->school_id != $schools->id) {
            Alert()->toast('You are not authorized to view this page', 'error');
            return redirect()->route('error.page');
        }

        // Map month names to numbers
        $monthsArray = [
            'January' => 1,
            'February' => 2,
            'March' => 3,
            'April' => 4,
            'May' => 5,
            'June' => 6,
            'July' => 7,
            'August' => 8,
            'September' => 9,
            'October' => 10,
            'November' => 11,
            'December' => 12,
        ];

        $monthNumber = $monthsArray[$month];

        // Get marking style for this exam
        $marking_style = Examination_result::query()
            ->where('school_id', $schools->id)
            ->where('class_id', $class_id[0])
            ->where('exam_type_id', $exam_id[0])
            ->whereDate('exam_date', $date)
            ->value('marking_style') ?? 1;

        // Query for the examination results
        $results = Examination_result::query()
            ->join('students', 'students.id', '=', 'examination_results.student_id')
            ->join('subjects', 'subjects.id', '=', 'examination_results.course_id')
            ->join('grades', 'grades.id', '=', 'examination_results.class_id')
            ->join('examinations', 'examinations.id', '=', 'examination_results.exam_type_id')
            ->select(
                'examination_results.*',
                'students.first_name',
                'students.middle_name',
                'students.last_name',
                'students.gender',
                'students.id as student_id',
                'students.group',
                'students.admission_number',
                'grades.class_name',
                'students.status',
                'examinations.exam_type',
                'subjects.course_name',
                'subjects.course_code'
            )
            ->where('examination_results.school_id', $schools->id)
            ->where('examination_results.class_id', $class_id[0])
            ->where('examination_results.exam_type_id', $exam_id[0])
            ->whereDate('examination_results.exam_date', $date)
            ->get();

        if ($results->isEmpty()) {
            Alert()->toast('No results found for this selection', 'info');
            return redirect()->route('results.general', ['school' => $school]);
        }

        // Filter students by class_id
        $studentsByClass = $results->where('class_id', $class_id[0])->groupBy('student_id');

        $totalMaleStudents = $studentsByClass->filter(fn($student) => strtolower($student->first()->gender) === 'male')->count();
        $totalFemaleStudents = $studentsByClass->filter(fn($student) => strtolower($student->first()->gender) === 'female')->count();

        // Average score per course with grade and course name
        $averageScoresByCourse = $results->groupBy('course_id')->map(function ($courseResults) use ($marking_style) {
            $averageScore = $courseResults->avg('score');
            return [
                'course_name' => $courseResults->first()->course_name,
                'course_code' => $courseResults->first()->course_code,
                'average_score' => $averageScore,
                'grade' => $this->calculateGrade($averageScore, $marking_style)
            ];
        });

        // Sum of all course averages
        $sumOfCourseAverages = $averageScoresByCourse->sum('average_score');

        // Sort courses by average score to determine position
        $sortedCourses = $averageScoresByCourse->sortByDesc('average_score')->values()->all();
        foreach ($sortedCourses as $index => &$course) {
            $course['position'] = $index + 1;
        }

        // Initialize division aggregate for marking style 3
        $divisionAggregate = [];
        if ($marking_style == 3) {
            $divisionAggregate = [
                'I' => 0,
                'II' => 0,
                'III' => 0,
                'IV' => 0,
                '0' => 0
            ];
        }

        // Evaluation score table
        $evaluationScores = $results->groupBy('course_id')->map(function ($courseResults) use ($marking_style) {
            $grades = [
                'A' => 0,
                'B' => 0,
                'C' => 0,
                'D' => 0,
                'E' => 0,
                'F' => 0,
                'ABS' => 0,
            ];

            foreach ($courseResults as $result) {
                $grade = $this->calculateGrade($result->score, $marking_style);
                $grades[$grade]++;
            }

            return $grades;
        });

        $courses = Subject::all();

        // Total average of all courses
        $totalAverageScore = $results->avg('score');

        // Student results with total marks, average, grade, and position
        $studentsResults = $results->groupBy('student_id')->map(function ($studentResults) use ($marking_style, &$divisionAggregate) {
            $totalMarks = $studentResults->sum('score');
            $average = $studentResults->avg('score');

            // Normalize gender to lowercase
            $gender = strtolower($studentResults->first()->gender);

            // For marking style 3
            if ($marking_style == 3) {
                $grade = $average == 0 ? 'ABS' : $this->calculateGrade($average, $marking_style);

                // Calculate aggregate points for division
                $aggregatePoints = 0;
                $gradePoints = [
                    'A' => 1,
                    'B' => 2,
                    'C' => 3,
                    'D' => 4,
                    'F' => 5,
                    'ABS' => 6
                ];

                foreach ($studentResults as $result) {
                    $courseGrade = $this->calculateGrade($result->score, $marking_style);
                    $aggregatePoints += $gradePoints[$courseGrade] ?? 6;
                }

                // Calculate division based on aggregate points
                $division = $this->calculateDivisionForStyle3($aggregatePoints, $studentResults->count());

                // HAPA NDIO TUNAHESABU DIVISION - MARA MOJA TU!
                if (isset($divisionAggregate[$division])) {
                    $divisionAggregate[$division]++;
                }

                return [
                    'student_id' => $studentResults->first()->student_id,
                    'admission_number' => $studentResults->first()->admission_number,
                    'student_name' => trim($studentResults->first()->first_name . ' ' .
                        $studentResults->first()->middle_name . ' ' .
                        $studentResults->first()->last_name),
                    'gender' => $gender, // Tumia gender iliyorekebishwa
                    'courses' => $studentResults->map(function ($result) use ($marking_style) {
                        return [
                            'course_id' => $result->course_id,
                            'course_name' => $result->course_name,
                            'score' => $result->score,
                            'grade' => $this->calculateGrade($result->score, $marking_style),
                        ];
                    }),
                    'group' => $studentResults->first()->group,
                    'total_marks' => $totalMarks,
                    'average' => $average,
                    'grade' => $grade,
                    'aggregate_points' => $aggregatePoints,
                    'division' => $division,
                ];
            }
            // For marking styles 1 and 2
            else {
                $grade = $average == 0 ? 'ABS' : $this->calculateGrade($average, $marking_style);

                return [
                    'student_id' => $studentResults->first()->student_id,
                    'admission_number' => $studentResults->first()->admission_number,
                    'student_name' => trim($studentResults->first()->first_name . ' ' .
                        $studentResults->first()->middle_name . ' ' .
                        $studentResults->first()->last_name),
                    'gender' => $gender, // Tumia gender iliyorekebishwa
                    'courses' => $studentResults->map(function ($result) use ($marking_style) {
                        return [
                            'course_id' => $result->course_id,
                            'course_name' => $result->course_name,
                            'score' => $result->score,
                            'grade' => $this->calculateGrade($result->score, $marking_style),
                        ];
                    }),
                    'group' => $studentResults->first()->group,
                    'total_marks' => $totalMarks,
                    'average' => $average,
                    'grade' => $grade,
                    'aggregate_points' => null,
                    'division' => null,
                ];
            }
        });

        // Sort students based on marking style
        if ($marking_style == 3) {
            $sortedStudentsResults = $studentsResults->sortBy(function ($student) {
                return $student['aggregate_points'];
            })->values()->all();
        } else {
            $sortedStudentsResults = $studentsResults->sortByDesc('total_marks')->values()->all();
        }

        // Calculate positions
        $lastValue = null;
        $position = 0;
        $counter = 0;

        foreach ($sortedStudentsResults as $index => &$studentResult) {
            $counter++;

            if ($marking_style == 3) {
                if ($studentResult['aggregate_points'] !== $lastValue) {
                    $position = $counter;
                    $lastValue = $studentResult['aggregate_points'];
                }
            } else {
                if ($studentResult['total_marks'] !== $lastValue) {
                    $position = $counter;
                    $lastValue = $studentResult['total_marks'];
                }
            }

            $studentResult['position'] = $position;
        }

        $totalUniqueStudents = $studentsByClass->count();

        // Count grades by gender based on overall student performance
        $gradesByGender = $studentsResults->groupBy('gender')->map(function ($group) use ($marking_style) {
            if ($marking_style == 1 || $marking_style == 2) {
                $grades = ['A' => 0, 'B' => 0, 'C' => 0, 'D' => 0, 'E' => 0];
            } else {
                $grades = ['A' => 0, 'B' => 0, 'C' => 0, 'D' => 0, 'E' => 0, 'F' => 0, 'ABS' => 0];
            }

            foreach ($group as $student) {
                $studentGrade = $student['grade'];

                if ($marking_style == 1 || $marking_style == 2) {
                    if (in_array($studentGrade, ['A', 'B', 'C', 'D', 'E'])) {
                        $grades[$studentGrade]++;
                    }
                } else {
                    if (isset($grades[$studentGrade])) {
                        $grades[$studentGrade]++;
                    }
                }
            }
            return $grades;
        });

        // Separate counts for male and female grades
        $totalMaleGrades = $gradesByGender->get('male', []);
        $totalFemaleGrades = $gradesByGender->get('female', []);

        // Count unique students
        $totalUniqueStudents = $results->pluck('student_id')->unique()->count();

        // Total subjects added in the results
        $totalSubjects = $results->pluck('course_id')->unique()->count();

        // Class total average
        $generalClassAvg = $totalSubjects > 0 ? $sumOfCourseAverages / $totalSubjects : 0;

        // Count grades by subject and gender
        $subjectGradesByGender = $results->groupBy('course_id')->map(function ($courseResults) use ($marking_style) {
            $grades = [
                'A' => ['male' => 0, 'female' => 0],
                'B' => ['male' => 0, 'female' => 0],
                'C' => ['male' => 0, 'female' => 0],
                'D' => ['male' => 0, 'female' => 0],
                'E' => ['male' => 0, 'female' => 0],
                'F' => ['male' => 0, 'female' => 0],
                'ABS' => ['male' => 0, 'female' => 0],
            ];

            foreach ($courseResults as $result) {
                $grade = $this->calculateGrade($result->score, $marking_style);
                $gender = strtolower($result->gender);

                if ($gender == 'male') {
                    $grades[$grade]['male']++;
                } else {
                    $grades[$grade]['female']++;
                }
            }

            return $grades;
        });

        // Convert to excel
        if ($request->has('export_excel')) {
            return Excel::download(
                new ResultsExport(
                    $results,
                    $totalUniqueStudents,
                    $sumOfCourseAverages,
                    $generalClassAvg,
                    $totalFemaleGrades,
                    $totalMaleGrades,
                    $sortedStudentsResults,
                    $sortedCourses,
                    $subjectGradesByGender,
                    $date,
                    $courses,
                    $marking_style,
                    $divisionAggregate
                ),
                'examination results.xlsx'
            );
        }

        // Get teachers with courses
        $teachersWithCourses = Examination_result::query()
            ->where('examination_results.school_id', $schools->id)
            ->where('examination_results.class_id', $class_id[0])
            ->where('examination_results.exam_type_id', $exam_id[0])
            ->whereDate('examination_results.exam_date', $date)
            ->join('teachers', 'teachers.id', '=', 'examination_results.teacher_id')
            ->leftJoin('users', 'users.id', '=', 'teachers.user_id')
            ->join('subjects', 'subjects.id', '=', 'examination_results.course_id')
            ->select(
                'teachers.id as teacher_id',
                'users.first_name',
                'users.last_name',
                'examination_results.course_id',
                'subjects.course_name',
                'subjects.course_code'
            )
            ->get()
            ->groupBy('teacher_id');

        // Format for teachers dropdown
        $teachers = $teachersWithCourses->map(function ($teacherResults, $teacherId) {
            $firstRecord = $teacherResults->first();
            return [
                'id' => Hashids::encode($teacherId),
                'name' => $firstRecord->first_name . ' ' . $firstRecord->last_name,
                'courses' => $teacherResults->map(function ($result) {
                    return [
                        'id' => Hashids::encode($result->course_id),
                        'name' => $result->course_name . ' (' . $result->course_code . ')'
                    ];
                })->unique('id')->values()
            ];
        })->values();

        // If there's only one teacher, prepare their courses
        $firstTeacherCourses = $teachers->isNotEmpty() ? $teachers->first()['courses'] : [];

        // Generate the PDF
        $pdf = \PDF::loadView('Results.results_by_month', compact(
            'school',
            'year',
            'class',
            'examType',
            'month',
            'results',
            'totalMaleStudents',
            'totalFemaleStudents',
            'totalMaleGrades',
            'totalFemaleGrades',
            'averageScoresByCourse',
            'evaluationScores',
            'totalAverageScore',
            'date',
            'sortedStudentsResults',
            'sumOfCourseAverages',
            'sortedCourses',
            'totalUniqueStudents',
            'subjectGradesByGender',
            'courses',
            'generalClassAvg',
            'marking_style',
            'divisionAggregate'
        ));

        $pdf->setOption('isHtml5ParserEnabled', true);
        $pdf->setOption('isPhpEnabled', true);

        // Generate filename using timestamp
        $timestamp = Carbon::now()->timestamp;
        $fileName = "results_{$timestamp}.pdf";
        $folderPath = public_path('reports');

        // Make sure the directory exists
        if (!File::exists($folderPath)) {
            File::makeDirectory($folderPath, 0755, true);
        }

        // Save the PDF to the 'reports' folder
        $pdf->save($folderPath . '/' . $fileName);

        // Generate the URL for accessing the saved PDF
        $fileUrl = asset('reports/' . $fileName);

        // Return the view with the file URL
        return view('Results.class_pdf_results', compact(
            'fileUrl',
            'fileName',
            'results',
            'date',
            'schools',
            'exam_id',
            'class_id',
            'month',
            'year',
            'teachers',
            'firstTeacherCourses',
            'marking_style'
        ));
    }

    private function calculateGrade($score, $marking_style)
    {
        if ($marking_style == 1) {
            if ($score >= 40.5) {
                return 'A';
            } elseif ($score >= 30.5) {
                return 'B';
            } elseif ($score >= 20.5) {
                return 'C';
            } elseif ($score >= 10.5) {
                return 'D';
            } elseif ($score >= 0.1) {
                return 'E';
            } else {
                return 'ABS';
            }
        } elseif ($marking_style == 2) {
            if ($score >= 80.5) {
                return 'A';
            } elseif ($score >= 60.5) {
                return 'B';
            } elseif ($score >= 40.5) {
                return 'C';
            } elseif ($score >= 20.5) {
                return 'D';
            } elseif ($score >= 0.1) {
                return 'E';
            } else {
                return 'ABS';
            }
        } elseif ($marking_style == 3) {
            // For division marking style
            if ($score >= 74.5) {
                return 'A';
            } elseif ($score >= 64.5) {
                return 'B';
            } elseif ($score >= 44.5) {
                return 'C';
            } elseif ($score >= 29.5) {
                return 'D';
            } elseif ($score >= 0.1) {
                return 'F';
            } else {
                return 'ABS';
            }
        } else {
            return 'ABS';
        }
    }

    private function calculateDivisionForStyle3($aggregatePoints, $totalSubjects)
    {
        // Ensure minimum subjects requirement
        if ($totalSubjects < 7) {
            return '0'; // No division if subjects are less than 7
        }

        // Calculate average grade points
        $averageGradePoint = $aggregatePoints / $totalSubjects;

        // Convert to division scale of 7-35 by multiplying by 7
        $divisionScore = $averageGradePoint * 7;

        // Determine division based on division score
        if ($divisionScore >= 7 && $divisionScore <= 17) {
            return 'I';
        } elseif ($divisionScore >= 18 && $divisionScore <= 22) {
            return 'II';
        } elseif ($divisionScore >= 23 && $divisionScore <= 25) {
            return 'III';
        } elseif ($divisionScore >= 26 && $divisionScore <= 32) {
            return 'IV';
        } elseif ($divisionScore >= 33 && $divisionScore <= 35) {
            return '0';
        } else {
            return '0';
        }
    }

    //end of results in general ==============================================

    //publishing results to be visible to parents and send sms via api  ***************************************************
    public function publishResult(Request $request, $school, $year, $class, $examType, $month, $date)
    {
        try {
            $school_id = Hashids::decode($school);
            $class_id = Hashids::decode($class);
            $exam_id = Hashids::decode($examType);

            $user = Auth::user();
            $schools = School::find($school_id[0]);

            if ($user->school_id != $schools->id) {
                return response()->json(['success' => false, 'message' => 'You are not authorized to perform this action.', 'type' => 'error']);
            }

            // ==== GET MARKING STYLE FIRST ====
            $marking_style = Examination_result::query()
                ->where('school_id', $schools->id)
                ->where('class_id', $class_id[0])
                ->where('exam_type_id', $exam_id[0])
                ->whereDate('exam_date', $date)
                ->value('marking_style') ?? 1;

            // ==== UPDATE status in the database ====
            $updatedRows = Examination_result::join('students', 'students.id', '=', 'examination_results.student_id')
                ->where('examination_results.school_id', $schools->id)
                ->where('examination_results.class_id', $class_id[0])
                ->where('examination_results.exam_type_id', $exam_id[0])
                ->where('students.status', 1)
                ->whereDate('examination_results.exam_date', $date)
                ->update(['examination_results.status' => 2]);

            if ($updatedRows > 0) {
                // ==== FETCH all results for ranking calculation ====
                $allStudentResults = Examination_result::query()
                    ->join('students', 'students.id', '=', 'examination_results.student_id')
                    ->join('subjects', 'subjects.id', '=', 'examination_results.course_id')
                    ->join('examinations', 'examinations.id', '=', 'examination_results.exam_type_id')
                    ->join('schools', 'schools.id', '=', 'examination_results.school_id')
                    ->where('examination_results.class_id', $class_id[0])
                    ->where('students.status', 1)
                    ->where('examination_results.school_id', $schools->id)
                    ->where('examination_results.exam_type_id', $exam_id[0])
                    ->whereDate('examination_results.exam_date', $date)
                    ->select(
                        'examination_results.*',
                        'students.id as student_id',
                        'students.first_name',
                        'students.middle_name',
                        'students.last_name',
                        'students.parent_id',
                        'students.status',
                        'examinations.exam_type',
                        'subjects.course_name',
                        'subjects.course_code',
                        'schools.school_name'
                    )
                    ->get();

                // Group results by student
                $studentsGrouped = $allStudentResults->groupBy('student_id');

                // ==== REKEDISHA: Calculate data based on marking style ====
                $studentsData = collect();
                foreach ($studentsGrouped as $studentId => $studentResults) {
                    $firstResult = $studentResults->first();
                    $totalMarks = $studentResults->sum('score');
                    $averageMarks = $studentResults->count() > 0 ? $totalMarks / $studentResults->count() : 0;

                    // ==== FOR MARKING STYLE 3: Calculate aggregate points and division ====
                    $aggregatePoints = null;
                    $division = null;
                    $grade = null;

                    if ($marking_style == 3) {
                        $gradePoints = [
                            'A' => 1,
                            'B' => 2,
                            'C' => 3,
                            'D' => 4,
                            'F' => 5,
                            'ABS' => 6
                        ];

                        $aggregatePoints = 0;
                        foreach ($studentResults as $result) {
                            $courseGrade = $this->calculateGrade($result->score, $marking_style);
                            $aggregatePoints += $gradePoints[$courseGrade] ?? 6;
                        }

                        $division = $this->calculateDivisionForStyle3($aggregatePoints, $studentResults->count());
                        $grade = $this->calculateGrade($averageMarks, $marking_style);
                    } else {
                        // For marking styles 1 & 2
                        $grade = $this->calculateGrade($averageMarks, $marking_style);
                    }

                    // Prepare courses string - REKEDISHA kuwa na grade kwa style 3
                    $courses = $studentResults->map(function ($result) use ($marking_style) {
                        $grade = $this->calculateGrade($result->score, $marking_style);

                        if ($marking_style == 3) {
                            // For style 3, show score and grade
                            return "{$result->course_code}={$result->score}{$grade}";
                        } else {
                            // For styles 1 & 2, show score only
                            return "{$result->course_code}={$result->score}";
                        }
                    })->implode("\n");

                    $studentsData->push([
                        'student_id' => $studentId,
                        'first_name' => $firstResult->first_name,
                        'middle_name' => $firstResult->middle_name,
                        'last_name' => $firstResult->last_name,
                        'parent_id' => $firstResult->parent_id,
                        'total_marks' => $totalMarks,
                        'average_marks' => $averageMarks,
                        'courses' => $courses,
                        'exam_type' => $firstResult->exam_type,
                        'aggregate_points' => $aggregatePoints,
                        'division' => $division,
                        'grade' => $grade,
                        'marking_style' => $marking_style,
                    ]);
                }

                // ==== REKEDISHA: Sort based on marking style ====
                if ($marking_style == 3) {
                    // For style 3, sort by aggregate points (lower is better)
                    $sortedStudents = $studentsData->sortBy('aggregate_points')->values();
                } else {
                    // For styles 1 & 2, sort by total marks (higher is better)
                    $sortedStudents = $studentsData->sortByDesc('total_marks')->values();
                }

                // ==== REKEDISHA: Calculate ranks with tie handling ====
                $rank = 1;
                $previousValue = null;
                $previousRank = null;
                $rankedStudents = collect();

                foreach ($sortedStudents as $index => $student) {
                    $currentValue = ($marking_style == 3) ? $student['aggregate_points'] : $student['total_marks'];

                    if ($previousValue !== null) {
                        if ($marking_style == 3) {
                            // For style 3, lower aggregate points is better
                            if ($currentValue > $previousValue) {
                                $rank = $index + 1;
                            }
                        } else {
                            // For styles 1 & 2, higher total marks is better
                            if ($currentValue < $previousValue) {
                                $rank = $index + 1;
                            }
                        }
                    }

                    if ($previousValue !== null && $currentValue == $previousValue) {
                        // Same score/points, same rank
                        $student['rank'] = $previousRank;
                    } else {
                        $student['rank'] = $rank;
                        $previousRank = $rank;
                    }

                    $rankedStudents->push($student);
                    $previousValue = $currentValue;
                }

                $totalStudents = $rankedStudents->count();
                $url = "https://shuleapp.tech";
                $dateFormat = Carbon::parse($date)->format('d/m/Y');
                $nextSmsService = new NextSmsService();
                $sender = $schools->sender_id ?? "SHULE APP";

                // Track SMS sending results
                $successCount = 0;
                $failCount = 0;
                $failedStudents = [];

                // Loop through ranked students and send SMS
                foreach ($rankedStudents as $student) {
                    try {
                        $parent = Parents::find($student['parent_id']);

                        if (!$parent) {
                            $failCount++;
                            $failedStudents[] = $student['first_name'] . ' ' . $student['last_name'];
                            continue;
                        }

                        $user = User::find($parent->user_id);

                        if (!$user || empty($user->phone)) {
                            $failCount++;
                            $failedStudents[] = $student['first_name'] . ' ' . $student['last_name'];
                            continue;
                        }

                        $phoneNumber = $this->formatPhoneNumber($user->phone);

                        if (!$phoneNumber) {
                            $failCount++;
                            $failedStudents[] = $student['first_name'] . ' ' . $student['last_name'];
                            continue;
                        }

                        // ==== REKEDISHA: Create message based on marking style ====
                        $fullname = $student['first_name'] . ' ' . $student['last_name'];

                        if ($marking_style == 3) {
                            // Message for Division Style (Style 3)
                            $message = "Habari, Matokeo ya " . strtoupper($fullname) . "\n";
                            $message .= "Mtihani wa: " . strtoupper($student['exam_type']) . "\n";
                            $message .= "wa Tarehe: {$dateFormat} ni:\n";
                            // Parse courses string
                            $coursesArray = explode("\n", $student['courses']);
                            foreach ($coursesArray as $course) {
                                $message .= strtoupper($course) . "\n";
                            }
                            $message .= "Jumla ya Pointi: {$student['aggregate_points']}\n";
                            $message .= "Divisheni: {$student['division']}\n";
                            $message .= "Nafasi: {$student['rank']} kati ya {$totalStudents}\n";
                            $message .= "Pakua ripoti hapa: {$url}\n";
                            $message .= "Asante kwa kuchagua " . strtoupper($schools->school_name);
                        } else {
                            // Message for Standard Grades (Styles 1 & 2)
                            $message = "Habari, Matokeo ya " . strtoupper($fullname) . "\n";
                            $message .= "Mtihani wa: " . strtoupper($student['exam_type']) . "\n";
                            $message .= "wa Tarehe: {$dateFormat} ni:\n";
                            // Parse courses string
                            $coursesArray = explode("\n", $student['courses']);
                            foreach ($coursesArray as $course) {
                                $message .= strtoupper($course) . "\n";
                            }
                            $message .= "Jumla: {$student['total_marks']}\n";
                            $message .= "Wastani: " . number_format($student['average_marks'], 1) . "\n";
                            $message .= "Daraja: {$student['grade']}\n";
                            $message .= "Nafasi: {$student['rank']} kati ya {$totalStudents}\n";
                            $message .= "Pakua ripoti hapa: {$url}\n";
                            $message .= "Asante kwa kuchagua " . strtoupper($schools->school_name);
                        }

                        $message = $this->cleanSmsText($message);

                        // Log::info("Prepared SMS for {$fullname} ({$phoneNumber}):\n{$message}");
                        // Send SMS
                        $response = $nextSmsService->sendSmsByNext(
                            $sender,
                            $phoneNumber,
                            $message,
                            $student['student_id'] . '_' . uniqid()
                        );

                        if ($response['success']) {
                            $successCount++;
                        } else {
                            $failCount++;
                            $failedStudents[] = $fullname;
                        }

                        // Add small delay to avoid rate limiting
                        usleep(50000); // 0.05 second delay

                    } catch (\Exception $e) {
                        $failCount++;
                        $failedStudents[] = $student['first_name'] . ' ' . $student['last_name'];
                        continue;
                    }
                }

                // Show appropriate message
                if ($successCount == 0 && $failCount > 0) {
                    Alert()->toast("Results published but failed to send SMS to all {$failCount} parents", 'error');
                } elseif ($failCount > 0) {
                    $failedNames = implode(', ', array_slice($failedStudents, 0, 5));
                    if (count($failedStudents) > 5) {
                        $failedNames .= ' and ' . (count($failedStudents) - 5) . ' more';
                    }
                    Alert()->toast("Results published! {$successCount} SMS sent, {$failCount} failed (e.g., {$failedNames})", 'warning');
                } else {
                    Alert()->toast("Results published and SMS sent to {$successCount} parents successfully!", 'success');
                }

                return back();
            } else {
                Alert()->toast('No results found to publish', 'warning');
                return back();
            }
        } catch (Exception $e) {
            Alert()->toast('Error: ' . $e->getMessage(), 'error');
            return back();
        }
    }

    public function unpublishResult($school, $year, $class, $examType, $month, $date)
    {
        try {

            $school_id = Hashids::decode($school);
            $exam_id = Hashids::decode($examType);
            $class_id = Hashids::decode($class);

            $user = Auth::user();

            $schools = school::find($school_id[0]);

            if ($user->school_id != $schools->id) {
                Alert()->toast('You are not authorized to view this page', 'error');
                return redirect()->route('error.page');
            }

            $updatedRows = Examination_result::where('school_id', $schools->id)
                // ->whereYear('exam_date', $year)
                ->where('class_id', $class_id[0])
                ->where('exam_type_id', $exam_id[0])
                // ->whereMonth('exam_date', $monthNumber)
                ->whereDate('exam_date', $date)
                ->update(['status' => 1]);
            if ($updatedRows) {
                Alert()->toast('Results unpublished successfully', 'success');
                return back();
            } else {
                Alert()->toast('No results found to unpublish.', 'error');
                return redirect()->back();
            }

            // return $monthsArray;
        } catch (\Exception $e) {
            Alert()->toast($e->getMessage(), 'error');
            return back();
        }
    }

    public function deleteResults($school, $year, $class, $examType, $month, $date)
    {
        try {
            $school_id = Hashids::decode($school);
            $class_id = Hashids::decode($class);
            $exam_id = Hashids::decode($examType);

            // dd($school_id, $class_id, $exam_id);
            // return $exam_id;

            $user = Auth::user();

            $schools = school::find($school_id[0]);

            if ($user->school_id != $schools->id) {
                Alert()->toast('You are not authorized to view this page', 'error');
                return redirect()->route('error.page');
            }

            $monthsArray = [
                'January' => 1,
                'February' => 2,
                'March' => 3,
                'April' => 4,
                'May' => 5,
                'June' => 6,
                'July' => 7,
                'August' => 8,
                'September' => 9,
                'October' => 10,
                'November' => 11,
                'December' => 12,
            ];

            $isPublished = Examination_result::where('school_id', $schools->id)
                ->where('class_id', $class_id[0])
                ->where('exam_type_id', $exam_id[0])
                ->whereDate('exam_date', $date)
                ->where('status', 2)
                ->exists();
            if ($isPublished) {
                Alert()->toast('Results data set is already published. Cannot delete.', 'error');
                return to_route('results.monthsByExamType', [$school, 'year' => $year, 'class' => $class, 'examType' => $examType]);
            }

            $existInCompile = generated_reports::where('class_id', $class_id[0])
                ->whereJsonContains('exam_dates', $date)
                ->where('school_id', $school_id[0])
                ->exists();

            if ($existInCompile) {
                Alert()->toast('Results already exist in the compiled reports. Cannot delete.', 'error');
                return to_route('results.monthsByExamType', [$school, 'year' => $year, 'class' => $class, 'examType' => $examType]);
            }
            // return $monthsArray;
            if (array_key_exists($month, $monthsArray)) {
                $monthNumber = $monthsArray[$month];
                // return $monthNumber;

                $results = Examination_result::where('school_id', $schools->id)
                    // ->whereYear('exam_date', $year)
                    ->where('class_id', $class_id[0])
                    ->where('exam_type_id', $exam_id[0])
                    // ->whereMonth('exam_date', $monthNumber)\
                    ->whereDate('exam_date', $date)
                    ->delete();
                if ($results) {
                    Alert()->toast('Results has deleted successfully', 'success');
                    return redirect()->back();
                }
            } else {
                Alert()->toast('Invalid month name provided.', 'error');
                return redirect()->back();
            }
        } catch (\Exception $e) {
            Alert()->toast($e->getMessage(), 'error');
            return back();
        }
    }

    //individual students reports show list
    public function individualStudentReports($school, $year, $class, $examType, $month, $date)
    {
        $school_id = Hashids::decode($school);
        $class_id = Hashids::decode($class);
        $exam_id = Hashids::decode($examType);

        $user = Auth::user();

        $schools = school::find($school_id[0]);

        if ($user->school_id != $schools->id) {
            Alert()->toast('You are not authorized to view this page', 'error');
            return redirect()->route('error.page');
        }

        // Mwezi kwa namba
        $monthsArray = [
            'January' => 1,
            'February' => 2,
            'March' => 3,
            'April' => 4,
            'May' => 5,
            'June' => 6,
            'July' => 7,
            'August' => 8,
            'September' => 9,
            'October' => 10,
            'November' => 11,
            'December' => 12,
        ];

        $classId = Grade::findOrFail($class_id[0]);

        $monthNumber = $monthsArray[$month];

        // Chagua wanafunzi wa kipekee kulingana na student_id
        $studentsResults = Examination_result::query()
            ->join('students', 'students.id', '=', 'examination_results.student_id')
            ->join('grades', 'grades.id', '=', 'examination_results.class_id')
            ->join('parents', 'parents.id', '=', 'students.parent_id')
            ->leftJoin('users', 'users.id', '=', 'parents.user_id')
            ->join('subjects', 'subjects.id', '=', 'examination_results.course_id')
            ->select(
                'students.id as student_id',
                'students.first_name',
                'students.middle_name',
                'students.last_name',
                'students.admission_number',
                'students.gender',
                'grades.class_name',
                'users.phone',
                'examination_results.*',
                'subjects.course_code',
            )
            // ->whereYear('examination_results.exam_date', $year)
            // ->whereMonth('examination_results.exam_date', $monthNumber)
            ->where('examination_results.class_id', $classId->id)
            ->whereDate('examination_results.exam_date', $date)
            ->where('examination_results.exam_type_id', $exam_id[0])
            ->distinct() // Hakikisha data ni ya kipekee kulingana na select fields
            ->orderBy('students.first_name')
            ->get();

        return view('Results.results_students_list', compact('schools', 'year', 'date', 'classId', 'exam_id', 'studentsResults', 'month'));
    }

    //delete single student results *****************************
    public function deleteStudentResult($school, $year, $class, $examType, $month, $student_id, $date)
    {
        $school_id = Hashids::decode($school);
        $class_id = Hashids::decode($class);
        $exam_id = Hashids::decode($examType);
        $student = Hashids::decode($student_id);

        // dd($school_id, $class_id, $exam_id, $student);
        $user = Auth::user();

        $schools = school::find($school_id[0]);

        if ($user->school_id != $schools->id) {
            Alert()->toast('You are not authorized to view this page', 'error');
            return redirect()->route('error.page');
        }

        $results = Examination_result::where('school_id', $schools->id)
            ->where('class_id', $class_id[0])
            ->where('exam_type_id', $exam_id[0])
            ->where('student_id', $student[0])
            ->whereDate('exam_date', $date)
            ->delete();
        // return $results;

        if ($results) {
            Alert()->toast('Results deleted successfully', 'success');
            return redirect()->back();
        } else {
            Alert()->toast('No results found to delete.', 'error');
            return redirect()->back();
        }
    }

    public function downloadIndividualReport($school, $year, $class, $examType, $month, $student, $date)
    {
        $school_id = Hashids::decode($school);
        $class_id = Hashids::decode($class);
        $exam_id = Hashids::decode($examType);
        $student_id = Hashids::decode($student);

        $user = Auth::user();
        $schools = school::find($school_id[0]);

        if ($user->school_id != $schools->id) {
            Alert()->toast('You are not authorized to view this page', 'error');
            return redirect()->route('error.page');
        }

        $studentId = Student::findOrFail($student_id[0]);

        $monthsArray = [
            'January' => 1,
            'February' => 2,
            'March' => 3,
            'April' => 4,
            'May' => 5,
            'June' => 6,
            'July' => 7,
            'August' => 8,
            'September' => 9,
            'October' => 10,
            'November' => 11,
            'December' => 12
        ];

        $monthValue = $monthsArray[$month];

        // ==== REKEDISHA: Add teacher_id kwenye select ====
        $results = Examination_result::query()
            ->join('students', 'students.id', '=', 'examination_results.student_id')
            ->join('subjects', 'subjects.id', '=', 'examination_results.course_id')
            ->join('grades', 'grades.id', '=', 'examination_results.class_id')
            ->join('examinations', 'examinations.id', '=', 'examination_results.exam_type_id')
            ->join('schools', 'schools.id', '=', 'examination_results.school_id')
            ->join('teachers', 'teachers.id', '=', 'examination_results.teacher_id')
            ->leftJoin('users', 'users.id', '=', 'teachers.user_id')
            ->select(
                'examination_results.*',
                'students.id as studentId',
                'students.first_name',
                'students.middle_name',
                'students.last_name',
                'students.admission_number',
                'students.group',
                'students.image',
                'students.gender',
                'students.admission_number',
                'subjects.course_name',
                'subjects.course_code',
                'grades.class_name',
                'grades.class_code',
                'examinations.exam_type',
                'users.first_name as teacher_first_name',
                'users.last_name as teacher_last_name',
                'schools.school_name',
                'schools.school_reg_no',
                'schools.postal_address',
                'schools.postal_name',
                'schools.logo',
                'schools.country',
                'teachers.id as teacher_id' // ==== REKEDISHA: Ongeza teacher_id ====
            )
            ->where('examination_results.student_id', $studentId->id)
            ->where('examination_results.exam_type_id', $exam_id[0])
            ->where('examination_results.class_id', $class_id[0])
            ->where('examination_results.school_id', $schools->id)
            ->where('examination_results.exam_date', $date)
            ->get();

        if ($results->isEmpty()) {
            Alert()->toast('No results found for this student', 'info');
            return redirect()->back();
        }

        // Get marking style
        $marking_style = $results->first()->marking_style ?? 1;

        // Calculate scores
        $totalScore = $results->sum('score');
        $averageScore = $results->count() > 0 ? $totalScore / $results->count() : 0;

        // For marking style 3, calculate aggregate and division
        if ($marking_style == 3) {
            // Calculate aggregate points for division
            $aggregatePoints = 0;
            $gradePoints = [
                'A' => 1,
                'B' => 2,
                'C' => 3,
                'D' => 4,
                'F' => 5,
                'ABS' => 6
            ];

            foreach ($results as $result) {
                $courseGrade = $this->calculateGrade($result->score, $marking_style);
                $aggregatePoints += $gradePoints[$courseGrade] ?? 6;
            }

            // Calculate division based on aggregate points
            $division = $this->calculateDivisionForStyle3($aggregatePoints, $results->count());
        } else {
            $aggregatePoints = null;
            $division = null;
        }

        // ==== REKEDISHA 1: Ranking query - tumia class_id[0] badala ya studentId->class_id ====
        $rankings = Examination_result::query()
            ->where('examination_results.class_id', $class_id[0]) // TUMIA CLASS_ID KUTOKA URL
            ->whereDate('exam_date', Carbon::parse($date))
            ->where('examination_results.exam_type_id', $exam_id[0])
            ->where('examination_results.school_id', $user->school_id)
            ->join('students', 'students.id', '=', 'examination_results.student_id')
            ->select('student_id', DB::raw('SUM(score) as total_score'))
            ->groupBy('student_id')
            ->orderByDesc('total_score')
            ->get();

        // Kutengeneza mfumo wa tie ranking
        $rank = 1;
        $previousScore = null;
        $ranks = [];

        foreach ($rankings as $key => $ranking) {
            if ($previousScore !== null && $ranking->total_score < $previousScore) {
                $rank = $key + 1;
            }
            $ranks[$ranking->student_id] = $rank;
            $previousScore = $ranking->total_score;
        }

        // Kupata rank ya mwanafunzi husika
        $studentRank = $ranks[$studentId->id] ?? null;

        // Add grades, remarks, and individual ranks to each result
        foreach ($results as $result) {
            // Calculate the grade and remarks based on marking_style
            $gradeResult = $this->calculateGrade($result->score, $marking_style);
            $result->grade = $gradeResult;

            // Determine remarks based on grade and marking style
            if ($marking_style == 1) {
                if ($result->score >= 41) {
                    $result->remarks = 'Excellent';
                } elseif ($result->score >= 31) {
                    $result->remarks = 'Good';
                } elseif ($result->score >= 21) {
                    $result->remarks = 'Pass';
                } elseif ($result->score >= 11) {
                    $result->remarks = 'Poor';
                } else {
                    $result->remarks = 'Fail';
                }
            } elseif ($marking_style == 2) {
                if ($result->score >= 81) {
                    $result->remarks = 'Excellent';
                } elseif ($result->score >= 61) {
                    $result->remarks = 'Good';
                } elseif ($result->score >= 41) {
                    $result->remarks = 'Pass';
                } elseif ($result->score >= 21) {
                    $result->remarks = 'Poor';
                } else {
                    $result->remarks = 'Fail';
                }
            } else {
                // For marking style 3
                if ($gradeResult == 'A') {
                    $result->remarks = 'Excellent';
                } elseif ($gradeResult == 'B') {
                    $result->remarks = 'Good';
                } elseif ($gradeResult == 'C') {
                    $result->remarks = 'Pass';
                } elseif ($gradeResult == 'D') {
                    $result->remarks = 'Unsatisfactory';
                } elseif ($gradeResult == 'F') {
                    $result->remarks = 'Fail';
                } else {
                    $result->remarks = 'Absent';
                }
            }

            // ==== REKEDISHA 2: Course ranking - tumia class_id[0] ====
            $courseRankings = Examination_result::query()
                ->where('course_id', $result->course_id)
                ->where('examination_results.class_id', $class_id[0]) // TUMIA CLASS_ID KUTOKA URL
                ->whereDate('exam_date', Carbon::parse($date))
                ->where('examination_results.exam_type_id', $exam_id[0])
                ->where('examination_results.school_id', $user->school_id)
                ->join('students', 'students.id', '=', 'examination_results.student_id')
                ->select('student_id', DB::raw('SUM(score) as total_score'))
                ->groupBy('student_id')
                ->orderByDesc('total_score')
                ->get();

            // Hakikisha wanafunzi wenye score sawa wanashirikiana rank
            $rank = 1;
            $previousScore = null;
            $courseRanks = []; // ==== REKEDISHA: Badilisha jina la variable ====

            foreach ($courseRankings as $key => $ranking) {
                if ($previousScore !== null && $ranking->total_score < $previousScore) {
                    $rank = $key + 1;
                }
                $courseRanks[$ranking->student_id] = $rank;
                $previousScore = $ranking->total_score;
            }

            // Kupata rank ya mwanafunzi husika
            $result->courseRank = $courseRanks[$studentId->id] ?? null;

            // ==== REKEDISHA 3: Ongeza mwalimu aliyepakia matokeo ====
            // Tafuta maelezo kamili ya mwalimu aliyepakia
            if ($result->teacher_id) {
                $uploadingTeacher = DB::table('teachers')
                    ->join('users', 'users.id', '=', 'teachers.user_id')
                    ->where('teachers.id', $result->teacher_id)
                    ->select('users.first_name', 'users.last_name')
                    ->first();

                if ($uploadingTeacher) {
                    $result->uploading_teacher_name = $uploadingTeacher->first_name . ' ' . $uploadingTeacher->last_name;
                    $result->uploading_teacher_initials = $uploadingTeacher->first_name . '. ' . substr($uploadingTeacher->last_name, 0, 1);
                }
            }
        }

        // Generate QR payload with summary
        $verificationData = [
            'student_name' => trim($studentId->first_name . ' ' . $studentId->middle_name . ' ' . $studentId->last_name),
            'admission_number' => $studentId->admission_number,
            'class' => $results->isNotEmpty() ? $results->first()->class_name : '-',
            'report_type' => $results->isNotEmpty() ? $results->first()->exam_type . ' Assessment' : 'Assessment',
            'term' => $results->isNotEmpty() ? $results->first()->Exam_term : '-',
            'school' => $results->isNotEmpty() ? $results->first()->school_name : $schools->school_name,
            'report_date' => Carbon::parse($date)->format('Y-m-d'),
            'report_id' => sha1($studentId->id . $exam_id[0] . $class_id[0] . $date),
            'issued_at' => now()->timestamp,

            // 🔹 Add these summary fields
            'total_score' => $totalScore,
            'average_score' => round($averageScore, 2),
            'student_rank' => $studentRank,
            'total_students' => $rankings->count(),
            'marking_style' => $marking_style,
        ];

        // Add division info for marking style 3
        if ($marking_style == 3) {
            $verificationData['division'] = $division;
            $verificationData['aggregate_points'] = $aggregatePoints;
        }

        // Sign and encrypt
        $verificationData['signature'] = hash_hmac('sha256', json_encode($verificationData), config('app.key'));
        $encryptedPayload = Crypt::encryptString(json_encode($verificationData));

        $verificationUrl = route('report.verify', ['payload' => $encryptedPayload]);

        $resultQr = Builder::create()
            ->writer(new PngWriter())
            ->data($verificationUrl)
            ->size(140)
            ->margin(4)
            ->build();

        $qrPng = base64_encode($resultQr->getString());

        // Pass the calculated data to the view
        $pdf = \PDF::loadView('Results.parent_results', compact(
            'results',
            'year',
            'date',
            'examType',
            'studentId',
            'student',
            'month',
            'totalScore',
            'averageScore',
            'studentRank',
            'rankings',
            'qrPng',
            'marking_style',
            'aggregatePoints',
            'division'
        ));

        return $pdf->stream($results->isNotEmpty() ? $results->first()->first_name . ' Results ' . $month . ' ' . $year . '.pdf' : 'Report_' . $year . '.pdf');
    }


    //Re-send sms results individually
    public function sendResultSms($school, $year, $class, $examType, $month, $student_id, $date)
    {
        try {
            $school_id = Hashids::decode($school);
            $class_id = Hashids::decode($class);
            $exam_id = Hashids::decode($examType);
            $student = Hashids::decode($student_id);

            $schools = school::find($school_id[0]);
            $user = Auth::user();

            if ($user->school_id != $schools->id) {
                Alert()->toast('You are not authorized to view this page', 'error');
                return redirect()->route('error.page');
            }

            // Fetch student information
            $studentInfo = Student::findOrFail($student[0]);

            // Map month names to their numeric values
            $monthsArray = [
                'January' => 1,
                'February' => 2,
                'March' => 3,
                'April' => 4,
                'May' => 5,
                'June' => 6,
                'July' => 7,
                'August' => 8,
                'September' => 9,
                'October' => 10,
                'November' => 11,
                'December' => 12
            ];
            $monthValue = $monthsArray[$month];

            // ==== REKEDISHA: Get marking style first ====
            $marking_style = Examination_result::query()
                ->where('school_id', $schools->id)
                ->where('class_id', $class_id[0])
                ->where('exam_type_id', $exam_id[0])
                ->whereDate('exam_date', $date)
                ->value('marking_style') ?? 1;

            $results = Examination_result::query()
                ->join('students', 'students.id', '=', 'examination_results.student_id')
                ->join('subjects', 'subjects.id', '=', 'examination_results.course_id')
                ->join('grades', 'grades.id', '=', 'examination_results.class_id')
                ->join('examinations', 'examinations.id', '=', 'examination_results.exam_type_id')
                ->join('schools', 'schools.id', '=', 'examination_results.school_id')
                ->select(
                    'examination_results.*',
                    'students.first_name',
                    'students.middle_name',
                    'students.last_name',
                    'students.status',
                    'subjects.course_name',
                    'subjects.course_code',
                    'examinations.exam_type',
                    'schools.school_name'
                )
                ->where('examination_results.student_id', $studentInfo->id)
                ->where('examination_results.exam_type_id', $exam_id[0])
                ->where('examination_results.class_id', $class_id[0])
                ->where('students.status', 1)
                ->where('examination_results.school_id', $schools->id)
                ->whereDate('examination_results.exam_date', $date)
                ->where('examination_results.status', 2)
                ->get();

            // Hakikisha kuwa kuna data kwenye $results
            if ($results->isEmpty()) {
                Alert()->toast('This result data set is locked 🔐', 'error');
                return redirect()->back();
            }

            // Calculate total score and average score
            $totalScore = $results->sum('score');
            $averageScore = $results->count() > 0 ? $totalScore / $results->count() : 0;

            // ==== REKEDISHA: Calculate aggregate points for marking style 3 ====
            $aggregatePoints = null;
            $division = null;
            $grade = null;

            if ($marking_style == 3) {
                $gradePoints = [
                    'A' => 1,
                    'B' => 2,
                    'C' => 3,
                    'D' => 4,
                    'F' => 5,
                    'ABS' => 6
                ];

                $aggregatePoints = 0;
                foreach ($results as $result) {
                    $courseGrade = $this->calculateGrade($result->score, $marking_style);
                    $aggregatePoints += $gradePoints[$courseGrade] ?? 6;
                }

                $division = $this->calculateDivisionForStyle3($aggregatePoints, $results->count());
                $grade = $this->calculateGrade($averageScore, $marking_style);
            } else {
                $grade = $this->calculateGrade($averageScore, $marking_style);
            }

            // ==== REKEDISHA: Determine the student's overall rank based on marking style ====
            if ($marking_style == 3) {
                // For marking style 3, rank by aggregate points (lower is better)
                $rankings = Examination_result::query()
                    ->where('examination_results.class_id', $class_id[0])
                    ->whereDate('exam_date', Carbon::parse($date))
                    ->where('examination_results.exam_type_id', $exam_id[0])
                    ->where('examination_results.school_id', $schools->id)
                    ->join('students', 'students.id', '=', 'examination_results.student_id')
                    ->select('student_id', DB::raw('SUM(score) as total_score'))
                    ->groupBy('student_id')
                    ->get();

                // Calculate aggregate points for each student
                $rankingsWithPoints = [];
                foreach ($rankings as $ranking) {
                    $studentResults = Examination_result::query()
                        ->where('student_id', $ranking->student_id)
                        ->where('exam_type_id', $exam_id[0])
                        ->where('class_id', $class_id[0])
                        ->whereDate('exam_date', $date)
                        ->get();

                    $points = 0;
                    foreach ($studentResults as $result) {
                        $courseGrade = $this->calculateGrade($result->score, $marking_style);
                        $points += $gradePoints[$courseGrade] ?? 6;
                    }

                    $rankingsWithPoints[] = [
                        'student_id' => $ranking->student_id,
                        'aggregate_points' => $points
                    ];
                }

                // Sort by aggregate points (ascending)
                $sortedRankings = collect($rankingsWithPoints)->sortBy('aggregate_points')->values();

                // Calculate ranks with tie handling
                $rank = 1;
                $previousPoints = null;
                $ranks = [];

                foreach ($sortedRankings as $key => $item) {
                    if ($previousPoints !== null && $item['aggregate_points'] > $previousPoints) {
                        $rank = $key + 1;
                    }
                    $ranks[$item['student_id']] = $rank;
                    $previousPoints = $item['aggregate_points'];
                }
            } else {
                // For marking styles 1 & 2, rank by total score (higher is better)
                $rankings = Examination_result::query()
                    ->where('examination_results.class_id', $class_id[0])
                    ->whereDate('exam_date', Carbon::parse($date))
                    ->where('examination_results.exam_type_id', $exam_id[0])
                    ->where('examination_results.school_id', $schools->id)
                    ->join('students', 'students.id', '=', 'examination_results.student_id')
                    ->select('student_id', DB::raw('SUM(score) as total_score'))
                    ->groupBy('student_id')
                    ->orderByDesc('total_score')
                    ->get();

                // Calculate ranks with tie handling
                $rank = 1;
                $previousScore = null;
                $ranks = [];

                foreach ($rankings as $key => $ranking) {
                    if ($previousScore !== null && $ranking->total_score < $previousScore) {
                        $rank = $key + 1;
                    }
                    $ranks[$ranking->student_id] = $rank;
                    $previousScore = $ranking->total_score;
                }
            }

            // Get student rank
            $studentRank = $ranks[$studentInfo->id] ?? null;

            // Check if student participated
            if ($studentRank === null) {
                $studentInRankings = isset($rankings) && (($marking_style == 3 && collect($sortedRankings)->contains('student_id', $studentInfo->id)) ||
                    ($marking_style != 3 && $rankings->contains('student_id', $studentInfo->id)));

                if (!$studentInRankings) {
                    $studentRank = 'N/A';
                    $totalStudents = 0;
                } else {
                    $studentRank = 'N/A';
                    $totalStudents = ($marking_style == 3) ? count($sortedRankings) : $rankings->count();
                }
            } else {
                $totalStudents = ($marking_style == 3) ? count($sortedRankings) : $rankings->count();
            }

            // Prepare basic info
            $fullName = $studentInfo->first_name . ' ' . $studentInfo->last_name;
            $examination = $results->first()->exam_type;
            $schoolName = strtoupper($results->first()->school_name);
            $url = 'https://shuleapp.tech';
            $dateFormat = Carbon::parse($date)->format('d/m/Y');

            // Find parent phone
            $parent = Parents::where('id', $studentInfo->parent_id)->first();

            if (!$parent) {
                Alert()->toast('Parent information not found for this student', 'error');
                return redirect()->back();
            }

            $users = User::where('id', $parent->user_id)->first();

            if (!$users || empty($users->phone)) {
                Alert()->toast('Parent phone number not found', 'error');
                return redirect()->back();
            }

            // ==== REKEDISHA: Create message based on marking style ====
            if ($marking_style == 3) {
                // Message for Division Style (Style 3)
                $messageContent = "Habari, Matokeo ya " . strtoupper($fullName) . "\n";
                $messageContent .= "Mtihani wa: " . strtoupper($examination) . "\n";
                $messageContent .= "wa Tarehe: {$dateFormat} ni:\n";
                foreach ($results as $result) {
                    $grade = $this->calculateGrade($result->score, $marking_style);
                    $messageContent .= strtoupper($result->course_code) . ": {$result->score}{$grade}\n";
                }
                $messageContent .= "Jumla ya Pointi: {$aggregatePoints}\n";
                $messageContent .= "Divisheni: {$division}\n";
                $messageContent .= "Nafasi: {$studentRank}";

                if ($totalStudents > 0) {
                    $messageContent .= " kati ya {$totalStudents}\n";
                } else {
                    $messageContent .= "\n";
                }
                $messageContent .= "Pakua ripoti hapa: {$url}\n";
                $messageContent .= "Asante kwa kuchagua . {$schoolName}";
            } else {
                $messageContent = "Habari, Matokeo ya " . strtoupper($fullName) . "\n";
                $messageContent .= "Mtihani wa: " . strtoupper($examination) . "\n";
                $messageContent .= "wa Tarehe: {$dateFormat} ni:\n";

                foreach ($results as $result) {
                    $messageContent .= strtoupper($result->course_code) . ": {$result->score}\n";
                }

                $messageContent .= "Jumla: {$totalScore}\n";
                $messageContent .= "Wastani: " . number_format($averageScore, 1) . "\n";
                $messageContent .= "Daraja: {$grade}\n";
                $messageContent .= "Nafasi: {$studentRank}";

                if ($totalStudents > 0) {
                    $messageContent .= " kati ya {$totalStudents}\n";
                } else {
                    $messageContent .= "\n";
                }
                $messageContent .= "Pakua ripoti hapa: {$url}\n";
                $messageContent .= "Asante kwa kuchagua " . $schoolName;
            }

            $messageContent = $this->cleanSmsText($messageContent);

            // Send SMS via NextSMS API
            $nextSmsService = new NextSmsService();
            $sender = $schools->sender_id ?? "SHULE APP";
            $destination = $this->formatPhoneNumber($users->phone);
            $reference = uniqid();

            $response = $nextSmsService->sendSmsByNext($sender, $destination, $messageContent, $reference);

            if (!$response['success']) {
                Alert()->toast('SMS failed: ' . $response['error'], 'error');
                return back();
            }

            // Log::info("SMS sent to {$destination}: {$messageContent}");

            Alert()->toast('Results SMS has been sent successfully', 'success');
            return redirect()->back();
        } catch (Exception $e) {
            Alert()->toast($e->getMessage(), 'error');
            return redirect()->back();
        }
    }
    private function formatPhoneNumber($phone)
    {
        // Remove any non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Ensure the number starts with the country code (e.g., 255 for Tanzania)
        if (strlen($phone) == 9) {
            $phone = '255' . $phone;
        } elseif (strlen($phone) == 10 && substr($phone, 0, 1) == '0') {
            $phone = '255' . substr($phone, 1);
        }

        return $phone;
    }

    public function updateScore(Request $request)
    {
        // Thibitisha data
        $request->validate([
            'student_id' => 'required|integer',
            'subject_id' => 'required|integer',
            'score' => 'nullable|numeric',
        ]);

        // Tafuta rekodi ya alama kwenye database
        $result = Examination_result::where('student_id', $request->student_id)
            ->where('id', $request->subject_id)
            ->first();

        if ($result) {
            // Sahihisha alama
            $result->score = $request->score;
            $result->save();

            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false]);
    }
    //send compiled results to the table compiled_results table
    public function saveCompiledResults(Request $request, $school, $year, $class)
    {
        $request->validate([
            'exam_type' => 'required|string|max:255',
            'class_id' => 'required|integer',
            'exam_dates' => 'required|array|min:2', // Angalau mitihani miwili
            'combine_option' => 'required|in:sum,average,individual',
            'term' => 'required|string|in:i,ii',
        ]);

        $selectedDataSet = $request->input('exam_dates', []);
        $examType = $request->input('exam_type');
        $classId = $request->input('class_id');
        $customExamType = $request->input('custom_exam_type');
        $combineMode = $request->input('combine_option');
        $reportTerm = $request->input('term');
        $school_id = Auth::user()->school_id;

        if ($examType === 'custom' && !empty($customExamType)) {
            $examType = $customExamType;
        }

        // ========== MARKING STYLE VALIDATION ==========
        // Collect all marking styles from selected exam dates
        $markingStyles = [];
        $conflictingExams = [];

        foreach ($selectedDataSet as $date) {
            // Get exam_type_id from examination_results using date
            $examInfo = Examination_result::where('school_id', $school_id)
                ->where('class_id', $classId)
                ->whereDate('exam_date', Carbon::parse($date))
                ->select('exam_type_id', 'marking_style')
                ->first();

            if (!$examInfo) {
                Alert()->toast('No results found for date: ' . Carbon::parse($date)->format('d/m/Y'), 'error');
                return back()->withInput();
            }

            $markingStyle = $examInfo->marking_style;

            // Get exam type name for better error message
            $examTypeName = Examination::find($examInfo->exam_type_id)->exam_type ?? 'Unknown Exam';
            $formattedDate = Carbon::parse($date)->format('d/m/Y');

            // Store exam info for conflict checking
            $examKey = $formattedDate . ' - ' . $examTypeName;

            if (!isset($markingStyles[$markingStyle])) {
                $markingStyles[$markingStyle] = [];
            }

            $markingStyles[$markingStyle][] = [
                'date' => $formattedDate,
                'exam_type' => $examTypeName,
                'key' => $examKey
            ];
        }

        // Check if there are multiple marking styles
        if (count($markingStyles) > 1) {
            $errorMessage = 'Cannot compile results: Selected exams have different Grading system.';

            // Build detailed error message
            $styleDescriptions = [
                1 => 'Style 1 (Point system)',
                2 => 'Style 2 (Percentage system)',
                3 => 'Style 3 (Division System)'
            ];

            $errorMessage .= "\n\nDetails:\n";

            foreach ($markingStyles as $style => $exams) {
                $styleName = $styleDescriptions[$style] ?? "Style $style";
                $errorMessage .= "\n$styleName:\n";

                foreach ($exams as $exam) {
                    $errorMessage .= "  - {$exam['date']}\n";
                }
            }

            // Identify which specific exam has different marking style
            if (count($markingStyles) == 2) {
                // Find the minority style
                $minorityStyle = null;
                $minorityCount = PHP_INT_MAX;

                foreach ($markingStyles as $style => $exams) {
                    if (count($exams) < $minorityCount) {
                        $minorityCount = count($exams);
                        $minorityStyle = $style;
                    }
                }

                if ($minorityStyle) {
                    $styleName = $styleDescriptions[$minorityStyle] ?? "Style $minorityStyle";
                    $errorMessage .= "\n\n⚠️ The exam(s) with $styleName cannot be combined with others.";

                    // List the conflicting exams
                    foreach ($markingStyles[$minorityStyle] as $exam) {
                        $errorMessage .= "\n   → {$exam['date']}";
                    }
                }
            }

            Alert()->toast($errorMessage, 'error')->persistent(true);
            return back()->withInput();
        }

        // Get the marking style being used (all should be same)
        $primaryMarkingStyle = array_key_first($markingStyles);
        $styleDescription = [
            1 => 'Style 1 (Point system)',
            2 => 'Style 2 (Percentage system)',
            3 => 'Style 3 (Division System)'
        ][$primaryMarkingStyle] ?? "Style $primaryMarkingStyle";

        // Check for duplicates
        $alreadyExists = generated_reports::where('class_id', $classId)
            ->where('school_id', $school_id)
            ->where('title', $examType)
            ->exists();

        if ($alreadyExists) {
            Alert()->toast('This results data set already exists', 'error');
            return to_route('results.examTypesByClass', ['school' => $school, 'year' => $year, 'class' => $class]);
        }

        // Create report with marking style info
        $report = generated_reports::create([
            'title' => $examType,
            'class_id' => $classId,
            'school_id' => $school_id,
            'exam_dates' => $selectedDataSet,
            'combine_option' => $combineMode,
            'created_by' => auth()->id(),
            'term' => $reportTerm,
            'marking_style' => $primaryMarkingStyle, // Store the marking style used
        ]);

        Alert()->toast("Report generated successfully using $styleDescription.", 'success');
        return to_route('results.examTypesByClass', ['school' => $school, 'year' => $year, 'class' => $class]);
    }

    // function for displaying compiled results by month ***************************************
    public function studentGeneratedCombinedReport($class, $year, $school, $report)
    {
        $reportId = Hashids::decode($report);
        $classId = Hashids::decode($class);
        $schoolId = Hashids::decode($school);

        $reports = generated_reports::findOrFail($reportId[0]);
        // return $reports;

        //fetch class details

        $classes = Grade::findOrFail($classId[0]);
        //fetch students lists found in the report
        $studentsReport = Examination_result::query()
            ->join('students', 'students.id', '=', 'examination_results.student_id')
            ->join('grades', 'grades.id', '=', 'examination_results.class_id')
            ->join('parents', 'parents.id', '=', 'students.parent_id')
            ->leftJoin('users', 'users.id', '=', 'parents.user_id')
            ->select(
                'examination_results.*',
                'students.first_name',
                'students.middle_name',
                'students.last_name',
                'students.admission_number',
                'students.gender',
                'students.id as studentId',
                'students.class_id as student_class_id',
                'grades.class_name',
                'grades.class_code',
                'students.school_id as student_school_id',
                'users.first_name as user_first_name',
                'users.last_name as user_last_name',
                'users.phone',
            )
            ->where('examination_results.class_id', $reports->class_id)
            ->where('examination_results.school_id', $reports->school_id)
            ->whereIn(DB::raw('DATE(exam_date)'), $reports->exam_dates)
            ->orderBy('students.first_name')
            ->get()
            ->unique('student_id');

        $myReportData = $studentsReport;

        $allScores = Examination_result::query()
            ->join('subjects', 'subjects.id', '=', 'examination_results.course_id')
            ->where('examination_results.class_id', $reports->class_id)
            ->where('examination_results.school_id', $reports->school_id)
            ->whereIn(DB::raw('DATE(exam_date)'), $reports->exam_dates)
            ->get()
            ->groupBy([
                'student_id',
                'course_id',
                function ($row) {
                    return date('Y-m-d', strtotime($row->exam_date));
                }
            ]);
        // return $studentsReport;

        return view('Results.combined_result_month', compact('reports', 'classes', 'class', 'reports', 'allScores', 'myReportData', 'year', 'school',));
    }

    //function for showing individual student report which is already compiled
    public function showStudentCompiledReport($school, $year, $class, $report, $student)
    {
        $studentId = Hashids::decode($student)[0];
        $schoolId = Hashids::decode($school)[0];
        $classId = Hashids::decode($class)[0];
        $reportId = Hashids::decode($report)[0];

        $reports = generated_reports::find($reportId);
        $examDates = $reports->exam_dates;

        // ==== REKEDISHA 1: Pata class_id ya kwenye generated_reports (iliyohifadhiwa) ====
        $storedClassId = $reports->class_id ?? $classId;

        $results = Examination_result::query()
            ->join('students', 'students.id', '=', 'examination_results.student_id')
            ->join('grades', 'grades.id', '=', 'examination_results.class_id')
            ->join('subjects', 'subjects.id', '=', 'examination_results.course_id')
            ->join('examinations', 'examinations.id', '=', 'examination_results.exam_type_id')
            ->join('schools', 'schools.id', '=', 'examination_results.school_id')
            ->join('teachers', 'teachers.id', '=', 'examination_results.teacher_id')
            ->leftJoin('users', 'users.id', '=', 'teachers.user_id')
            ->select(
                'students.id as studentId',
                'students.first_name',
                'students.middle_name',
                'students.last_name',
                'students.group',
                'students.gender',
                'students.image',
                'subjects.id as subjectId',
                'subjects.course_name',
                'subjects.course_code',
                'students.admission_number',
                'grades.class_name',
                'grades.class_code',
                'examination_results.*',
                'examinations.exam_type',
                'examinations.symbolic_abbr',
                'schools.school_name',
                'schools.school_reg_no',
                'schools.postal_address',
                'schools.postal_name',
                'schools.logo',
                'schools.country',
                'users.first_name as teacher_first_name',
                'users.last_name as teacher_last_name',
                'teachers.id as teacher_id' // ==== REKEDISHA: Pata teacher_id ya aliyepakia ====
            )
            ->where('examination_results.student_id', $studentId)
            ->where('examination_results.class_id', $storedClassId) // TUMIA CLASS_ID ILIYOHIFADHIWA
            ->where('examination_results.school_id', $schoolId)
            ->whereIn(DB::raw('DATE(exam_date)'), $examDates)
            ->get();

        $classResultsGrouped = $results->groupBy('subjectId');

        $examHeaders = $results->map(function ($item) {
            return [
                'abbr' => $item->symbolic_abbr,
                'date' => $item->exam_date,
                'display' => $item->symbolic_abbr . ' ' . \Carbon\Carbon::parse($item->exam_date)->format('d M Y')
            ];
        })->unique(fn($item) => $item['abbr'] . $item['date'])->values();

        $combineOption = $reports->combine_option ?? 'individual';
        $finalData = [];

        // Vigezo vya kuweka jumla za mwanafunzi
        $studentTotalMarks = 0;
        $subjectCount = 0;

        foreach ($classResultsGrouped as $subjectId => $subjectResults) {
            $subjectName = $subjectResults->first()->course_name;
            $subjectCode = $subjectResults->first()->course_code;

            // ==== REKEDISHA 2: Tafuta mwalimu aliyepakia matokeo ====
            // Chukua mwalimu wa kwanza aliyepakia matokeo kwenye somo hili
            $uploadingTeacher = $subjectResults->first();
            $teacher = $uploadingTeacher && $uploadingTeacher->teacher_first_name && $uploadingTeacher->teacher_last_name
                ? $uploadingTeacher->teacher_first_name . '. ' . substr($uploadingTeacher->teacher_last_name, 0, 1)
                : 'N/A';

            // AU: Tafuta mwalimu kutoka kwa teacher_id iliyohifadhiwa kwenye examination_results
            $examTeacher = null;
            foreach ($subjectResults as $result) {
                if ($result->teacher_id) {
                    $examTeacher = DB::table('teachers')
                        ->join('users', 'users.id', '=', 'teachers.user_id')
                        ->where('teachers.id', $result->teacher_id)
                        ->select('users.first_name', 'users.last_name')
                        ->first();
                    break;
                }
            }

            if ($examTeacher) {
                $teacher = $examTeacher->first_name . '. ' . substr($examTeacher->last_name, 0, 1);
            }

            $examScores = [];
            $total = 0;
            $average = 0;

            // TUMIA SAME LOGIC KAMA downloadCombinedReport
            if ($combineOption == 'individual') {
                foreach ($examHeaders as $exam) {
                    $score = $subjectResults->where('symbolic_abbr', $exam['abbr'])
                        ->where('exam_date', $exam['date'])
                        ->first()->score ?? null;
                    $examScores[$exam['abbr'] . '_' . $exam['date']] = $score;
                }

                $total = collect($examScores)->filter()->sum();
                $average = collect($examScores)->filter()->avg() ?? 0;
            } elseif ($combineOption == 'sum') {
                foreach ($examHeaders as $exam) {
                    $score = $subjectResults->where('symbolic_abbr', $exam['abbr'])
                        ->where('exam_date', $exam['date'])
                        ->first()->score ?? null;
                    $examScores[$exam['abbr'] . '_' . $exam['date']] = $score;
                }
                $total = collect($examScores)->sum();
                $average = count(array_filter($examScores)) > 0 ? $total / count(array_filter($examScores)) : 0;
            } elseif ($combineOption == 'average') {
                foreach ($examHeaders as $exam) {
                    $score = $subjectResults->where('symbolic_abbr', $exam['abbr'])
                        ->where('exam_date', $exam['date'])
                        ->first()->score ?? null;
                    $examScores[$exam['abbr'] . '_' . $exam['date']] = $score;
                }
                $filtered = collect($examScores)->filter();
                $total = 0;
                $average = $filtered->count() > 0 ? $filtered->avg() : 0;
            }

            // Ongeza kwa jumla za mwanafunzi
            $studentTotalMarks += $average;
            $subjectCount++;

            // Weka placeholder kwa position
            $finalData[] = [
                'subjectName' => $subjectName,
                'teacher' => $teacher,
                'subjectCode' => $subjectCode,
                'examScores' => $examScores,
                'total' => $total,
                'average' => round($average, 2),
                'position' => 0 // Placeholder, itabakiweka baada ya rank calculation
            ];
        }

        // **Sasa hesabu position ya kila somo - SAME LOGIC**
        foreach ($finalData as &$subject) {
            $allStudentSubjectAverages = [];

            // Pata wanafunzi wote na averages zao kwenye somo hili
            $subjectId = DB::table('subjects')
                ->where('course_name', $subject['subjectName'])
                ->where('course_code', $subject['subjectCode'])
                ->value('id');

            if ($subjectId) {
                // Get all students' averages for this subject
                $subjectResultsAll = DB::table('examination_results')
                    ->join('subjects', 'subjects.id', '=', 'examination_results.course_id')
                    ->where('examination_results.course_id', $subjectId)
                    ->where('examination_results.class_id', $storedClassId) // TUMIA CLASS_ID ILIYOHIFADHIWA
                    ->where('examination_results.school_id', $schoolId)
                    ->whereIn(DB::raw('DATE(exam_date)'), $examDates)
                    ->select('examination_results.*', 'subjects.course_name')
                    ->get()
                    ->groupBy('student_id');

                foreach ($subjectResultsAll as $student_id => $studentResults) {
                    // Calculate average for each student using the same combine option
                    if ($combineOption == 'individual') {
                        $avg = $studentResults->avg('score') ?? 0;
                    } elseif ($combineOption == 'sum') {
                        $avg = $studentResults->count() > 0 ? $studentResults->sum('score') / $studentResults->count() : 0;
                    } elseif ($combineOption == 'average') {
                        $avg = $studentResults->avg('score') ?? 0;
                    }

                    $allStudentSubjectAverages[$student_id] = round($avg, 2);
                }

                // Sort in descending order and assign positions with tie handling
                arsort($allStudentSubjectAverages);
                $positions = [];
                $position = 1;
                $previousAverage = null;
                $sameRankCount = 0;

                foreach ($allStudentSubjectAverages as $std_id => $avg) {
                    if ($previousAverage !== null && $avg < $previousAverage) {
                        $position += $sameRankCount;
                        $sameRankCount = 1;
                    } else {
                        $sameRankCount++;
                    }

                    $positions[$std_id] = $position;
                    $previousAverage = $avg;
                }

                // Set the position for this student
                $subject['position'] = $positions[$studentId] ?? '-';
            }
        }
        unset($subject);

        $student = $results->first();
        $schoolInfo = $results->first();

        // **Calculate Student General Average** (jumla ya average za masomo / idadi ya masomo)
        $studentGeneralAverage = $subjectCount > 0 ? round($studentTotalMarks / $subjectCount, 2) : 0;

        // **Total Marks** (jumla ya average za masomo yote)
        $totalScoreForStudent = round($studentTotalMarks, 2);

        $examHeadersWithDates = $results
            ->mapWithKeys(fn($item) => [$item->symbolic_abbr => $item->exam_date])
            ->unique()
            ->toBase();

        $examAverages = [];
        foreach ($examHeaders as $exam) {
            $totalPerExam = 0;
            $countPerExam = 0;
            $abbr = $exam['abbr'];
            $date = $exam['date'];

            foreach ($finalData as $subject) {
                $score = $subject['examScores'][$abbr . '_' . $date] ?? null;
                if (is_numeric($score)) {
                    $totalPerExam += $score;
                    $countPerExam++;
                }
            }

            $examAverages[$abbr . '_' . $date] = $countPerExam > 0 ? round($totalPerExam / $countPerExam, 2) : 0;
        }

        // =================== GENERAL POSITION (USING TOTAL MARKS) ===================
        // Pata average za wanafunzi wote kwa kutumia logic ile ile
        $allStudentAverages = [];

        // ==== REKEDISHA 3: Pata wanafunzi wote walio kwenye class iliyohifadhiwa ====
        // Pata wanafunzi wote waliopo kwenye class (kutoka kwa results za wakati huo)
        $allStudents = DB::table('examination_results')
            ->where('class_id', $storedClassId) // TUMIA CLASS_ID ILIYOHIFADHIWA
            ->where('school_id', $schoolId)
            ->whereIn(DB::raw('DATE(exam_date)'), $examDates)
            ->distinct()
            ->pluck('student_id');

        foreach ($allStudents as $stdId) {
            // Pata results za mwanafunzi huyu
            $studentResults = DB::table('examination_results')
                ->where('student_id', $stdId)
                ->where('class_id', $storedClassId) // TUMIA CLASS_ID ILIYOHIFADHIWA
                ->where('school_id', $schoolId)
                ->whereIn(DB::raw('DATE(exam_date)'), $examDates)
                ->get()
                ->groupBy('course_id');

            $studentTotalAvg = 0;
            $studentSubjectCount = 0;

            foreach ($studentResults as $courseId => $subjectResults) {
                // Calculate average for each subject using the same combine option
                if ($combineOption == 'individual') {
                    $subjectAvg = $subjectResults->avg('score') ?? 0;
                } elseif ($combineOption == 'sum') {
                    $subjectAvg = $subjectResults->count() > 0 ? $subjectResults->sum('score') / $subjectResults->count() : 0;
                } elseif ($combineOption == 'average') {
                    $subjectAvg = $subjectResults->avg('score') ?? 0;
                }

                $studentTotalAvg += round($subjectAvg, 2);
                $studentSubjectCount++;
            }

            // Calculate student's general average (total of subject averages)
            $allStudentAverages[$stdId] = $studentSubjectCount > 0 ? round($studentTotalAvg, 2) : 0;
        }

        // Sort in descending order and assign positions with tie handling
        arsort($allStudentAverages);
        $generalRanked = [];
        $rank = 1;
        $currentPosition = 1;
        $previousAverage = null;
        $sameRankCount = 0;

        foreach ($allStudentAverages as $std_id => $avg) {
            if ($previousAverage !== null && $avg < $previousAverage) {
                $rank += $sameRankCount;
                $sameRankCount = 1;
            } else {
                $sameRankCount++;
            }

            $generalRanked[$std_id] = $rank;
            $previousAverage = $avg;
        }

        $generalPosition = $generalRanked[$studentId] ?? '-';
        $totalStudents = count($generalRanked);

        $examSpecifications = $results
            ->map(fn($item) => [
                'abbr' => $item->symbolic_abbr,
                'full_name' => $item->exam_type,
                'date' => $item->exam_date
            ])
            ->unique(fn($item) => $item['abbr'] . $item['full_name'])
            ->values()
            ->keyBy('abbr');

        return view('generated_reports.index', compact(
            'finalData',
            'examHeaders',
            'studentGeneralAverage',
            'examHeadersWithDates',
            'results',
            'examAverages',
            'year',
            'generalPosition',
            'totalStudents',
            'student',
            'studentId',
            'reports',
            'combineOption',
            'schoolInfo',
            'examSpecifications',
            'school',
            'report',
            'class',
            'totalScoreForStudent',
            'subjectCount',
        ));
    }

    public function sendSmsForCombinedReport($school, $year, $class, $report, $student)
    {
        try {
            $studentId = Hashids::decode($student)[0];
            $schoolId = Hashids::decode($school)[0];
            $classId = Hashids::decode($class)[0];
            $reportId = Hashids::decode($report)[0];

            $reports = generated_reports::findOrFail($reportId);

            if ($reports->status == 1) {
                $examDates = $reports->exam_dates;

                // ==== REKEDISHA: Pata class_id ya kwenye generated_reports ====
                $storedClassId = $reports->class_id ?? $classId;

                // STEP 1: Fetch all results for the student
                $results = Examination_result::query()
                    ->join('students', 'students.id', '=', 'examination_results.student_id')
                    ->join('grades', 'grades.id', '=', 'examination_results.class_id')
                    ->join('subjects', 'subjects.id', '=', 'examination_results.course_id')
                    ->join('examinations', 'examinations.id', '=', 'examination_results.exam_type_id')
                    ->where('examination_results.student_id', $studentId)
                    ->where('examination_results.class_id', $storedClassId) // TUMIA STORED CLASS ID
                    ->where('examination_results.school_id', $schoolId)
                    ->whereIn(DB::raw('DATE(exam_date)'), $examDates)
                    ->select(
                        'students.id as student_id',
                        'students.first_name',
                        'students.last_name',
                        'students.parent_id',
                        'students.gender',
                        'examination_results.score',
                        'examination_results.exam_date',
                        'examination_results.course_id',
                        'grades.class_name',
                        'examinations.symbolic_abbr',
                        'subjects.course_code'
                    )
                    ->get();

                if ($results->isEmpty()) {
                    Alert()->toast('No results found for this student.', 'error');
                    return to_route('students.combined.report', ['school' => $school, 'year' => $year, 'class' => $class, 'report' => $report]);
                }

                // STEP 2: Group results by subject and calculate averages
                $subjectAverages = $results->groupBy('course_id')->map(function ($subjectResults) {
                    return [
                        'course_code' => $subjectResults->first()->course_code,
                        'average' => $subjectResults->avg('score')
                    ];
                });

                // STEP 2: Calculate totals
                $studentTotal = $results->sum('score');
                $studentAverageTotal = number_format($subjectAverages->sum('average'));
                $studentAverage = $results->avg('score');

                // ==== REKEDISHA: STEP 3: Calculate position WITH TIE RANKING ====
                // Pata wanafunzi wote walio kwenye class iliyohifadhiwa
                $allStudentsScores = Examination_result::where('class_id', $storedClassId) // TUMIA STORED CLASS ID
                    ->where('school_id', $schoolId)
                    ->whereIn(DB::raw('DATE(exam_date)'), $examDates)
                    ->get()
                    ->groupBy('student_id')
                    ->map(function ($studentResults) {
                        return $studentResults->sum('score');
                    });

                // Apply tie ranking logic
                $sortedStudents = $allStudentsScores->sortDesc();

                $rank = 1;
                $previousScore = null;
                $studentsWithRank = collect();
                $index = 0;

                foreach ($sortedStudents as $student_id => $score) {
                    if ($previousScore !== null && $score < $previousScore) {
                        $rank = $index + 1;
                    }

                    $studentsWithRank->push([
                        'student_id' => $student_id,
                        'score' => $score,
                        'rank' => $rank
                    ]);

                    $previousScore = $score;
                    $index++;
                }

                // Find current student's position
                $studentRankData = $studentsWithRank->firstWhere('student_id', $studentId);
                $studentRank = $studentRankData['rank'] ?? '-';
                $totalStudents = $sortedStudents->count();

                // Rest of the code remains the same...
                $studentData = $results->first();
                $parentId = $studentData->parent_id ?? null;

                if (!$parentId) {
                    Alert()->toast('Parent not found for this student.', 'error');
                    return to_route('students.combined.report', ['school' => $school, 'year' => $year, 'class' => $class, 'report' => $report]);
                }

                $parent = Parents::query()->join('users', 'users.id', '=', 'parents.user_id')
                    ->select('parents.*', 'users.phone')
                    ->findOrFail($parentId);
                $phoneNumber = $parent->phone ?? null;

                if (!$phoneNumber) {
                    Alert()->toast('Phone number not found for parent.', 'error');
                    return to_route('students.combined.report', ['school' => $school, 'year' => $year, 'class' => $class, 'report' => $report]);
                }

                // Format SMS message
                $formattedPhone = $this->formatPhoneNumber($phoneNumber);
                $studentName = strtoupper($studentData->first_name . ' ' . $studentData->last_name);
                $reportDate = Carbon::parse($reports->created_at)->format('d-m-Y');
                $schoolInfo = school::find($schoolId);
                $sender = $schoolInfo->sender_id ?? "SHULE APP";
                $link = "https://shuleapp.tech";

                // ==== REKEDISHA: Build subject results part of the message ====
                $subjectResultsText = "";
                foreach ($subjectAverages as $subject) {
                    $averageValue = $subject['average'] ?? 0;
                    $subjectResultsText .= strtoupper($subject['course_code']) . ": " . number_format($averageValue, 1) . "\n";
                }

                // ==== REKEDISHA: Create better formatted message ====
                $message = "MATOKEO YA {$studentName}\n";
                $message .= "==========================\n";
                $message .= "Ripoti: " . strtoupper($reports->title) . "\n";
                $message .= "Tarehe: {$reportDate}\n";
                $message .= "Darasa: {$studentData->class_name}\n";
                $message .= "==========================\n";
                $message .= "MATOKEO YA MASOMO:\n";
                $message .= $subjectResultsText;
                $message .= "==========================\n";
                $message .= "Jumla: {$studentTotal}\n";
                $message .= "Wastani: " . number_format($studentAverage, 1) . "\n";

                if ($studentRank !== '-' && $totalStudents > 0) {
                    $message .= "Nafasi: {$studentRank} kati ya {$totalStudents}\n";
                } else {
                    $message .= "Nafasi: N/A\n";
                }

                $message .= "==========================\n";
                $message .= "Pakua ripoti: {$link}";

                // Check message length
                if (strlen($message) > 480) {
                    // Trim if too long
                    $message = substr($message, 0, 477) . "...";
                }

                try {
                    $nextSmsService = new NextSmsService();
                    $response = $nextSmsService->sendSmsByNext(
                        $sender,
                        $formattedPhone,
                        $message,
                        uniqid()
                    );

                    if (!$response['success']) {
                        Alert()->toast('SMS failed: ' . $response['error'], 'error');
                        return back();
                    }

                    Alert()->toast('Results SMS has been Re-sent successfully', 'success');
                    return to_route('students.combined.report', [
                        'school' => $school,
                        'year' => $year,
                        'class' => $class,
                        'report' => $report
                    ]);
                } catch (\Exception $e) {
                    Alert()->toast('Failed to send SMS: ' . $e->getMessage(), 'error');
                    return redirect()->back();
                }
            } else {
                Alert()->toast('This report data set is locked 🔐, please unlock it first', 'error');
                return to_route('students.combined.report', [
                    'school' => $school,
                    'year' => $year,
                    'class' => $class,
                    'report' => $report
                ]);
            }
        } catch (\Exception $e) {
            Alert()->toast('Error: ' . $e->getMessage(), 'error');
            return redirect()->back();
        }
    }
    //download student report
    public function downloadCombinedReport($school, $year, $class, $report, $student)
    {
        $studentId = Hashids::decode($student)[0];
        $schoolId = Hashids::decode($school)[0];
        $classId = Hashids::decode($class)[0];
        $reportId = Hashids::decode($report)[0];

        $reports = generated_reports::find($reportId);
        $examDates = $reports->exam_dates; // array

        // ==== REKEDISHA 1: Pata class_id ya kwenye generated_reports ====
        $storedClassId = $reports->class_id ?? $classId;

        $results = Examination_result::query()
            ->join('students', 'students.id', '=', 'examination_results.student_id')
            ->join('grades', 'grades.id', '=', 'examination_results.class_id')
            ->join('subjects', 'subjects.id', '=', 'examination_results.course_id')
            ->join('examinations', 'examinations.id', '=', 'examination_results.exam_type_id')
            ->join('schools', 'schools.id', '=', 'examination_results.school_id')
            ->join('teachers', 'teachers.id', '=', 'examination_results.teacher_id')
            ->leftJoin('users', 'users.id', '=', 'teachers.user_id')
            ->select(
                'students.id as studentId',
                'students.first_name',
                'students.middle_name',
                'students.last_name',
                'students.group',
                'students.gender',
                'students.image',
                'subjects.id as subjectId',
                'subjects.course_name',
                'subjects.course_code',
                'students.admission_number',
                'grades.class_name',
                'grades.class_code',
                'examination_results.*',
                'examinations.exam_type',
                'examinations.symbolic_abbr',
                'schools.school_name',
                'schools.school_reg_no',
                'schools.postal_address',
                'schools.postal_name',
                'schools.logo',
                'schools.country',
                'users.first_name as teacher_first_name',
                'users.last_name as teacher_last_name',
                'teachers.id as teacher_id' // ==== REKEDISHA: Ongeza teacher_id ====
            )
            ->where('examination_results.student_id', $studentId)
            ->where('examination_results.class_id', $storedClassId) // TUMIA STORED CLASS ID
            ->where('examination_results.school_id', $schoolId)
            ->whereIn(DB::raw('DATE(exam_date)'), $examDates)
            ->get();

        $classResultsGrouped = $results->groupBy('subjectId');

        // Badilisha uundaji wa examHeaders kuwa kwa kila mtihani tofauti
        $examHeaders = $results->map(function ($item) {
            return [
                'abbr' => $item->symbolic_abbr,
                'date' => $item->exam_date,
                'display' => $item->symbolic_abbr . ' ' . \Carbon\Carbon::parse($item->exam_date)->format('d M Y')
            ];
        })->unique(function ($item) {
            return $item['abbr'] . $item['date'];
        })->values();

        $finalData = [];
        $combineOption = $reports->combine_option ?? 'individual';

        // Vigezo vya kuweka jumla za mwanafunzi
        $studentTotalMarks = 0;
        $subjectCount = 0;

        foreach ($classResultsGrouped as $subjectId => $subjectResults) {
            $subjectName = $subjectResults->first()->course_name;
            $subjectCode = $subjectResults->first()->course_code;

            // ==== REKEDISHA 2: Tafuta mwalimu aliyepakia matokeo ====
            $uploadingTeacher = null;
            foreach ($subjectResults as $result) {
                if ($result->teacher_id) {
                    $uploadingTeacher = DB::table('teachers')
                        ->join('users', 'users.id', '=', 'teachers.user_id')
                        ->where('teachers.id', $result->teacher_id)
                        ->select('users.first_name', 'users.last_name')
                        ->first();
                    break;
                }
            }

            $teacher = 'N/A';
            if ($uploadingTeacher) {
                $teacher = $uploadingTeacher->first_name . '. ' . substr($uploadingTeacher->last_name, 0, 1);
            } else {
                // Fallback: tumia mwalimu kutoka kwenye results (iliyokuwa awali)
                $firstResult = $subjectResults->first();
                if ($firstResult && $firstResult->teacher_first_name && $firstResult->teacher_last_name) {
                    $teacher = $firstResult->teacher_first_name . '. ' . substr($firstResult->teacher_last_name, 0, 1);
                }
            }

            $examScores = [];
            $total = 0;
            $average = 0;

            if ($combineOption == 'individual') {
                foreach ($examHeaders as $exam) {
                    $score = $subjectResults->where('symbolic_abbr', $exam['abbr'])
                        ->where('exam_date', $exam['date'])
                        ->first()->score ?? null;
                    $examScores[$exam['abbr'] . '_' . $exam['date']] = $score;
                }

                $total = collect($examScores)->filter()->sum();
                $average = collect($examScores)->filter()->avg() ?? 0;
            } elseif ($combineOption == 'sum') {
                foreach ($examHeaders as $exam) {
                    $score = $subjectResults->where('symbolic_abbr', $exam['abbr'])
                        ->where('exam_date', $exam['date'])
                        ->first()->score ?? null;
                    $examScores[$exam['abbr'] . '_' . $exam['date']] = $score;
                }
                $total = collect($examScores)->sum();
                $average = count(array_filter($examScores)) > 0 ? $total / count(array_filter($examScores)) : 0;
            } elseif ($combineOption == 'average') {
                foreach ($examHeaders as $exam) {
                    $score = $subjectResults->where('symbolic_abbr', $exam['abbr'])
                        ->where('exam_date', $exam['date'])
                        ->first()->score ?? null;
                    $examScores[$exam['abbr'] . '_' . $exam['date']] = $score;
                }
                $filtered = collect($examScores)->filter();
                $total = 0;
                $average = $filtered->count() > 0 ? $filtered->avg() : 0;
            }

            // Ongeza kwa jumla za mwanafunzi
            $studentTotalMarks += $average;
            $subjectCount++;

            $finalData[] = [
                'subjectName' => $subjectName,
                'teacher' => $teacher,
                'subjectCode' => $subjectCode,
                'examScores' => $examScores,
                'total' => $total,
                'average' => round($average, 2),
                'position' => 0 // Placeholder, itabakiweka baada ya rank calculation
            ];
        }

        // **Sasa hesabu position ya kila somo**
        foreach ($finalData as &$subject) {
            $allStudentSubjectAverages = [];

            // Pata wanafunzi wote na averages zao kwenye somo hili
            $subjectId = DB::table('subjects')
                ->where('course_name', $subject['subjectName'])
                ->where('course_code', $subject['subjectCode'])
                ->value('id');

            if ($subjectId) {
                // Get all students' averages for this subject
                $subjectResultsAll = DB::table('examination_results')
                    ->join('subjects', 'subjects.id', '=', 'examination_results.course_id')
                    ->where('examination_results.course_id', $subjectId)
                    ->where('examination_results.class_id', $storedClassId) // TUMIA STORED CLASS ID
                    ->where('examination_results.school_id', $schoolId)
                    ->whereIn(DB::raw('DATE(exam_date)'), $examDates)
                    ->select('examination_results.*', 'subjects.course_name')
                    ->get()
                    ->groupBy('student_id');

                foreach ($subjectResultsAll as $student_id => $studentResults) {
                    // Calculate average for each student using the same combine option
                    if ($combineOption == 'individual') {
                        $avg = $studentResults->avg('score') ?? 0;
                    } elseif ($combineOption == 'sum') {
                        $avg = $studentResults->count() > 0 ? $studentResults->sum('score') / $studentResults->count() : 0;
                    } elseif ($combineOption == 'average') {
                        $avg = $studentResults->avg('score') ?? 0;
                    }

                    $allStudentSubjectAverages[$student_id] = round($avg, 2);
                }

                // Sort in descending order and assign positions with tie handling
                arsort($allStudentSubjectAverages);
                $positions = [];
                $position = 1;
                $previousAverage = null;
                $sameRankCount = 0;

                foreach ($allStudentSubjectAverages as $std_id => $avg) {
                    if ($previousAverage !== null && $avg < $previousAverage) {
                        $position += $sameRankCount;
                        $sameRankCount = 1;
                    } else {
                        $sameRankCount++;
                    }

                    $positions[$std_id] = $position;
                    $previousAverage = $avg;
                }

                // Set the position for this student
                $subject['position'] = $positions[$studentId] ?? '-';
            }
        }
        unset($subject);

        $students = $results->first();
        $schoolInfo = $results->first();

        // **Calculate Student General Average** (jumla ya average za masomo / idadi ya masomo)
        $studentGeneralAverage = $subjectCount > 0 ? round($studentTotalMarks / $subjectCount, 2) : 0;

        // **Total Marks** (jumla ya average za masomo yote - zilizokaa rounded)
        $totalScoreForStudent = round($studentTotalMarks, 2);

        // =================== EXAM HEADERS WITH DATES ===================
        $examHeadersWithDates = $results
            ->mapWithKeys(function ($item) {
                return [$item->symbolic_abbr => $item->exam_date];
            })->unique()->toBase();

        // =================== EXAM AVERAGE PER EXAM DATE ===================
        $examAverages = [];
        foreach ($examHeaders as $exam) {
            $totalPerExam = 0;
            $countPerExam = 0;
            $abbr = $exam['abbr'];
            $date = $exam['date'];

            foreach ($finalData as $subject) {
                $score = $subject['examScores'][$abbr . '_' . $date] ?? null;
                if (is_numeric($score)) {
                    $totalPerExam += $score;
                    $countPerExam++;
                }
            }

            $examAverages[$abbr . '_' . $date] = $countPerExam > 0 ? round($totalPerExam / $countPerExam, 2) : 0;
        }

        // =================== GENERAL POSITION (USING TOTAL MARKS WITH 1 DECIMAL) ===================
        // Pata average za wanafunzi wote kwa kutumia logic ile ile
        $allStudentAverages = [];

        // ==== REKEDISHA 3: Pata wanafunzi wote kutoka kwa examination_results ====
        // Hii inahakikisha tunapata wanafunzi waliokuwepo wakati wa mtihani
        $allStudents = DB::table('examination_results')
            ->where('class_id', $storedClassId) // TUMIA STORED CLASS ID
            ->where('school_id', $schoolId)
            ->whereIn(DB::raw('DATE(exam_date)'), $examDates)
            ->distinct()
            ->pluck('student_id');

        foreach ($allStudents as $stdId) {
            // Pata results za mwanafunzi huyu
            $studentResults = DB::table('examination_results')
                ->where('student_id', $stdId)
                ->where('class_id', $storedClassId) // TUMIA STORED CLASS ID
                ->where('school_id', $schoolId)
                ->whereIn(DB::raw('DATE(exam_date)'), $examDates)
                ->get()
                ->groupBy('course_id');

            $studentTotalAvg = 0;
            $studentSubjectCount = 0;

            foreach ($studentResults as $courseId => $subjectResults) {
                // Calculate average for each subject using the same combine option
                if ($combineOption == 'individual') {
                    $subjectAvg = $subjectResults->avg('score') ?? 0;
                } elseif ($combineOption == 'sum') {
                    $subjectAvg = $subjectResults->count() > 0 ? $subjectResults->sum('score') / $subjectResults->count() : 0;
                } elseif ($combineOption == 'average') {
                    $subjectAvg = $subjectResults->avg('score') ?? 0;
                }

                $studentTotalAvg += round($subjectAvg, 2);
                $studentSubjectCount++;
            }

            // Calculate student's general average (total of subject averages)
            $allStudentAverages[$stdId] = $studentSubjectCount > 0 ? round($studentTotalAvg, 2) : 0;
        }

        // Sort in descending order and assign positions with tie handling
        arsort($allStudentAverages);
        $generalRanked = [];
        $rank = 1;
        $currentPosition = 1;
        $previousAverage = null;
        $sameRankCount = 0;

        foreach ($allStudentAverages as $std_id => $avg) {
            if ($previousAverage !== null && $avg < $previousAverage) {
                $rank += $sameRankCount;
                $sameRankCount = 1;
            } else {
                $sameRankCount++;
            }

            $generalRanked[$std_id] = $rank;
            $previousAverage = $avg;
        }

        $generalPosition = $generalRanked[$studentId] ?? '-';
        $totalStudents = count($generalRanked);

        // =================== EXAM SPECIFICATIONS ===================
        $examSpecifications = $results
            ->map(function ($item) {
                return [
                    'abbr' => $item->symbolic_abbr,
                    'full_name' => $item->exam_type,
                    'date' => $item->exam_date
                ];
            })
            ->unique(function ($item) {
                return $item['abbr'] . $item['full_name'];
            })
            ->values()
            ->keyBy('abbr');

        $verificationData = [
            'student_name' => trim(
                $students->first_name . ' ' . $students->middle_name . ' ' . $students->last_name
            ),
            'admission_number' => $students->admission_number,
            'class' => $students->class_name,
            'report_type' => $reports->title,
            'term' => $reports->term,
            'school' => $schoolInfo->school_name,
            'report_date' => $reports->created_at->format('Y-m-d'),
            'report_id' => $reports->id,
            'issued_at' => now()->timestamp,

            // ================== SUMMARY INFO ==================
            'total_score' => $totalScoreForStudent ?? 0,
            'average_score' => $studentGeneralAverage ?? 0,
            'student_rank' => $generalPosition ?? '-',
            'total_students' => $totalStudents ?? 0,
        ];

        $verificationData['signature'] = hash_hmac(
            'sha256',
            json_encode($verificationData),
            config('app.key')
        );

        $encryptedPayload = Crypt::encryptString(
            json_encode($verificationData)
        );

        $verificationUrl = route('report.verify', [
            'payload' => $encryptedPayload
        ]);

        $result = Builder::create()
            ->writer(new PngWriter())
            ->data($verificationUrl)
            ->size(150)
            ->margin(5)
            ->build();

        $qrPng = base64_encode($result->getString());

        $pdf = \PDF::loadView('generated_reports.compiled_report', compact(
            'finalData',
            'examHeaders',
            'studentGeneralAverage',
            'examHeadersWithDates',
            'results',
            'examAverages',
            'year',
            'generalPosition',
            'totalStudents',
            'students',
            'reports',
            'schoolInfo',
            'examSpecifications',
            'school',
            'report',
            'class',
            'totalScoreForStudent',
            'subjectCount',
            'reportId',
            'classId',
            'qrPng'
        ));

        $timestamp = Carbon::now()->timestamp;
        $fileName = "report_{$timestamp}.pdf";
        $folderPath = public_path('reports');

        if (!File::exists($folderPath)) {
            File::makeDirectory($folderPath, 0755, true);
        }

        $pdf->save($folderPath . '/' . $fileName);
        $fileUrl = asset('reports/' . $fileName);

        return view('generated_reports.pdf_report', compact('fileUrl', 'year', 'reports', 'class', 'school', 'report', 'students', 'studentId', 'schoolId', 'classId', 'reportId'));
    }

    //publish combined report to parents
    public function publishCombinedReport($school, $year, $class, $report)
    {
        $schoolId = Hashids::decode($school)[0];
        $classId = Hashids::decode($class)[0];
        $reportId = Hashids::decode($report)[0];
        $status = 1; // Published status

        try {
            // STEP 1: Update report status to "published"
            $report = generated_reports::findOrFail($reportId);

            if (!$report) {
                Alert()->toast('Report not found.', 'error');
                return redirect()->back();
            }

            // ==== REKEDISHA: Pata class_id ya kwenye generated_reports ====
            $storedClassId = $report->class_id ?? $classId;

            // Fanya update moja tu
            $updatedReport = $report->update(['status' => $status]);

            if (!$updatedReport) {
                Alert()->toast('Failed to publish the report.', 'error');
                return redirect()->back();
            }

            // STEP 2: Get exam dates from the report
            $examDates = $report->exam_dates; // Array of dates

            // STEP 3: Fetch all exam results for these dates (same as PDF logic)
            $results = Examination_result::query()
                ->join('students', 'students.id', '=', 'examination_results.student_id')
                ->join('grades', 'grades.id', '=', 'examination_results.class_id')
                ->join('subjects', 'subjects.id', '=', 'examination_results.course_id')
                ->join('examinations', 'examinations.id', '=', 'examination_results.exam_type_id')
                ->select(
                    'students.id as student_id',
                    'students.first_name',
                    'students.middle_name',
                    'students.last_name',
                    'students.parent_id',
                    'students.status',
                    'examinations.symbolic_abbr',
                    'examination_results.score',
                    'examination_results.exam_date',
                    'examination_results.course_id',
                    'subjects.course_code'
                )
                ->where('students.status', 1) // Only active students
                ->where('examination_results.class_id', $storedClassId) // TUMIA STORED CLASS ID
                ->where('examination_results.school_id', $schoolId)
                ->whereIn(DB::raw('DATE(exam_date)'), $examDates)
                ->get();

            // Check if results exist
            if ($results->isEmpty()) {
                Alert()->toast('No exam results found for these dates.', 'error');
                return redirect()->back();
            }

            // STEP 4: Group results by student and calculate TOTAL SCORE (like PDF)
            $studentsData = $results->groupBy('student_id')->map(function ($studentResults) {
                // Group by subject and calculate averages
                $subjectAverages = $studentResults->groupBy('course_id')->map(function ($subjectResults) {
                    return [
                        'course_code' => $subjectResults->first()->course_code,
                        'average' => $subjectResults->avg('score')
                    ];
                });

                $totalScore = $subjectAverages->sum('average');
                $totalAverage = $studentResults->avg('score'); // Direct average of all scores

                return [
                    'student_id' => $studentResults->first()->student_id,
                    'first_name' => $studentResults->first()->first_name,
                    'middle_name' => $studentResults->first()->middle_name,
                    'last_name' => $studentResults->first()->last_name,
                    'parent_id' => $studentResults->first()->parent_id,
                    'total_score' => $totalScore,
                    'total_average' => $totalAverage,
                    'subject_averages' => $subjectAverages,
                ];
            });

            // STEP 5: Sort students by total_score (descending) and assign ranks (like PDF)
            $sortedStudents = $studentsData->sortByDesc('total_score')->values();

            $rank = 1;
            $previousScore = null;
            $previousRank = 1;

            $studentsWithRank = $sortedStudents->map(function ($student, $index) use (&$rank, &$previousScore, &$previousRank) {
                if ($previousScore !== null && $student['total_score'] < $previousScore) {
                    $rank = $index + 1;
                }

                $student['rank'] = $rank;
                $previousScore = $student['total_score'];
                $previousRank = $rank;

                return $student;
            });

            // STEP 6: Prepare SMS payload (updated to match PDF calculations)
            $parentsPayload = $studentsWithRank->map(function ($student) use ($studentsWithRank) {
                $totalStudents = $studentsWithRank->count();
                $positionText = "{$student['rank']} kati ya {$totalStudents}";

                // Build subject results text
                $subjectResultsText = "";
                foreach ($student['subject_averages'] as $subject) {
                    $subjectAverage = $subject['average'] ?? 0;
                    $subjectResultsText .= strtoupper($subject['course_code']) . ": " . number_format($subjectAverage, 1) . "\n";
                }

                return [
                    'student_id' => $student['student_id'],
                    'parent_id' => $student['parent_id'],
                    'student_name' => strtoupper($student['first_name'] . ' ' . $student['last_name']),
                    'position' => $positionText,
                    'total_score' => number_format($student['total_score'], 1),
                    'total_average' => number_format($student['total_average'], 1), // 1 decimal place
                    'subject_results' => $subjectResultsText
                ];
            });

            // STEP 7: Send SMS to parents (updated message format)
            $successCount = 0;
            $failCount = 0;
            $schoolInfo = school::find($schoolId);
            $link = "https://shuleapp.tech";
            $sender = $schoolInfo->sender_id ?? "SHULE APP";
            $reportDate = Carbon::parse($report->created_at)->format('d-m-Y');
            $nextSmsService = new NextSmsService();

            foreach ($parentsPayload as $payload) {
                try {
                    // ==== REKEDISHA: Check if parent exists ====
                    $parent = Parents::find($payload['parent_id']);

                    if (!$parent) {
                        // Log::warning("Parent not found for student ID: {$payload['student_id']}");
                        $failCount++;
                        continue;
                    }

                    // ==== REKEDISHA: Check if user exists ====
                    $user = User::find($parent->user_id);

                    if (!$user || empty($user->phone)) {
                        // Log::warning("User or phone not found for parent ID: {$parent->id}");
                        $failCount++;
                        continue;
                    }

                    $phoneNumber = $this->formatPhoneNumber($user->phone);

                    if (!$phoneNumber) {
                        // Log::warning("Invalid phone number format: {$user->phone}");
                        $failCount++;
                        continue;
                    }

                    // ==== REKEDISHA: Create better formatted message ====
                    $message = "MATOKEO YA {$payload['student_name']}\n";
                    $message .= "==========================\n";
                    $message .= "Ripoti: " . strtoupper($report->title) . "\n";
                    $message .= "Tarehe: {$reportDate}\n";
                    $message .= "==========================\n";
                    $message .= "MATOKEO YA MASOMO:\n";
                    $message .= $payload['subject_results'];
                    $message .= "==========================\n";
                    $message .= "Jumla: {$payload['total_score']}\n";
                    $message .= "Wastani: {$payload['total_average']}\n";
                    $message .= "Nafasi: {$payload['position']}\n";
                    $message .= "==========================\n";
                    $message .= "Pakua ripoti: {$link}";

                    // Check message length
                    if (strlen($message) > 480) {
                        $message = substr($message, 0, 477) . "...";
                    }

                    // Log::info("Sending SMS to {$phoneNumber} for student: {$payload['student_name']}");
                    $response = $nextSmsService->sendSmsByNext($sender, $phoneNumber, $message, uniqid());

                    if ($response['success']) {
                        $successCount++;
                        // Log::info("SMS sent successfully to {$phoneNumber}");
                    } else {
                        $failCount++;
                        // Log::error("SMS failed for {$phoneNumber}: {$response['error']}");
                    }

                    // Add small delay to avoid rate limiting
                    usleep(100000); // 0.1 second delay

                } catch (\Exception $e) {
                    $failCount++;
                    // Log::error("Error sending SMS for student ID {$payload['student_id']}: " . $e->getMessage());
                    continue;
                }
            }

            // Show summary alert
            if ($failCount > 0) {
                Alert()->toast("Report published! {$successCount} SMS sent successfully, {$failCount} failed.", 'warning');
            } else {
                Alert()->toast("Report has been published and {$successCount} SMS sent to parents successfully!", 'success');
            }

            return to_route('results.examTypesByClass', ['school' => $school, 'year' => $year, 'class' => $class]);
        } catch (Exception $e) {
            Alert()->toast('Error: ' . $e->getMessage(), 'error');
            return to_route('results.examTypesByClass', ['school' => $school, 'year' => $year, 'class' => $class]);
        }
    }

    //unpublish combined report
    public function unpublishCombinedReport($school, $year, $class, $report)
    {
        $school_id = Hashids::decode($school);
        $class_id = Hashids::decode($class);
        $report_id = Hashids::decode($report);
        $status = 0;

        $reports = generated_reports::findOrFail($report_id[0]);
        // return $reports;
        try {
            $reports->update(['status' => $status]);

            Alert()->toast('Results data set has been Locked successfully 🔐', 'success');
            return to_route('results.examTypesByClass', ['school' => $school, 'year' => $year, 'class' => $class]);
        } catch (Exception $e) {
            Alert()->toast($e->getMessage(), 'error');
            return to_route('results.examTypesByClass', ['school' => $school, 'year' => $year, 'class' => $class]);
        }
    }

    public function downloadGeneralCombinedReport($school, $year, $class, $report)
    {
        $schoolId = Hashids::decode($school)[0];
        $classId = Hashids::decode($class)[0];
        $reportId = Hashids::decode($report)[0];

        $reports = generated_reports::find($reportId);
        $examDates = $reports->exam_dates;
        $marking_style = $reports->marking_style ?? 2; // Default to style 2 if not set

        // 1. GET ALL STUDENTS IN THE CLASS
        $students = Student::where('class_id', $classId)
            ->where('school_id', $schoolId)
            ->where('status', 1) // Only active students
            ->orderBy('admission_number')
            ->get();

        // 2. GET ALL SUBJECTS FOR THIS CLASS
        $subjects = Subject::whereHas('examination_results', function ($query) use ($classId, $schoolId, $examDates) {
            $query->where('class_id', $classId)
                ->where('school_id', $schoolId)
                ->whereIn(DB::raw('DATE(exam_date)'), $examDates);
        })
            ->select('id', 'course_name', 'course_code')
            ->get();

        // 3. GET ALL EXAM RESULTS
        $results = Examination_result::query()
            ->join('students', 'students.id', '=', 'examination_results.student_id')
            ->join('subjects', 'subjects.id', '=', 'examination_results.course_id')
            ->select(
                'students.id as student_id',
                'students.admission_number',
                'students.first_name',
                'students.middle_name',
                'students.last_name',
                'students.gender',
                'students.status',
                'subjects.id as subject_id',
                'subjects.course_name',
                'subjects.course_code',
                'examination_results.score',
                'examination_results.exam_type_id',
                'examination_results.Exam_term',
                'examination_results.exam_date',
                'examination_results.marking_style'
            )
            ->where('examination_results.class_id', $classId)
            ->where('students.status', 1) // Only active students
            ->where('examination_results.school_id', $schoolId)
            ->whereIn(DB::raw('DATE(exam_date)'), $examDates)
            ->get();

        $totalCandidates = $results->pluck('student_id')->unique()->count();
        // 4. GROUP RESULTS BY STUDENT AND CALCULATE AVERAGES
        $studentData = [];
        $subjectCodes = $subjects->pluck('course_code')->toArray();

        foreach ($students as $student) {
            $studentResults = $results->where('student_id', $student->id);

            $studentSubjectAverages = [];
            $totalScore = 0;
            $subjectCount = 0;

            foreach ($subjects as $subject) {
                $subjectScores = $studentResults->where('subject_id', $subject->id)->pluck('score');
                $average = $subjectScores->avg();

                if (!is_null($average)) {
                    $roundedAverage = number_format($average, 2);
                    $studentSubjectAverages[$subject->course_code] = [
                        'score' => $roundedAverage,
                        'grade' => $this->calculateGrade($roundedAverage, $results->first()->marking_style)
                    ];
                    $totalScore += $roundedAverage;
                    $subjectCount++;
                } else {
                    $studentSubjectAverages[$subject->course_code] = [
                        'score' => null,
                        'grade' => null
                    ];
                }
            }

            $overallAverage = $subjectCount > 0 ? number_format($totalScore / $subjectCount, 2) : 0;

            $studentData[] = [
                'student_id' => $student->id,
                'admission_number' => $student->admission_number,
                'gender' => $student->gender,
                'student_name' => $student->first_name . ' ' . ($student->middle_name ? $student->middle_name . ' ' : '') . $student->last_name,
                'subject_averages' => $studentSubjectAverages,
                'total' => round($totalScore, 2),
                'average' => round($overallAverage, 2),
                'grade' => $this->calculateGrade($overallAverage, $results->first()->marking_style),
            ];
        }

        // 5. CALCULATE STUDENT RANKS WITH TIE HANDLING
        $sortedStudents = collect($studentData)->sortByDesc('total')->values();
        $rankedStudents = [];
        $previousScore = null;
        $currentRank = 0;
        $skip = 0;

        foreach ($sortedStudents as $index => $student) {
            if ($previousScore !== null && $student['total'] == $previousScore) {
                // Tie: keep same rank
                $student['rank'] = $currentRank;
                $skip++;
            } else {
                // New rank
                $currentRank = $index + 1;
                $student['rank'] = $currentRank;
                $skip = 0;
            }

            $rankedStudents[] = $student;
            $previousScore = $student['total'];
        }

        // 6. CALCULATE GRADE DISTRIBUTION SUMMARY
        $gradeSummary = [
            'male' => ['A' => 0, 'B' => 0, 'C' => 0, 'D' => 0, 'E' => 0],
            'female' => ['A' => 0, 'B' => 0, 'C' => 0, 'D' => 0, 'E' => 0],
            'total' => ['A' => 0, 'B' => 0, 'C' => 0, 'D' => 0, 'E' => 0]
        ];

        foreach ($rankedStudents as $student) {
            $gender = strtolower($student['gender']);
            $grade = $student['grade'];

            if (isset($gradeSummary[$gender][$grade])) {
                $gradeSummary[$gender][$grade]++;
            }
            if (isset($gradeSummary['total'][$grade])) {
                $gradeSummary['total'][$grade]++;
            }
        }

        // 7. CALCULATE SUBJECT AVERAGES AND OVERALL AVERAGE
        $subjectAverages = [];
        $overallTotalAverage = 0;
        $subjectCount = 0;
        $subjectAveragesSum = 0;

        foreach ($subjects as $subject) {
            $subjectTotal = 0;
            $studentCount = 0;

            foreach ($rankedStudents as $student) {
                if (isset($student['subject_averages'][$subject->course_code]['score'])) {
                    $subjectTotal += $student['subject_averages'][$subject->course_code]['score'];
                    $studentCount++;
                }
            }

            if ($studentCount > 0) {
                $subjectAverage = number_format($subjectTotal / $studentCount, 2);
                $subjectAverages[$subject->course_code] = [
                    'average' => $subjectAverage,
                    'grade' => $this->calculateGrade($subjectAverage, $results->first()->marking_style)
                ];
                $overallTotalAverage += $subjectAverage;
                $subjectAveragesSum += $subjectAverage; // Ongeza wastani wa kila somo kwenye jumla
                $subjectCount++;
            }
        }

        $overallTotalAverage = $subjectCount > 0 ? number_format($overallTotalAverage / $subjectCount, 3) : 0;
        $overallGrade = $this->calculateGrade($overallTotalAverage, $results->first()->marking_style);

        // 8. PREPARE SUBJECT RANKING DATA WITH POSITIONS
        $subjectPerformance = [];
        foreach ($subjects as $subject) {
            $subjectAverages = [];

            foreach ($studentData as $student) {
                if (isset($student['subject_averages'][$subject->course_code]['score'])) {
                    $subjectAverages[] = $student['subject_averages'][$subject->course_code]['score'];
                }
            }

            $subjectAverage = count($subjectAverages) > 0 ? number_format(array_sum($subjectAverages) / count($subjectAverages), 2) : 0;

            $subjectPerformance[] = [
                'subject_id' => $subject->id,
                'subject_code' => $subject->course_code,
                'subject_name' => $subject->course_name,
                'average' => $subjectAverage,
                'grade' => $this->calculateGrade($subjectAverage, $results->first()->marking_style),
            ];
        }

        // Sort subjects by average (highest first) and assign positions with tie handling
        $sortedSubjects = collect($subjectPerformance)->sortByDesc('average');

        $subjectPositions = [];
        $currentPosition = 1;
        $previousAverage = null;
        $skip = 0;

        foreach ($sortedSubjects as $index => $subject) {
            if ($previousAverage !== null && $subject['average'] < $previousAverage) {
                $currentPosition += $skip + 1;
                $skip = 0;
            } elseif ($previousAverage !== null && $subject['average'] == $previousAverage) {
                $skip++;
            }

            $subject['position'] = $currentPosition;
            $subjectPositions[] = $subject;
            $previousAverage = $subject['average'];
        }

        // 9. PREPARE PERFORMANCE ANALYSIS BY GENDER
        $performanceAnalysis = [];
        foreach ($subjects as $subject) {
            $maleGrades = ['A' => 0, 'B' => 0, 'C' => 0, 'D' => 0, 'E' => 0];
            $femaleGrades = ['A' => 0, 'B' => 0, 'C' => 0, 'D' => 0, 'E' => 0];

            foreach ($studentData as $student) {
                if (isset($student['subject_averages'][$subject->course_code]['grade'])) {
                    $grade = $student['subject_averages'][$subject->course_code]['grade'];

                    if ($student['gender'] == 'male') {
                        $maleGrades[$grade]++;
                    } else {
                        $femaleGrades[$grade]++;
                    }
                }
            }

            $performanceAnalysis[] = [
                'subject_code' => $subject->course_code,
                'subject_name' => $subject->course_name,
                'male_grades' => $maleGrades,
                'female_grades' => $femaleGrades,
            ];
        }

        // 10. GET SCHOOL INFO
        $schoolInfo = School::find($schoolId);
        $classInfo = Grade::find($classId);

        // 11. GENERATE PDF
        $pdf = \PDF::loadView('generated_reports.general_combine_report', compact(
            'rankedStudents',
            'subjectCodes',
            'subjectPositions',
            'performanceAnalysis',
            'gradeSummary',
            'subjectAverages',
            'overallTotalAverage',
            'totalCandidates',
            'overallGrade',
            'subjectAveragesSum',
            'year',
            'schoolInfo',
            'classInfo',
            'class',
            'reports',
            'results',
            'marking_style'
        ));

        $timestamp = Carbon::now()->timestamp;
        $fileName = "report_{$timestamp}.pdf";
        $folderPath = public_path('reports');

        if (!File::exists($folderPath)) {
            File::makeDirectory($folderPath, 0755, true);
        }

        $pdf->save($folderPath . '/' . $fileName);
        $fileUrl = asset('reports/' . $fileName);

        return view('generated_reports.general_pdf', compact('fileUrl', 'results', 'year', 'reports', 'class', 'school', 'report', 'schoolInfo'));
    }

    //download parent_student combined report
    public function parentDownloadStudentCombinedReport($school, $year, $report, $student, $class)
    {
        $studentId = Hashids::decode($student)[0];
        // return $studentId;
        $schoolId = Hashids::decode($school)[0];
        $reportId = Hashids::decode($report)[0];
        $classId = Hashids::decode($class)[0];

        $reports = generated_reports::find($reportId);
        $examDates = $reports->exam_dates; // array

        $results = Examination_result::query()
            ->join('students', 'students.id', '=', 'examination_results.student_id')
            ->join('grades', 'grades.id', '=', 'examination_results.class_id')
            ->join('subjects', 'subjects.id', '=', 'examination_results.course_id')
            ->join('examinations', 'examinations.id', '=', 'examination_results.exam_type_id')
            ->join('schools', 'schools.id', '=', 'examination_results.school_id')
            ->join('teachers', 'teachers.id', '=', 'examination_results.teacher_id')
            ->leftJoin('users', 'users.id', '=', 'teachers.user_id')
            ->select(
                'students.id as studentId',
                'students.first_name',
                'students.middle_name',
                'students.last_name',
                'students.group',
                'students.gender',
                'students.image',
                'subjects.id as subjectId',
                'subjects.course_name',
                'subjects.course_code',
                'students.admission_number',
                'grades.class_name',
                'grades.class_code',
                'examination_results.*',
                'examinations.exam_type',
                'examinations.symbolic_abbr',
                'schools.school_name',
                'schools.school_reg_no',
                'schools.postal_address',
                'schools.postal_name',
                'schools.logo',
                'schools.country',
                'users.first_name as teacher_first_name',
                'users.last_name as teacher_last_name'
            )
            ->where('examination_results.student_id', $studentId)
            ->where('examination_results.class_id', $classId)
            ->where('examination_results.school_id', $schoolId)
            ->whereIn(DB::raw('DATE(exam_date)'), $examDates)
            ->get();

        if ($results->isEmpty()) {
            Alert()->toast('No examination results found for the selected student and report.', 'error');
            return redirect()->back();
        }

        $classResultsGrouped = $results->groupBy('subjectId');

        // Badilisha uundaji wa examHeaders kuwa kwa kila mtihani tofauti
        $examHeaders = $results->map(function ($item) {
            return [
                'abbr' => $item->symbolic_abbr,
                'date' => $item->exam_date,
                'display' => $item->symbolic_abbr . ' ' . \Carbon\Carbon::parse($item->exam_date)->format('d M Y')
            ];
        })->unique(function ($item) {
            return $item['abbr'] . $item['date']; // Unique kwa mchanganyiko wa abbreviation na tarehe
        })->values();

        $finalData = [];
        $combineOption = $reports->combine_option ?? 'individual';

        foreach ($classResultsGrouped as $subjectId => $subjectResults) {
            $subjectName = $subjectResults->first()->course_name;
            $subjectCode = $subjectResults->first()->course_code;
            $teacher = $subjectResults->first()->teacher_first_name . '. ' . $subjectResults->first()->teacher_last_name[0];

            $examScores = [];
            $total = 0;
            $average = 0;

            if ($combineOption == 'individual') {
                foreach ($examHeaders as $exam) {
                    $score = $subjectResults->where('symbolic_abbr', $exam['abbr'])
                        ->where('exam_date', $exam['date'])
                        ->first()->score ?? null;
                    $examScores[$exam['abbr'] . '_' . $exam['date']] = $score;
                }

                $total = collect($examScores)->filter()->sum();
                $average = collect($examScores)->filter()->avg() ?? 0;
            } elseif ($combineOption == 'sum') {
                foreach ($examHeaders as $exam) {
                    $score = $subjectResults->where('symbolic_abbr', $exam['abbr'])
                        ->where('exam_date', $exam['date'])
                        ->first()->score ?? null;
                    $examScores[$exam['abbr'] . '_' . $exam['date']] = $score;
                }
                $total = collect($examScores)->sum();
                $average = count(array_filter($examScores)) > 0 ? $total / count(array_filter($examScores)) : 0;
            } elseif ($combineOption == 'average') {
                foreach ($examHeaders as $exam) {
                    $score = $subjectResults->where('symbolic_abbr', $exam['abbr'])
                        ->where('exam_date', $exam['date'])
                        ->first()->score ?? null;
                    $examScores[$exam['abbr'] . '_' . $exam['date']] = $score;
                }
                $filtered = collect($examScores)->filter();
                $total = 0;
                $average = $filtered->count() > 0 ? $filtered->avg() : 0;
            }

            $allScores = Examination_result::where('course_id', $subjectId)
                ->where('class_id', $classId)
                ->where('school_id', $schoolId)
                ->whereIn(DB::raw('DATE(exam_date)'), $examDates)
                ->get()
                ->groupBy('student_id')
                ->map(function ($scores) {
                    return $scores->sum('score'); // Daima tumia jumla kwa rank
                })
                ->sortDesc()
                ->values();

            $position = $allScores->search($total) + 1;

            $finalData[] = compact('subjectName', 'teacher', 'subjectCode', 'examScores', 'total', 'average', 'position');
        }

        $students = $results->first();
        $schoolInfo = $results->first();
        // return $schoolInfo;

        // =================== EXAM HEADERS WITH DATES ===================
        $examHeadersWithDates = $results
            ->mapWithKeys(function ($item) {
                return [$item->symbolic_abbr => $item->exam_date];
            })->unique()->toBase(); // toBase() to allow ->values()

        // =================== EXAM AVERAGE PER EXAM DATE ===================
        $examAverages = [];
        foreach ($examHeaders as $exam) {
            $totalPerExam = 0;
            $countPerExam = 0;
            $abbr = $exam['abbr'];
            $date = $exam['date'];

            foreach ($finalData as $subject) {
                $score = $subject['examScores'][$abbr . '_' . $date] ?? null;
                if (is_numeric($score)) {
                    $totalPerExam += $score;
                    $countPerExam++;
                }
            }

            $examAverages[$abbr . '_' . $date] = $countPerExam > 0 ? number_format($totalPerExam / $countPerExam, 2) : 0;
        }

        // =================== GENERAL AVERAGE ===================
        $sumOfAverages = array_sum($examAverages);
        $validScores = $results->filter(function ($item) {
            return !is_null($item->score);
        });

        $studentGeneralAverage = $validScores->count() > 0
            ? number_format($validScores->avg('score'), 2)
            : 0;

        $totalScoreForStudent = $validScores->sum('score');

        // =================== GENERAL POSITION (WITH TIE RANKING) ===================
        $studentId = $results->first()->student_id ?? null;

        // Pata alama za wanafunzi wote waliopo kwenye report hiyo
        $allStudentScores = Examination_result::where('class_id', $classId)
            ->where('school_id', $schoolId)
            ->whereIn(DB::raw('DATE(exam_date)'), $examDates)
            ->get()
            ->groupBy('student_id')
            ->map(function ($results) {
                return $results->sum('score');
            });

        // Pangilia kwa kushuka (descending)
        $sortedScores = $allStudentScores->sortDesc();

        // Tumia tie-aware ranking
        $ranked = [];
        $rank = 1;
        $position = 1;
        $previousScore = null;

        foreach ($sortedScores as $student_id => $score) {
            if ($previousScore !== null && $score < $previousScore) {
                $rank = $position;
            }
            $ranked[$student_id] = $rank;
            $previousScore = $score;
            $position++;
        }

        // Pata nafasi ya mwanafunzi anayehusika
        $generalPosition = $ranked[$studentId] ?? '-';
        $totalStudents = count($ranked);

        // =================== EXAM SPECIFICATIONS ===================
        $examSpecifications = $results
            ->map(function ($item) {
                return [
                    'abbr' => $item->symbolic_abbr,
                    'full_name' => $item->exam_type,
                    'date' => $item->exam_date
                ];
            })
            ->unique(function ($item) {
                return $item['abbr'] . $item['full_name'];
            })
            ->values()
            ->keyBy('abbr'); // We key by abbreviation for easy lookup

        //verify using qr code
        $verificationData = [
            'student_name' => trim($students->first_name . ' ' . $students->middle_name . ' ' . $students->last_name),
            'admission_number' => $students->admission_number,
            'class' => $students->class_name,
            'report_type' => $reports->title,
            'term' => $reports->term,
            'school' => $schoolInfo->school_name,
            'report_date' => $reports->created_at->format('Y-m-d'),
            'report_id' => $reports->id,
            'issued_at' => now()->timestamp,

            // ================== SUMMARY INFO ==================
            'total_score' => $totalScoreForStudent ?? 0,
            'average_score' => $studentGeneralAverage ?? 0,
            'student_rank' => $generalPosition ?? '-',
            'total_students' => $totalStudents ?? 0,
        ];

        $verificationData['signature'] = hash_hmac(
            'sha256',
            json_encode($verificationData),
            config('app.key')
        );


        $encryptedPayload = Crypt::encryptString(
            json_encode($verificationData)
        );

        $verificationUrl = route('report.verify', [
            'payload' => $encryptedPayload
        ]);


        $result = Builder::create()
            ->writer(new PngWriter())
            ->data($verificationUrl)
            ->size(150)
            ->margin(5)
            ->build();


        $qrPng = base64_encode($result->getString());

        $pdf = \PDF::loadView('generated_reports.compiled_report', compact(
            'finalData',
            'examHeaders',
            'studentGeneralAverage',
            'examHeadersWithDates',
            'results',
            'examAverages',
            'sumOfAverages',
            'year',
            'generalPosition',
            'totalStudents',
            'students',
            'reports',
            'schoolInfo',
            'examSpecifications',
            'school',
            'report',
            'class',
            'totalScoreForStudent',
            'qrPng'
        ));
        // return $pdf->stream('compiled_report.pdf'); // au ->download() kama unataka ipakuliwe
        $timestamp = Carbon::now()->timestamp;
        $fileName = "report_{$timestamp}.pdf"; // Filename format: student_result_<admission_number>_<timestamp>.pdf
        $folderPath = public_path('reports'); // Folder path in public directory

        // Make sure the directory exists
        if (!File::exists($folderPath)) {
            File::makeDirectory($folderPath, 0755, true);
        }

        // Save the PDF to the 'reports' folder
        $pdf->save($folderPath . '/' . $fileName);

        // Generate the URL for accessing the saved PDF
        $fileUrl = asset('reports/' . $fileName);

        return view('generated_reports.student_pdf_report', compact('fileUrl', 'year', 'reports', 'class', 'school', 'report', 'students', 'studentId', 'schoolId', 'classId', 'reportId'));
    }

    // function to delete compiled results*************************************************
    public function destroyReport($class, $year, $school, $reportId)
    {
        try {
            $school_id = Hashids::decode($school);
            $class_id = Hashids::decode($class);
            $report_id = Hashids::decode($reportId);

            // Validate decoded IDs
            if (empty($school_id[0]) || empty($class_id[0]) || empty($report_id[0])) {
                return redirect()
                    ->route('results.examTypesByClass', ['school' => $school, 'year' => $year, 'class' => $class])
                    ->with('error', 'Invalid parameters');
            }

            $report = generated_reports::find($report_id[0]);

            if (!$report) {
                return redirect()
                    ->route('results.examTypesByClass', ['school' => $school, 'year' => $year, 'class' => $class])
                    ->with('error', 'Report not found');
            }

            // Store IDs before deletion
            $school_id = $report->school_id;
            $class_id = $report->class_id;

            if ($report->status == 1) {
                return redirect()
                    ->route('results.examTypesByClass', ['school' => $school, 'year' => $year, 'class' => $class])
                    ->with('error', 'Cannot delete a published report');
            }

            $report->delete();

            return redirect()
                ->route('results.examTypesByClass', [
                    'school' => Hashids::encode($school_id),
                    'year' => $year,
                    'class' => Hashids::encode($class_id)
                ])
                ->with('success', 'Report deleted successfully');
        } catch (\Exception $e) {
            Log::error("Delete report error: " . $e->getMessage());
            return redirect()
                ->route('results.examTypesByClass', ['school' => $school, 'year' => $year, 'class' => $class])
                ->with('error', 'Error deleting report');
        }
    }

    public function verify(Request $request)
    {
        try {
            $payload = json_decode(
                Crypt::decryptString($request->payload),
                true
            );

            $signature = $payload['signature'];
            unset($payload['signature']);

            $expectedSignature = hash_hmac(
                'sha256',
                json_encode($payload),
                config('app.key')
            );

            if (!hash_equals($expectedSignature, $signature)) {
                return view('generated_reports.verification', [
                    'valid' => false,
                    'message' => 'Invalid or tampered report'
                ]);
            }

            return view('generated_reports.verification', [
                'valid' => true,
                'data' => $payload
            ]);
        } catch (\Exception $e) {
            return view('generated_reports.verification', [
                'valid' => false,
                'message' => 'Invalid or expired QR code'
            ]);
        }
    }

    public function rollbackResults(Request $request)
    {
        // dd($request->all());
        try {
            $request->validate([
                'teacher_id' => 'required',
                'course_id' => 'required',
                'school' => 'required',
                'class' => 'required',
                'examType' => 'required',
                'date' => 'required|date',
            ]);

            // Decode parameters from URL
            $school_id = Hashids::decode($request->school)[0];
            $class_id = Hashids::decode($request->class)[0];
            $exam_type_id = Hashids::decode($request->examType)[0];
            $teacher_id = Hashids::decode($request->teacher_id)[0];
            $course_id = $request->course_id === 'all' ? null : Hashids::decode($request->course_id)[0];
            $exam_date = $request->date;

            // Start building the query
            $query = Examination_result::where('school_id', $school_id)
                ->where('class_id', $class_id)
                ->where('exam_type_id', $exam_type_id)
                ->where('teacher_id', $teacher_id)
                ->whereDate('exam_date', $exam_date);

            // If not "all", filter by specific course
            if ($course_id) {
                $query->where('course_id', $course_id);
            }

            // Get the results to rollback
            $results = $query->get();

            if ($results->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No results found for the selected criteria'
                ]);
            }

            if ($results->contains('status', 2)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Results data set has been locked and cannot be rolled back 🔐'
                ]);
            }

            // Start database transaction
            DB::beginTransaction();

            foreach ($results as $result) {
                // Check if this result already exists in temporary_results
                $existingTemp = temporary_results::where([
                    'student_id' => $result->student_id,
                    'course_id' => $result->course_id,
                    'class_id' => $result->class_id,
                    'exam_type_id' => $result->exam_type_id,
                    'school_id' => $result->school_id,
                    'teacher_id' => $result->teacher_id,
                    'exam_date' => $result->exam_date,
                ])->first();

                if (!$existingTemp) {
                    // Create temporary result
                    temporary_results::create([
                        'student_id' => $result->student_id,
                        'course_id' => $result->course_id,
                        'class_id' => $result->class_id,
                        'teacher_id' => $result->teacher_id,
                        'exam_type_id' => $result->exam_type_id,
                        'school_id' => $result->school_id,
                        'score' => $result->score,
                        'exam_term' => $result->Exam_term,
                        'marking_style' => $result->marking_style,
                        'exam_date' => $result->exam_date,
                        'expiry_date' => now()->addHours(72)
                    ]);
                }

                // Delete from examination_results
                $result->delete();
            }

            DB::commit();
            $remainingResultsCount = Examination_result::where('school_id', $school_id)
                ->where('class_id', $class_id)
                ->where('exam_type_id', $exam_type_id)
                ->whereDate('exam_date', $exam_date)
                ->count();

            return response()->json([
                'success' => true,
                'message' => 'Successfully rolled back ' . $results->count() . ' records to temporary storage',
                'count' => $results->count(),
                'has_remaining_results' => $remainingResultsCount > 0
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Error rolling back results: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Clean SMS text to use only GSM 03.38 character set
     * This ensures SMS is counted as standard GSM (160 chars per segment)
     */
    private function cleanSmsText($text)
    {
        // Badilisha baadhi ya Unicode kuwa ASCII
        $replacements = [
            '–' => '-',   // en dash
            '—' => '-',   // em dash
            '“' => '"',
            '”' => '"',
            '‘' => "'",
            '’' => "'",
        ];

        $text = strtr($text, $replacements);

        // Ruhusu: A-Z, a-z, 0-9, space, newline na alama za kawaida za GSM
        $text = preg_replace('/[^A-Za-z0-9 @£$¥èéùìòÇ\nØø\rÅåΔ_ΦΓΛΩΠΨΣΘΞÆæßÉ!"#¤%&\'()*+,\-.\/:;<=>?¡ÄÖÑÜ§¿äöñüà]/u', '', $text);

        return $text;
    }
}
