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
            ->whereDate('exam_date', Carbon::parse($date)) // Filtering by date
            ->where('examination_results.school_id', $user->school_id)
            ->where('examination_results.status', 2) // Assuming 2 is the status for published results
            ->where('students.parent_id', $parent->id)
            ->get();

        // Calculate the sum of all scores
        $totalScore = $results->sum('score');
        $averageScore = $totalScore / $results->count();

        // Calculate rankings
        $rankings = Examination_result::query()
            ->where('examination_results.class_id', $results->first()->class_id) // Angalia wanafunzi wa darasa hili pekee
            ->whereDate('exam_date', Carbon::parse($date)) // Angalia mtihani wa tarehe husika pekee
            ->where('examination_results.exam_type_id', $exam_id[0]) // Angalia aina ya mtihani
            ->where('examination_results.school_id', $user->school_id)
            ->join('students', 'students.id', '=', 'examination_results.student_id') // Kuunganisha na students
            ->select('student_id', DB::raw('SUM(score) as total_score'))
            ->groupBy('student_id')
            ->orderByDesc('total_score') // Pangilia kwa score kwanza
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
            if ($result->marking_style == 1) {
                if ($result->score >= 41) {
                    $result->grade = 'A';
                    $result->remarks = 'Excellent';
                } elseif ($result->score >= 31) {
                    $result->grade = 'B';
                    $result->remarks = 'Good';
                } elseif ($result->score >= 21) {
                    $result->grade = 'C';
                    $result->remarks = 'Pass';
                } elseif ($result->score >= 11) {
                    $result->grade = 'D';
                    $result->remarks = 'Poor';
                } else {
                    $result->grade = 'E';
                    $result->remarks = 'Fail';
                }
            } else {
                if ($result->score >= 81) {
                    $result->grade = 'A';
                    $result->remarks = 'Excellent';
                } elseif ($result->score >= 61) {
                    $result->grade = 'B';
                    $result->remarks = 'Good';
                } elseif ($result->score >= 41) {
                    $result->grade = 'C';
                    $result->remarks = 'Pass';
                } elseif ($result->score >= 21) {
                    $result->grade = 'D';
                    $result->remarks = 'Poor';
                } else {
                    $result->grade = 'E';
                    $result->remarks = 'Fail';
                }
            }

            $courseRankings = Examination_result::query()
                ->where('course_id', $result->course_id)
                ->where('examination_results.class_id', $studentId->class_id) // Angalia wanafunzi wa darasa hili pekee
                ->whereDate('exam_date', Carbon::parse($date)) // Angalia mtihani wa tarehe husika pekee
                ->where('examination_results.exam_type_id', $exam_id[0]) // Angalia aina ya mtihani
                ->where('examination_results.school_id', $user->school_id)
                ->join('students', 'students.id', '=', 'examination_results.student_id') // Kuunganisha na students
                ->select('student_id', DB::raw('SUM(score) as total_score'))
                ->groupBy('student_id')
                ->orderByDesc('total_score') // Pangilia kwa score kwanza
                ->get();

            // Hakikisha wanafunzi wenye score sawa wanashirikiana rank
            $rank = 1;
            $previousScore = null;
            $ranks = [];

            foreach ($courseRankings as $key => $ranking) {
                if ($previousScore !== null && $ranking->total_score < $previousScore) {
                    $rank = $key + 1;
                }
                $ranks[$ranking->student_id] = $rank;
                $previousScore = $ranking->total_score;
            }

            // Kupata rank ya mwanafunzi husika
            $result->courseRank = $ranks[$studentId->id] ?? null;
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
        ];

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
        $pdf = \PDF::loadView('Results.parent_results', compact('results', 'year', 'qrPng', 'studentId', 'type', 'student', 'month', 'date', 'totalScore', 'averageScore', 'studentRank', 'rankings'));

        // Generate filename using timestamp
        $timestamp = Carbon::now()->timestamp;
        $fileName = "student_result_{$studentId->admission_number}_{$timestamp}.pdf"; // Filename format: student_result_<admission_number>_<timestamp>.pdf
        $folderPath = public_path('reports'); // Folder path in public directory

        // Make sure the directory exists
        if (!File::exists($folderPath)) {
            File::makeDirectory($folderPath, 0755, true);
        }

        // Save the PDF to the 'reports' folder
        $pdf->save($folderPath . '/' . $fileName);

        // Generate the URL for accessing the saved PDF
        $fileUrl = asset('reports/' . $fileName);

        // Return the view with the file URL to be used in the iframe
        return view('Results.parent_academic_reports', compact('fileUrl', 'fileName', 'exam_id', 'results', 'year', 'studentId', 'type', 'student', 'month', 'date',));
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

    public function resultsByMonth($school, $year, $class, $examType, $month, $date, Request $request)
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
            // ->where('students.status', 1) // Only active students
            ->whereDate('examination_results.exam_date', $date)
            ->get();

        if ($results->isEmpty()) {
            Alert()->toast('No results found for this selection', 'info');
            return redirect()->route('results.general', ['school' => $school]);
        }
        // Filter students by class_id
        $studentsByClass = $results->where('class_id', $class_id[0])->groupBy('student_id');

        $totalMaleStudents = $studentsByClass->filter(fn($student) => $student->first()->gender === 'male')->count();
        $totalFemaleStudents = $studentsByClass->filter(fn($student) => $student->first()->gender === 'female')->count();

        // Average score per course with grade and course name
        $averageScoresByCourse = $results->groupBy('course_id')->map(function ($courseResults) {
            $averageScore = $courseResults->avg('score');
            return [
                'course_name' => $courseResults->first()->course_name,
                'course_code' => $courseResults->first()->course_code,
                'average_score' => $averageScore,
                'grade' => $this->calculateGrade($averageScore, $courseResults->first()->marking_style)
            ];
        });

        // Sum of all course averages
        $sumOfCourseAverages = $averageScoresByCourse->sum('average_score');

        // Sort courses by average score to determine position
        $sortedCourses = $averageScoresByCourse->sortByDesc('average_score')->values()->all();
        foreach ($sortedCourses as $index => &$course) {
            $course['position'] = $index + 1;
        }

        // Evaluation score table
        $evaluationScores = $results->groupBy('course_id')->map(function ($courseResults) {
            $grades = [
                'A' => 0,
                'B' => 0,
                'C' => 0,
                'D' => 0,
                'E' => 0,
                'ABS' => 0,
            ];

            foreach ($courseResults as $result) {
                $grade = $this->calculateGrade($result->score, $result->marking_style);
                $grades[$grade]++;
            }

            return $grades;
        });

        $courses = Subject::all(); // Assuming 'Subject' is your model for courses
        // Total average of all courses
        $totalAverageScore = $results->avg('score');

        // Student results with total marks, average, grade, and position
        $studentsResults = $results->groupBy('student_id')->map(function ($studentResults) {
            $totalMarks = $studentResults->sum('score');
            $average = $studentResults->avg('score');
            $grade = $average == 0 ? 'ABS' : $this->calculateGrade($average, $studentResults->first()->marking_style);

            return [
                'student_id' => $studentResults->first()->student_id,
                'admission_number' => $studentResults->first()->admission_number,
                'student_name' => $studentResults->first()->first_name . ' ' . $studentResults->first()->middle_name . ' ' . $studentResults->first()->last_name,
                'gender' => $studentResults->first()->gender,
                'courses' => $studentResults->map(function ($result) {
                    return [
                        'course_name' => $result->course_name,
                        'score' => $result->score,
                        'grade' => $this->calculateGrade($result->score, $result->marking_style),
                    ];
                }),
                'group' => $studentResults->first()->group,
                'total_marks' => $totalMarks,
                'average' => $average,
                'grade' => $grade,
            ];
        });

        // Sort students by total marks to determine position
        $sortedStudentsResults = $studentsResults->sortByDesc('total_marks')->values()->all();

        $lastTotal = null;
        $position = 0;
        $counter = 0;

        foreach ($sortedStudentsResults as $index => &$studentResult) {
            $counter++;

            if ($studentResult['total_marks'] !== $lastTotal) {
                $position = $counter;
                $lastTotal = $studentResult['total_marks'];
            }

            $studentResult['position'] = $position;
        }


        $totalUniqueStudents = $studentsByClass->count();

        // Count grades by gender based on overall student performance
        $gradesByGender = $studentsResults->groupBy('gender')->map(function ($group) {
            $grades = ['A' => 0, 'B' => 0, 'C' => 0, 'D' => 0, 'E' => 0, 'ABS' => 0, 'ABS' => 0];
            foreach ($group as $student) {
                $grades[$student['grade']]++; // Count the grade calculated for the student
            }
            return $grades;
        });

        // Separate counts for male and female grades
        $totalMaleGrades = $gradesByGender->get('male', ['A' => 0, 'B' => 0, 'C' => 0, 'D' => 0, 'E' => 0, 'ABS' => 0]);
        $totalFemaleGrades = $gradesByGender->get('female', ['A' => 0, 'B' => 0, 'C' => 0, 'D' => 0, 'E' => 0, 'ABS' => 0]);

        // Count unique students
        $totalUniqueStudents = $results->pluck('student_id')->unique()->count();

        //total subjects added in the results
        $totalSubjects = $results->pluck('course_id')->unique()->count();

        //class total average
        $generalClassAvg = $sumOfCourseAverages / $totalSubjects;
        // Count grades by subject and gender (A, B, C, D, E)
        $subjectGradesByGender = $results->groupBy('course_id')->map(function ($courseResults) {
            $grades = [
                'A' => ['male' => 0, 'female' => 0],
                'B' => ['male' => 0, 'female' => 0],
                'C' => ['male' => 0, 'female' => 0],
                'D' => ['male' => 0, 'female' => 0],
                'E' => ['male' => 0, 'female' => 0],
                'ABS' => ['male' => 0, 'female' => 0],
            ];

            foreach ($courseResults as $result) {
                $grade = $this->calculateGrade($result->score, $result->marking_style);

                // Increment the count for the respective grade and gender
                if ($result->gender == 'male') {
                    $grades[$grade]['male']++;
                } else {
                    $grades[$grade]['female']++;
                }
            }

            return $grades;
        });

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
                ),
                'examination results.xlsx'
            );
        }

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

        // Format for teachers dropdown - CORRECTED:
        $teachers = $teachersWithCourses->map(function ($teacherResults, $teacherId) {
            $firstRecord = $teacherResults->first();
            return [
                'id' => Hashids::encode($teacherId),
                'name' => $firstRecord->first_name . ' ' . $firstRecord->last_name, // REMOVED middle_name
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
        ));
        $pdf->setOption('isHtml5ParserEnabled', true);
        $pdf->setOption('isPhpEnabled', true);

        // Generate filename using timestamp
        $timestamp = Carbon::now()->timestamp;
        $fileName = "results_{$school}_{$year}_{$class}_{$examType}_{$month}_{$timestamp}.pdf"; // Use dynamic filename format
        $folderPath = public_path('reports'); // Folder path in public directory

        // Make sure the directory exists
        if (!File::exists($folderPath)) {
            File::makeDirectory($folderPath, 0755, true);
        }

        // Save the PDF to the 'reports' folder
        $pdf->save($folderPath . '/' . $fileName);

        // Generate the URL for accessing the saved PDF
        $fileUrl = asset('reports/' . $fileName);

        // Return the view with the file URL to be used in the iframe
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
            // 'availableCourses',
            'firstTeacherCourses'
        ));
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
            } elseif ($score >= 0.5) {
                return 'E';
            } else {
                return 'ABS';
            }
        } else {
            if ($score >= 80.5) {
                return 'A';
            } elseif ($score >= 60.5) {
                return 'B';
            } elseif ($score >= 40.5) {
                return 'C';
            } elseif ($score >= 20.5) {
                return 'D';
            } elseif ($score >= 0.5) {
                return 'E';
            } else {
                return 'ABS';
            }
        }
    }
    //end of results in general ==============================================

    //publishing results to be visible to parents and send sms via Beem api  ***************************************************
    public function publishResult(Request $request, $school, $year, $class, $examType, $month, $date)
    {
        try {

            $school_id = Hashids::decode($school);
            $class_id = Hashids::decode($class);
            $exam_id = Hashids::decode($examType);
            // dd($school_id, $class_id, $exam_id);

            $user = Auth::user();
            $schools = School::find($school_id[0]);

            if ($user->school_id != $schools->id) {
                return response()->json(['success' => false, 'message' => 'You are not authorized to perform this action.', 'type' => 'error']);
            }

            // Update status in the database
            $updatedRows = Examination_result::join('students', 'students.id', '=', 'examination_results.student_id')
                ->where('examination_results.school_id', $schools->id)
                ->where('examination_results.class_id', $class_id[0])
                ->where('examination_results.exam_type_id', $exam_id[0])
                ->where('students.status', 1) //only active students
                ->whereDate('examination_results.exam_date', $date)
                ->update(['examination_results.status' => 2]);

            if ($updatedRows) {
                // If status is 2 (Published), send SMS notifications
                $studentResults = Examination_result::join('students', 'students.id', '=', 'examination_results.student_id')
                    ->join('subjects', 'subjects.id', 'examination_results.course_id')
                    ->join('examinations', 'examinations.id', '=', 'examination_results.exam_type_id')
                    ->join('schools', 'schools.id', '=', 'examination_results.school_id')
                    ->leftJoin('parents', 'parents.id', '=', 'students.parent_id')
                    ->leftJoin('users', 'users.id', '=', 'parents.user_id')
                    ->select(
                        'examination_results.*',
                        'users.phone',
                        'students.first_name',
                        'students.middle_name',
                        'students.last_name',
                        'students.status',
                        'examinations.exam_type',
                        'subjects.course_name',
                        'subjects.course_code',
                        'schools.school_name'
                    )
                    ->where('examination_results.class_id', $class_id[0])
                    ->where('students.status', 1) //only active students
                    ->where('examination_results.school_id', $schools->id)
                    ->where('examination_results.exam_type_id', $exam_id[0])
                    ->where('examination_results.status', 2) // Published results
                    ->whereDate('examination_results.exam_date', $date)
                    ->get();

                // Remove duplicate student entries
                $studentsData = $studentResults->unique('student_id')->values();

                // Calculate ranks based on total marks
                $studentsData = $studentsData->map(function ($student) use ($studentResults) {
                    $courses = $studentResults->where('student_id', $student->student_id)
                        ->map(fn($result) => "{$result->course_code}={$result->score}")
                        ->implode("\n");

                    $totalMarks = $studentResults->where('student_id', $student->student_id)->sum('score');
                    $averageMarks = $totalMarks / $studentResults->where('student_id', $student->student_id)->count();

                    $student->courses = $courses;
                    $student->total_marks = $totalMarks;
                    $student->average_marks = $averageMarks;

                    return $student;
                });

                // Sort students by total marks in descending order
                $studentsData = $studentsData->sortByDesc('total_marks')->values();
                $term = $studentResults->first()->Exam_term;

                $rank = 1;
                $previousScore = null;
                $previousRank = null;

                $studentsData = $studentsData->map(function ($student, $index) use (&$rank, &$previousScore, &$previousRank) {
                    if ($previousScore !== null && $student->total_marks < $previousScore) {
                        $rank = $index + 1; // Rank inabadilika tu kama alama ni tofauti
                    }

                    // Kama alama ni sawa na ya mwanafunzi uliopita, tumia rank sawa
                    if ($previousScore !== null && $student->total_marks == $previousScore) {
                        $student->rank = $previousRank; // Wanafunzi wawili wapate rank sawa
                    } else {
                        $student->rank = $rank;
                        $previousRank = $rank;
                    }

                    $previousScore = $student->total_marks;
                    return $student;
                });

                // School URL
                $url = "https://shuleapp.tech";
                $beemSmsService = new BeemSmsService();

                // Find total number of students
                $totalStudents = $studentsData->count();

                // Loop through each student and prepare the payload for each parent
                foreach ($studentsData as $student) {
                    $phoneNumber = $this->formatPhoneNumber($student->phone);
                    $dateFormat = Carbon::parse($date)->format('d-m-Y');
                    $fullname = $student->first_name . ' ' . $student->last_name;
                    if (!$phoneNumber) {
                        // Log::error("Invalid phone number for {$student->first_name}: {$student->phone}");
                        return response()->json([
                            'success' => false,
                            'message' => "Invalid phone number for {$student->first_name}",
                            'type' => 'error'
                        ]);
                    }

                    // Construct the SMS message
                    $messageContent = "Matokeo ya " . strtoupper($fullname) . ", \n";
                    $messageContent .= "Mtihani wa " . strtoupper($student->exam_type) . ", wa Tar. {$dateFormat} ni: \n";
                    $messageContent .= strtoupper($student->courses) . "\n";
                    $messageContent .= "Jumla {$student->total_marks}, Wastani " . number_format($student->average_marks) . ", Nafasi ya {$student->rank} kati ya {$totalStudents}. \n";
                    $messageContent .= "Pakua ripoti hapa {$url} ";

                    // Prepare the recipients array
                    $recipients = [
                        [
                            'recipient_id' => $student->student_id, // Unique ID for each recipient
                            'dest_addr' => $phoneNumber, // Parent's phone number
                        ]
                    ];

                    // Send SMS to each parent individually using Beem API
                    $source_Addr = $schools->sender_id ?? 'shuleApp';
                    // $beemSmsService->sendSms($source_Addr, $messageContent, $recipients);

                    // Send using nextSMS API (option 2)
                    $nextSmsService = new NextSmsService();
                    $payload = [
                        'from' => $schools->sender_id ?? "SHULE APP",
                        'to' => $phoneNumber,
                        'text' => $messageContent,
                        'reference' => $student->student_id
                    ];
                    $response = $nextSmsService->sendSmsByNext(
                        $payload['from'],
                        $payload['to'],
                        $payload['text'],
                        $payload['reference']
                    );

                    // Log::info("NextSMS Payload: ". $payload['text']);

                    if (!$response['success']) {
                        Alert()->toast('SMS failed: ' . $response['error'], 'error');
                        return back();
                    }
                }

                // Log::info("Send SMS: ". $payload['text']);
                Alert()->toast('Results published and sent to parents successfully', 'success');
                return back();
            }
        } catch (Exception $e) {
            Alert()->toast($e->getMessage(), 'error');
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

        // First query (unchanged as it works correctly)
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
            )
            ->where('examination_results.student_id', $studentId->id)
            ->where('examination_results.exam_type_id', $exam_id[0])
            ->where('examination_results.class_id', $class_id[0])
            ->where('examination_results.school_id', $schools->id)
            ->where('examination_results.exam_date', $date)
            ->get();

        // Calculate scores
        $totalScore = $results->sum('score');
        $averageScore = $totalScore / $results->count();

        // Fixed ranking query
        $rankings = Examination_result::query()
            ->where('examination_results.class_id', $studentId->class_id) // Angalia wanafunzi wa darasa hili pekee
            ->whereDate('exam_date', Carbon::parse($date)) // Angalia mtihani wa tarehe husika pekee
            ->where('examination_results.exam_type_id', $exam_id[0]) // Angalia aina ya mtihani
            ->where('examination_results.school_id', $user->school_id)
            ->join('students', 'students.id', '=', 'examination_results.student_id') // Kuunganisha na students
            ->select('student_id', DB::raw('SUM(score) as total_score'))
            ->groupBy('student_id')
            ->orderByDesc('total_score') // Pangilia kwa score kwanza
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
            if ($result->marking_style == 1) {
                if ($result->score >= 41) {
                    $result->grade = 'A';
                    $result->remarks = 'Excellent';
                } elseif ($result->score >= 31) {
                    $result->grade = 'B';
                    $result->remarks = 'Good';
                } elseif ($result->score >= 21) {
                    $result->grade = 'C';
                    $result->remarks = 'Pass';
                } elseif ($result->score >= 11) {
                    $result->grade = 'D';
                    $result->remarks = 'Poor';
                } else {
                    $result->grade = 'E';
                    $result->remarks = 'Fail';
                }
            } else {
                if ($result->score >= 81) {
                    $result->grade = 'A';
                    $result->remarks = 'Excellent';
                } elseif ($result->score >= 61) {
                    $result->grade = 'B';
                    $result->remarks = 'Good';
                } elseif ($result->score >= 41) {
                    $result->grade = 'C';
                    $result->remarks = 'Pass';
                } elseif ($result->score >= 21) {
                    $result->grade = 'D';
                    $result->remarks = 'Poor';
                } else {
                    $result->grade = 'E';
                    $result->remarks = 'Fail';
                }
            }

            $courseRankings = Examination_result::query()
                ->where('course_id', $result->course_id)
                ->where('examination_results.class_id', $studentId->class_id) // Angalia wanafunzi wa darasa hili pekee
                ->whereDate('exam_date', Carbon::parse($date)) // Angalia mtihani wa tarehe husika pekee
                ->where('examination_results.exam_type_id', $exam_id[0]) // Angalia aina ya mtihani
                ->where('examination_results.school_id', $user->school_id)
                ->join('students', 'students.id', '=', 'examination_results.student_id') // Kuunganisha na students
                ->select('student_id', DB::raw('SUM(score) as total_score'))
                ->groupBy('student_id')
                ->orderByDesc('total_score') // Pangilia kwa score kwanza
                ->get();

            // Hakikisha wanafunzi wenye score sawa wanashirikiana rank
            $rank = 1;
            $previousScore = null;
            $ranks = [];

            foreach ($courseRankings as $key => $ranking) {
                if ($previousScore !== null && $ranking->total_score < $previousScore) {
                    $rank = $key + 1;
                }
                $ranks[$ranking->student_id] = $rank;
                $previousScore = $ranking->total_score;
            }

            // Kupata rank ya mwanafunzi husika
            $result->courseRank = $ranks[$studentId->id] ?? null;
        }
        // Generate QR payload with summary
        $verificationData = [
            'student_name' => trim($studentId->first_name . ' ' . $studentId->middle_name . ' ' . $studentId->last_name),
            'admission_number' => $studentId->admission_number,
            'class' => $results->first()->class_name,
            'report_type' => $results->first()->exam_type . ' Assessment',
            'term' => $results->first()->Exam_term,
            'school' => $results->first()->school_name,
            'report_date' => \Carbon\Carbon::parse($date)->format('Y-m-d'),
            'report_id' => sha1($studentId->id . $exam_id[0] . $class_id[0] . $date),
            'issued_at' => now()->timestamp,

            // 🔹 Add these summary fields
            'total_score' => $totalScore,
            'average_score' => round($averageScore, 2),
            'student_rank' => $studentRank,
            'total_students' => $rankings->count(),
        ];


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
        $pdf = \PDF::loadView('Results.parent_results', compact('results', 'year', 'date', 'examType', 'studentId', 'student', 'month', 'totalScore', 'averageScore', 'studentRank', 'rankings', 'qrPng'));

        return $pdf->stream($results->first()->first_name . ' Results ' . $month . ' ' . $year . '.pdf');
    }

    //Re-send sms results individually
    public function sendResultSms($school, $year, $class, $examType, $month, $student_id, $date)
    {
        try {

            $school_id = Hashids::decode($school);
            $class_id = Hashids::decode($class);
            $exam_id = Hashids::decode($examType);
            $student = Hashids::decode($student_id);

            // dd($school_id, $class_id, $student, $exam_id);

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
                // ->whereYear('examination_results.exam_date', $year)
                // ->whereMonth('examination_results.exam_date', $monthValue)
                ->where('examination_results.school_id', $schools->id)
                ->whereDate('examination_results.exam_date', $date)
                ->where('examination_results.status', 2)
                ->get();

            // Hakikisha kuwa kuna data kwenye $results
            if ($results->isEmpty()) {
                // Log::error("Hakuna matokeo yaliyopatikana kwa mwanafunzi: {$studentInfo->id}");
                Alert()->toast('This result data set is locked 🔐', 'error');
                return redirect()->back();
            }

            // Calculate total score and average score
            $totalScore = $results->sum('score');
            $averageScore = $results->count() > 0 ? $totalScore / $results->count() : 0;

            // Determine the student's overall rank
            $rankings = Examination_result::query()
                ->where('examination_results.class_id', $studentInfo->class_id) // Angalia wanafunzi wa darasa hili pekee
                ->whereDate('exam_date', Carbon::parse($date)) // Angalia mtihani wa tarehe husika pekee
                ->where('examination_results.exam_type_id', $exam_id[0])
                ->where('examination_results.school_id', $schools->id)
                ->join('students', 'students.id', '=', 'examination_results.student_id') // Kuunganisha na students
                ->select('student_id', DB::raw('SUM(score) as total_score'))
                ->groupBy('student_id')
                ->orderByDesc('total_score') // Pangilia kwa score kwanza
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
            $studentRank = $ranks[$studentInfo->id] ?? null;

            // Prepare the message content
            $fullName = $studentInfo->first_name . ' ' . $studentInfo->last_name;
            $examination = $results->first()->exam_type;
            $term = $results->first()->Exam_term;
            $schoolName = $results->first()->school_name;

            $courseScores = [];
            foreach ($results as $result) {
                $courseScores[] = "{$result->course_code}={$result->score} \n";
            }

            $totalStudents = $rankings->count();
            $url = 'https://shuleapp.tech';
            $dateFormat = Carbon::parse($date)->format('d-m-Y');

            // find the parent phone number
            $parent = Parents::where('id', $studentInfo->parent_id)->first();
            // return $parent;
            //find phone related to parent in users table
            $users = User::where('id', $parent->user_id)->first();
            // return $users->phone;

            //prepare send sms payload to send via Beem API *************************************
            $sourceAddr = $schools->sender_id ?? 'shuleApp';
            $recipient_id = 1;
            $phone = $this->formatPhoneNumber($users->phone);
            $recipients = [
                [
                    'recipient_id' => $recipient_id++,
                    'dest_addr' => $phone
                ],
            ];

            //send sms by Beem API
            // $response = $beemSmsService->sendSms($sourceAddr, $messageContent, $recipients);

            // send sms via NextSMS API ************************************************************
            $nextSmsService = new NextSmsService();
            $sender = $schools->sender_id ?? "SHULE APP";
            $destination = $this->formatPhoneNumber($users->phone);
            $messageContent = "Matokeo ya " . strtoupper($fullName) . ", Mtihani wa " . strtoupper($examination) . ",\n";
            $messageContent .= "wa Tar. {$dateFormat} ni: \n";
            $messageContent .= strtoupper(implode($courseScores));
            $messageContent .= "Jumla $totalScore, Wastani " . number_format($averageScore) . ", Nafasi $studentRank kati ya $totalStudents. Pakua ripoti hapa {$url}";
            $reference = uniqid();

            $payload = [
                'from' => $sender,
                'to' => $destination,
                'text' => $messageContent,
                'reference' => $reference
            ];

            // Output the message content (or send it via SMS)
            // Log::info("Sending sms to ". $messageContent);

            $response = $nextSmsService->sendSmsByNext($payload['from'], $payload['to'], $payload['text'], $payload['reference']);

            if (!$response['success']) {
                Alert()->toast('SMS failed: ' . $response['error'], 'error');
                return back();
            }
            // return $response;
            Alert()->toast('Results SMS has been Re-sent successfully', 'success');
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
        // return "hello";
        // dd($request->all());
        $request->validate([
            'exam_type' => 'required|string|max:255',
            'class_id' => 'required|integer',
            'exam_dates' => 'required|array',
            'combine_option' => 'required|in:sum,average,individual',
            'term' => 'required|string|in:i,ii',
        ]);

        $selectedDataSet = $request->input('exam_dates', []);
        $examType = $request->input('exam_type');
        $classId = $request->input('class_id');
        $customExamType = $request->input('custom_exam_type'); // Capture custom exam type
        $combineMode = $request->input('combine_option');
        $reportTerm = $request->input('term');

        if ($examType === 'custom' && !empty($customExamType)) {
            $examType = $customExamType;
        }

        //check for duplicates
        $alreadyExists = generated_reports::where('class_id', $classId)
            ->where('school_id', auth()->user()->school_id)
            ->where('title', $examType)
            ->exists();
        if ($alreadyExists) {
            Alert()->toast('This results data set already exists', 'error');
            return to_route('results.examTypesByClass', ['school' => $school, 'year' => $year, 'class' => $class]);
        }

        $report = generated_reports::create([
            'title' => $examType,
            'class_id' => $classId,
            'school_id' => Auth::user()->school_id,
            'exam_dates' => $selectedDataSet,
            'combine_option' => $combineMode,
            'created_by' => auth()->id(),
            'term' => $reportTerm,
        ]);

        // return redirect()->route('generated-reports.show', $report->id)->with('success', 'Report generated successfully.');
        Alert()->toast('Report generated successfully.', 'success');
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

        // Vigezo vya kuweka jumla za mwanafunzi - SASA HUNA
        $studentTotalMarks = 0;
        $subjectCount = 0;

        foreach ($classResultsGrouped as $subjectId => $subjectResults) {
            $subjectName = $subjectResults->first()->course_name;
            $subjectCode = $subjectResults->first()->course_code;

            $currentTeacher = DB::table('class_learning_courses')
                ->join('teachers', 'teachers.id', '=', 'class_learning_courses.teacher_id')
                ->join('users', 'users.id', '=', 'teachers.user_id')
                ->where('class_learning_courses.class_id', $classId)
                ->where('class_learning_courses.course_id', $subjectId)
                ->where('class_learning_courses.status', 1)
                ->select('users.first_name', 'users.last_name')
                ->first();

            $teacher = $currentTeacher
                ? $currentTeacher->first_name . '. ' . $currentTeacher->last_name[0]
                : $subjectResults->first()->teacher_first_name . '. ' . $subjectResults->first()->teacher_last_name[0];

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
                    ->where('examination_results.class_id', $classId)
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

        // Pata wanafunzi wote waliopo kwenye class
        $allStudents = DB::table('students')
            ->where('class_id', $classId)
            ->where('school_id', $schoolId)
            ->pluck('id');

        foreach ($allStudents as $stdId) {
            // Pata results za mwanafunzi huyu
            $studentResults = DB::table('examination_results')
                ->where('student_id', $stdId)
                ->where('class_id', $classId)
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
        $studentId = Hashids::decode($student)[0];
        $schoolId = Hashids::decode($school)[0];
        $classId = Hashids::decode($class)[0];
        $reportId = Hashids::decode($report)[0];

        $reports = generated_reports::findOrFail($reportId);

        if ($reports->status == 1) {
            $examDates = $reports->exam_dates;

            // STEP 1: Fetch all results for the student
            $results = Examination_result::query()
                ->join('students', 'students.id', '=', 'examination_results.student_id')
                ->join('grades', 'grades.id', '=', 'examination_results.class_id')
                ->join('subjects', 'subjects.id', '=', 'examination_results.course_id')
                ->join('examinations', 'examinations.id', '=', 'examination_results.exam_type_id')
                ->where('examination_results.student_id', $studentId)
                ->where('examination_results.class_id', $classId)
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

            // STEP 3: Calculate position WITH TIE RANKING (like publishCombinedReport)
            $allStudentsScores = Examination_result::where('class_id', $reports->class_id)
                ->where('school_id', $schoolId)
                ->whereIn(DB::raw('DATE(exam_date)'), $examDates)
                ->get()
                ->groupBy('student_id')
                ->map(function ($studentResults) {
                    return $studentResults->sum('score');
                })
                ->sortDesc();

            // Apply tie ranking logic
            $sortedStudents = $allStudentsScores->sortDesc()->values();

            $rank = 1;
            $previousScore = null;
            $studentsWithRank = collect();

            foreach ($sortedStudents as $index => $score) {
                if ($previousScore !== null && $score < $previousScore) {
                    $rank = $index + 1; // Only increase rank if score is different
                }

                $studentsWithRank->push([
                    'student_id' => $allStudentsScores->keys()[$index],
                    'score' => $score,
                    'rank' => $rank
                ]);

                $previousScore = $score;
            }

            // Find current student's position
            $studentRank = $studentsWithRank->firstWhere('student_id', $studentId)['rank'] ?? '-';
            $totalStudents = $sortedStudents->count();

            $position = $studentRank;

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

            // Build subject results part of the message
            $subjectResultsText = "";
            foreach ($subjectAverages as $subject) {
                $subjectResultsText .= strtoupper($subject['course_code']) . ": " . number_format($subject['average']) . "\n";
            }

            $message = "Matokeo ya {$studentName}\n"
                . "Mtihani wa " . strtoupper($reports->title) . "\n"
                . "wa Tar. {$reportDate} ni:\n"
                . $subjectResultsText
                . "Jumla: {$studentAverageTotal}, Wastani: " . number_format($studentAverage, 1) . "\n"
                . "Nafasi ya {$position} kati ya {$totalStudents}.\n"
                . "Pakua ripoti hapa {$link}.";

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

                // Log::info("Sending SMS to {$formattedPhone}: {$message}");
                Alert()->toast('Results SMS has been Re-sent successfully', 'success');
                return to_route('students.combined.report', [
                    'school' => $school,
                    'year' => $year,
                    'class' => $class,
                    'report' => $report
                ]);
            } catch (\Exception $e) {
                // Log::error("Failed to send SMS: " . $e->getMessage());
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

            // onyesha mwalimu wa sasa na kama hayupo onyesha mwalimu wa zamani
            $currentTeacher = DB::table('class_learning_courses')
                ->join('teachers', 'teachers.id', '=', 'class_learning_courses.teacher_id')
                ->join('users', 'users.id', '=', 'teachers.user_id')
                ->where('class_learning_courses.class_id', $classId)
                ->where('class_learning_courses.course_id', $subjectId)
                ->where('class_learning_courses.status', 1)
                ->select('users.first_name', 'users.last_name')
                ->first();

            $teacher = $currentTeacher
                ? $currentTeacher->first_name . '. ' . $currentTeacher->last_name[0]
                : $subjectResults->first()->teacher_first_name . '. ' . $subjectResults->first()->teacher_last_name[0];

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
                'average' => round($average, 2), // Rounded to 1 decimal place
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
                    ->where('examination_results.class_id', $classId)
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

        // Pata wanafunzi wote waliopo kwenye class
        $allStudents = DB::table('students')
            ->where('class_id', $classId)
            ->where('school_id', $schoolId)
            ->pluck('id');

        foreach ($allStudents as $stdId) {
            // Pata results za mwanafunzi huyu
            $studentResults = DB::table('examination_results')
                ->where('student_id', $stdId)
                ->where('class_id', $classId)
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

                $studentTotalAvg += round($subjectAvg, 1);
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
            ->keyBy('abbr'); // We key by abbreviation for easy lookup

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

            $report->update(['status' => $status]);

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
                ->where('examination_results.class_id', $classId)
                ->where('examination_results.school_id', $schoolId)
                ->whereIn(DB::raw('DATE(exam_date)'), $examDates)
                ->get();

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
                    $subjectResultsText .= strtoupper($subject['course_code']) . "=" . number_format($subject['average']) . "\n";
                }

                return [
                    'parent_id' => $student['parent_id'],
                    'student_name' => strtoupper($student['first_name'] . ' ' . $student['last_name']),
                    'position' => $positionText,
                    'total_score' => number_format($student['total_score'], 1),
                    'total_average' => number_format($student['total_average']), // 2 decimal places like PDF
                    'subject_results' => $subjectResultsText
                ];
            });

            // STEP 7: Send SMS to parents (updated message format)
            foreach ($parentsPayload as $payload) {
                $schoolInfo = school::find($schoolId);
                $link = "https://shuleapp.tech";
                $nextSmsService = new NextSmsService();
                $sender = $schoolInfo->sender_id ?? "SHULE APP";
                $parent = Parents::find($payload['parent_id']);
                $user = User::findOrFail($parent->user_id);
                $phoneNumber = $this->formatPhoneNumber($user->phone);
                $reportDate = Carbon::parse($report->created_at)->format('d-m-Y');

                $message = "Matokeo ya {$payload['student_name']}\n"
                    . "Mtihani wa " . strtoupper($report->title) . "\n"
                    . "wa Tar. {$reportDate} ni:\n"
                    . $payload['subject_results']
                    . "Jumla: {$payload['total_score']}\n"
                    . "Wastani: {$payload['total_average']}\n"
                    . "Nafasi ya {$payload['position']}.\n"
                    . "Pakua ripoti hapa {$link}";

                // Log::info("Sending SMS to {$phoneNumber}: {$message}");
                $response = $nextSmsService->sendSmsByNext($sender, $phoneNumber, $message, uniqid());

                if (!$response['success']) {
                    Alert()->toast('SMS failed: ' . $response['error'], 'error');
                    return back();
                }
            }

            Alert()->toast('Report has been published and sent to parents successfully!', 'success');
            return to_route('results.examTypesByClass', ['school' => $school, 'year' => $year, 'class' => $class]);
        } catch (Exception $e) {
            Alert()->toast($e->getMessage(), 'error');
            // return redirect()->back();
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
}
