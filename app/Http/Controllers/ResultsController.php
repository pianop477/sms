<?php

namespace App\Http\Controllers;

use App\Models\compiled_results;
use App\Models\Examination;
use App\Models\Examination_result;
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

        $students = Student::find($decoded[0]);
        if(! $students) {
            Alert()->toast('No such student was found', 'error');
            return back();
        }

        $user = Auth::user();
        $parent = Parents::where('user_id', $user->id)->first();
        $results = Examination_result::query()
                                    ->join('students', 'students.id', '=', 'examination_results.student_id')
                                    ->select('examination_results.*', 'students.parent_id')
                                    ->where('student_id', $students->id)
                                    ->where('students.parent_id', $parent->id)
                                    ->where('examination_results.school_id', $user->school_id)
                                    ->where('examination_results.class_id', $students->class_id)
                                    ->orderBy('examination_results.exam_date', 'DESC')
                                    ->get();

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

        $students = Student::find($decoded[0]);

        if(! $students ) {
            Alert()->toast('No such student was found in the attendance', 'error');
            return back();
        }
        $user = Auth::user();
        $parent = Parents::where('user_id', $user->id)->first();
         $examTypes = Examination_result::query()
                    ->join('examinations', 'examinations.id', '=', 'examination_results.exam_type_id')
                    ->join('students', 'students.id', '=', 'examination_results.student_id')
                    ->select('examinations.exam_type', 'examinations.id as exam_id', 'students.parent_id')
                    ->distinct()
                    ->whereYear('exam_date', $year)
                    ->where('student_id', $students->id)
                    ->where('examination_results.school_id', $user->school_id)
                    ->where('students.parent_id', $parent->id)
                    ->where('examination_results.class_id', $students->class_id)
                    ->orderBy('examinations.exam_type', 'asc')
                    ->paginate(10);

         return view('Results.result_type', compact('students', 'year', 'examTypes'));
     }

     public function resultByMonth($student, $year, $exam_type)
    {
        $student_id = Hashids::decode($student);
        $exam_id = Hashids::decode($exam_type);

        $students = Student::find($student_id[0]);
        $user = Auth::user();
        $parent = Parents::where('user_id', $user->id)->first();

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
            ->where('examination_results.class_id', $students->class_id)
            ->orderBy('exam_date') // Sorting ya moja kwa moja kabla ya grouping
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
                        'students.id as studentId', 'students.first_name', 'students.middle_name', 'students.last_name', 'students.admission_number',
                        'students.group', 'students.image', 'students.gender', 'students.admission_number', 'students.parent_id',
                        'subjects.course_name', 'subjects.course_code',
                        'grades.class_name', 'grades.class_code',
                        'examinations.exam_type',
                        'users.first_name as teacher_first_name', 'users.last_name as teacher_last_name',
                        'schools.school_name', 'schools.school_reg_no', 'schools.postal_address', 'schools.postal_name', 'schools.logo', 'schools.country',
                    )
                    ->where('examination_results.student_id', $studentId->id)
                    ->where('examination_results.exam_type_id', $exam_id[0])
                    ->where('examination_results.class_id', $studentId->class_id)
                    ->whereDate('exam_date', Carbon::parse($date)) // Filtering by date
                    ->where('examination_results.school_id', $user->school_id)
                    ->where('students.parent_id', $parent->id)
                    ->get();

        // Calculate the sum of all scores
        $totalScore = $results->sum('score');
        $averageScore = $totalScore / $results->count();

        // Calculate rankings
        $rankings = Examination_result::query()
                ->where('class_id', $studentId->class_id) // Angalia wanafunzi wa darasa hili pekee
                ->whereDate('exam_date', Carbon::parse($date)) // Angalia mtihani wa tarehe husika pekee
                ->join('students', 'students.id', '=', 'examination_results.student_id') // Kuunganisha na jedwali la wanafunzi
                ->select('student_id', DB::raw('SUM(score) as total_score'), 'students.first_name')
                ->groupBy('student_id', 'students.first_name')
                ->orderByDesc('total_score') // Pangilia kwa score kwanza
                ->orderBy('students.first_name') // Ikiwa score zinafanana, panga kwa jina
                ->get();

        $studentRank = $rankings->pluck('student_id')->search($studentId->id) + 1;


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
                ->where('class_id', $studentId->class_id) // Angalia wanafunzi wa darasa hili pekee
                ->whereDate('exam_date', Carbon::parse($date)) // Angalia mtihani wa tarehe husika pekee
                ->join('students', 'students.id', '=', 'examination_results.student_id') // Kuunganisha na students
                ->select('student_id', DB::raw('SUM(score) as total_score'), 'students.first_name')
                ->groupBy('student_id', 'students.first_name')
                ->orderByDesc('total_score') // Pangilia kwa score kwanza
                ->orderBy('students.first_name') // Ikiwa score zinafanana, panga kwa jina
                ->get();

            $result->courseRank = $courseRankings->pluck('student_id')->search($studentId->id) + 1;


        }

        // Generate the PDF
        $pdf = \PDF::loadView('Results.parent_results', compact('results', 'year', 'studentId', 'type', 'student', 'month', 'date', 'totalScore', 'averageScore', 'studentRank', 'rankings'));

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
        if($user->school_id != $schools->id){
            Alert()->toast('You are not authorized to view this page', 'error');
            return redirect()->route('error.page');
        }

        $results = Examination_result::query()
                                ->join('students', 'students.id', '=', 'examination_results.student_id')
                                ->join('grades', 'grades.id', '=', 'examination_results.class_id')
                                ->select(
                                    'examination_results.*',
                                    'grades.id as class_id', 'grades.class_name', 'grades.class_code'
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

        if($user->school_id != $schools->id){
            Alert()->toast('You are not authorized to view this page', 'error');
            return redirect()->route('error.page');
        }
        else {
            $results = Examination_result::query()
                        ->join('grades', 'grades.id', '=', 'examination_results.class_id')
                        ->select(
                            'examination_results.*',
                            'grades.id as class_id', 'grades.class_name', 'grades.class_code'
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

        if($user->school_id != $schools->id){
            Alert()->toast('You are not authorized to view this page', 'error');
            return redirect()->route('error.page');
        }

        else {
                $results = Examination_result::query()
                                    ->join('examinations', 'examinations.id', '=', 'examination_results.exam_type_id')
                                    ->select(
                                        'examination_results.*',
                                        'examinations.id as exam_type_id', 'examinations.exam_type'
                                    )
                                    ->where('examination_results.school_id', $schools->id)
                                    ->whereYear('examination_results.exam_date', $year)
                                    ->where('examination_results.class_id', $classes->id)
                                    ->get();

                $grades = Grade::find($class);

                $months = [
                    'January' => 1, 'February' => 2, 'March' => 3, 'April' => 4, 'May' => 5, 'June' => 6,
                    'July' => 7, 'August' => 8, 'September' => 9, 'October' => 10, 'November' => 11, 'December' => 12
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
                    return Carbon::parse($item->exam_date)->format('F');
                });

                //get compiled results
                $compiled_results = compiled_results::where('school_id', $schools->id)
                                                    ->where('class_id', $classes->id)
                                                    ->get();

                $groupedByExamType = $results->groupBy('exam_type_id'); // Group by exam type using results
                $compiledGroupByExam = $compiled_results->groupBy('report_name'); // Group by exam type using compiled results

                return view('Results.general_result_type', compact('schools', 'groupedByMonth', 'compiledGroupByExam', 'year', 'exams', 'grades', 'classes', 'groupedByExamType'));
        }
    }

    //send compiled results to the table compiled_results table
    public function saveCompiledResults(Request $request, school $school, $year, $class)
    {
        $selectedMonths = $request->input('months', []);
        $compiledTerm = $request->input('term');
        $examType = $request->input('exam_type');
        $customExamType = $request->input('custom_exam_type'); // Capture custom exam type
        $reportDate = $request->input('report_date'); // Capture report date

        // return $reportDate;

        if (empty($selectedMonths)) {
            Alert::error('Fail', 'No months selected');
            return back();
        }

        // If "Custom" is selected, use custom exam type instead
        if ($examType === 'custom' && !empty($customExamType)) {
            $examType = $customExamType;
        }

        $months = [
            'January' => 1, 'February' => 2, 'March' => 3, 'April' => 4, 'May' => 5, 'June' => 6,
            'July' => 7, 'August' => 8, 'September' => 9, 'October' => 10, 'November' => 11, 'December' => 12
        ];

        $monthsArray = array_map(fn($month) => $months[$month] ?? null, $selectedMonths);
        sort($monthsArray); // Ensure the array is sorted
        $sourceResults = json_encode($monthsArray);

        $results = DB::table('examination_results')
                    ->where('school_id', $school->id)
                    ->where('class_id', $class)
                    ->whereYear('exam_date', $year)
                    ->whereIn(DB::raw('MONTH(exam_date)'), $monthsArray)
                    ->select('student_id', 'course_id', 'score')
                    ->get()
                    ->groupBy(['student_id', 'course_id']);

        if ($results->isEmpty()) {
            Alert()->toast('No results found for the selected months', 'error');
            return back();
        }

        $duplicateFound = false;

        foreach ($results as $studentId => $courses) {
            foreach ($courses as $courseId => $studentResults) {
                $exists = compiled_results::where('student_id', $studentId)
                    ->where('class_id', $class)
                    ->where('school_id', $school->id)
                    ->where('course_id', $courseId)
                    ->where('compiled_term', $compiledTerm)
                    ->where('report_name', $examType)
                    ->whereDate('report_date', $reportDate) // Check if report_date exists
                    ->exists();

                if ($exists) {
                    $duplicateFound = true;
                    break 2; // Exit both loops
                }
            }
        }

        if ($duplicateFound) {
            Alert()->toast('Some results already exist in the database', 'error');
            return back();
        }

        // If no duplicates were found, proceed with saving results
        foreach ($results as $studentId => $courses) {
            foreach ($courses as $courseId => $studentResults) {
                compiled_results::create([
                    'student_id' => $studentId,
                    'class_id' => $class,
                    'school_id' => $school->id,
                    'course_id' => $courseId,
                    'report_name' => $examType,
                    'source_results' => $sourceResults,
                    'compiled_term' => $compiledTerm,
                    'total_score' => $studentResults->sum('score'),
                    'average_score' => $studentResults->avg('score'),
                    'report_date' => $reportDate, // Store the report date
                ]);
            }
        }

        Alert()->toast('Report saved successfully', 'success');
        return redirect()->route('fetch.report', ['class' => $class, 'year' => $year, 'school' => $school]);
    }

    //here function for displaying combined report results by exam type ************************************
    public function fetchReport (Request $request, $class, $year, school $school)
    {
        //declare variables
        $reportMonth = Carbon::parse($request->report_date)->format('m');
        $reportName = $request->input('exam_type');

        $reportTerm = $request->input('term');
        $termArray = ['i', 'ii'];
        // return $termArray;

        //fetch results
        $compiledResultsQuery = compiled_results::whereMonth('report_date', $reportMonth)
                                            ->where('report_name', $reportName)
                                            ->where('class_id', $class)
                                            ->where('school_id', $school->id)
                                            ->whereYear('report_date', $year)
                                            ->whereIn('status', [0,1]);

            // Apply term filter only if a specific term is selected
            if (!empty($reportTerm)) {
                $compiledResultsQuery->where('compiled_term', $reportTerm);
            } else {
                $compiledResultsQuery->whereIn('compiled_term', $termArray); // Return results for both terms if none selected
            }

            // Execute the query
            $combinedResults = $compiledResultsQuery->get();

        // return $combinedResults;
        $groupedReportName = $combinedResults->groupBy('report_name')->sortBy('report_name');

        return view('Results.combined_exam_type', compact('combinedResults', 'groupedReportName', 'year', 'school', 'class'));
    }
    // end of combine report display

    // function for displaying compiled results by month ***************************************
    public function compileResultByMonth ($class, $year, school $school, $exam)
    {
        $results = compiled_results::where('school_id', $school->id)
                                    ->where('class_id', $class)
                                    ->where('report_name', $exam)
                                    ->whereYear('report_date', $year)
                                    ->get();
        // return $results;
        $groupedByMonth = $results->groupBy(function ($item) {
            return Carbon::parse($item->report_date)->format('F');
        });

        return view('Results.combined_result_month', compact('school', 'year', 'class', 'exam', 'groupedByMonth', 'results'));
    }
    // end of compiled results by month

    // function to delete compiled results*************************************************
    public function deleteCombinedResults($class, $year, school $school, $exam, $month)
    {
        $monthNumber = Carbon::parse($month)->format('m');
        // return $monthNumber;
        $results = compiled_results::where('school_id', $school->id)
                                    ->where('class_id', $class)
                                    ->where('report_name', $exam)
                                    ->whereYear('report_date', $year)
                                    ->whereMonth('report_date', $monthNumber)
                                    ->delete();

        if($results){
            Alert()->toast('Results deleted successfully', 'success');
            return redirect()->route('results.byExamType', ['school' => $school, 'year' => $year, 'class' => $class,]);
        } else {
            Alert()->toast('No results found to delete.', 'error');
            return redirect()->back();
        }
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
            'students.first_name', 'students.middle_name', 'students.last_name',
            'students.id as student_id', 'students.admission_number',
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

    return view('Results.months_by_exam_type', compact('schools', 'results', 'year', 'class_id', 'exam_id', 'groupedByMonth'));
}


public function resultsByMonth($school, $year, $class, $examType, $month, $date)
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
        'January' => 1, 'February' => 2, 'March' => 3, 'April' => 4,
        'May' => 5, 'June' => 6, 'July' => 7, 'August' => 8, 'September' => 9,
        'October' => 10, 'November' => 11, 'December' => 12,
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
                    'students.first_name', 'students.middle_name', 'students.last_name', 'students.gender', 'students.id as student_id', 'students.group', 'students.admission_number',
                    'grades.class_name',
                    'examinations.exam_type',
                    'subjects.course_name', 'subjects.course_code'
                )
                ->where('examination_results.school_id', $schools->id)
                ->where('examination_results.class_id', $class_id[0])
                ->where('examination_results.exam_type_id', $exam_id[0])
                ->whereDate('examination_results.exam_date', $date)
                ->get();

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
    foreach ($sortedStudentsResults as $index => &$studentResult) {
        $studentResult['position'] = $index + 1;
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


    // Generate the PDF
    $pdf = \PDF::loadView('Results.results_by_month', compact(
        'school', 'year', 'class', 'examType', 'month', 'results', 'totalMaleStudents', 'totalFemaleStudents', 'totalMaleGrades', 'totalFemaleGrades',
        'averageScoresByCourse', 'evaluationScores', 'totalAverageScore', 'date', 'sortedStudentsResults', 'sumOfCourseAverages', 'sortedCourses',
        'totalUniqueStudents', 'subjectGradesByGender', 'courses'
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
    return view('Results.class_pdf_results', compact('fileUrl', 'fileName', 'results', 'date', 'schools', 'exam_id', 'class_id', 'month', 'year'));
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
            } elseif($score >= 0.5) {
                return 'E';
            } else { return 'ABS'; }

        } else {
            if ($score >= 80.5) {
                return 'A';
            } elseif ($score >= 60.5) {
                return 'B';
            } elseif ($score >= 40.5) {
                return 'C';
            } elseif ($score >= 20.5) {
                return 'D';
            } elseif($score >= 0.5) {
                return 'E';
            } else { return 'ABS'; }
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
            $updatedRows = Examination_result::where('school_id', $schools->id)
                                ->where('class_id', $class_id[0])
                                ->where('exam_type_id', $exam_id[0])
                                ->whereDate('exam_date', $date)
                                ->update(['status' => 2 ]);

            if ($updatedRows) {
                // If status is 2 (Published), send SMS notifications
                $studentResults = Examination_result::join('students', 'students.id', '=', 'examination_results.student_id')
                                ->join('subjects', 'subjects.id', 'examination_results.course_id')
                                ->join('examinations', 'examinations.id', '=', 'examination_results.exam_type_id')
                                ->join('schools', 'schools.id', '=', 'examination_results.school_id')
                                ->leftJoin('parents', 'parents.id', '=', 'students.parent_id')
                                ->leftJoin('users', 'users.id', '=', 'parents.user_id')
                                ->select(
                                    'examination_results.*', 'users.phone',
                                    'students.first_name', 'students.middle_name', 'students.last_name', 'students.status',
                                    'examinations.exam_type', 'subjects.course_name', 'subjects.course_code', 'schools.school_name'
                                )
                                ->where('examination_results.class_id', $class_id[0])
                                ->where('students.status', 1)
                                ->where('examination_results.school_id', $schools->id)
                                ->where('examination_results.exam_type_id', $exam_id[0])
                                ->whereDate('examination_results.exam_date', $date)
                                ->get();

                // Remove duplicate student entries
                $studentsData = $studentResults->unique('student_id')->values();

                // Calculate ranks based on total marks
                $studentsData = $studentsData->map(function ($student) use ($studentResults) {
                $courses = $studentResults->where('student_id', $student->student_id)
                    ->map(fn($result) => "{$result->course_code} - {$result->score}")
                    ->implode(', ');

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

                // Assign ranks
                $studentsData = $studentsData->map(fn($student, $index) => tap($student, fn($s) => $s->rank = $index + 1));

                // School URL
                $url = "https://shuleapp.tech";
                $beemSmsService = new BeemSmsService();

                // Find total number of students
                $totalStudents = $studentsData->count();

                // Loop through each student and prepare the payload for each parent
                foreach ($studentsData as $student) {
                $phoneNumber = $this->formatPhoneNumber($student->phone);
                if (!$phoneNumber) {
                    // Log::error("Invalid phone number for {$student->first_name}: {$student->phone}");
                    return response()->json(['success' => false, 'message' => "Invalid phone number for {$student->first_name}", 'type' => 'error']);
                }

                $messageContent = "Matokeo ya: " . strtoupper("{$student->first_name} {$student->last_name}"). ", ";
                $messageContent .= "Mtihani: " . strtoupper($student->exam_type) ." => " . Carbon::parse($date)->format('d/m/Y'). " ni:- ";
                $messageContent .= strtoupper("{$student->courses}") . ". ";
                $messageContent .= "Jumla: {$student->total_marks}, Wastani: " . number_format($student->average_marks) . ", Nafasi: {$student->rank} kati ya: {$totalStudents}. ";
                $messageContent .= "Zaidi tembelea: $url.";

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
                }

                Alert()->toast('Results published successfully', 'success');
                return back();
            }
        }
        catch (Exception $e) {
            Alert()->toast($e->getMessage(), 'error');
            return back();
        }
    }

    public function unpublishResult ($school, $year, $class, $examType, $month, $date)
    {
        try {

            $school_id = Hashids::decode($school);
            $exam_id = Hashids::decode($examType);
            $class_id = Hashids::decode($class);

            $user = Auth::user();

            $schools = school::find($school_id[0]);

            if($user->school_id != $schools->id){
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
                if($updatedRows){
                    Alert()->toast('Results unpublished successfully', 'success');
                    return back();
                } else {
                    Alert()->toast('No results found to unpublish.', 'error' );
                    return redirect()->back();
                }

            // return $monthsArray;
        }
        catch (\Exception $e) {
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

            if($user->school_id != $schools->id){
                Alert()->toast('You are not authorized to view this page', 'error');
                return redirect()->route('error.page');
            }

            $monthsArray = [
                'January' => 1, 'February' => 2, 'March' => 3, 'April' => 4,
                'May' => 5, 'June' => 6, 'July' => 7, 'August' => 8, 'September' => 9,
                'October' => 10, 'November' => 11, 'December' => 12,
            ];

            // return $monthsArray;
            if(array_key_exists($month, $monthsArray)){
                $monthNumber = $monthsArray[$month];
                // return $monthNumber;

                $results = Examination_result::where('school_id', $schools->id)
                                                // ->whereYear('exam_date', $year)
                                                ->where('class_id', $class_id[0])
                                                ->where('exam_type_id', $exam_id[0])
                                                // ->whereMonth('exam_date', $monthNumber)\
                                                ->whereDate('exam_date', $date)
                                                ->delete();
                if($results) {
                    Alert()->toast('Results has deleted successfully', 'success');
                    return redirect()->back();
                }

            } else {
                Alert()->toast('Invalid month name provided.', 'error' );
                return redirect()->back();
            }

        }
        catch(\Exception $e){
            Alert()->error($e->getMessage(), 'error');
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

        if($user->school_id != $schools->id){
            Alert()->toast('You are not authorized to view this page', 'error');
            return redirect()->route('error.page');
        }

         // Mwezi kwa namba
        $monthsArray = [
            'January' => 1, 'February' => 2, 'March' => 3, 'April' => 4,
            'May' => 5, 'June' => 6, 'July' => 7, 'August' => 8, 'September' => 9,
            'October' => 10, 'November' => 11, 'December' => 12,
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
                        'students.first_name', 'students.middle_name', 'students.last_name', 'students.admission_number',
                        'students.gender', 'grades.class_name', 'users.phone',
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
    public function deleteStudentResult($school, $year, $class, $examType, $month, $student_id, $date )
    {
        $school_id = Hashids::decode($school);
        $class_id = Hashids::decode($class);
        $exam_id = Hashids::decode($examType);
        $student = Hashids::decode($student_id);

        // dd($school_id, $class_id, $exam_id, $student);
        $user = Auth::user();

        $schools = school::find($school_id[0]);

        if($user->school_id != $schools->id){
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

        if($results){
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

        // return $student_id;
        $user = Auth::user();
        $schools = school::find($school_id[0]);

        if($user->school_id != $schools->id){
            Alert()->toast('You are not authorized to view this page', 'error');
            return redirect()->route('error.page');
        }

        $studentId = Student::findOrFail($student_id[0]);
        //
        $monthsArray = [
            'January' => 1, 'February' => 2, 'March' => 3, 'April' => 4, 'May' => 5, 'June' => 6,
            'July' => 7, 'August' => 8, 'September' => 9, 'October' => 10, 'November' => 11, 'December' => 12
        ];

        $monthValue = $monthsArray[$month];

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
                    'students.id as studentId', 'students.first_name', 'students.middle_name', 'students.last_name', 'students.admission_number',
                    'students.group', 'students.image', 'students.gender', 'students.admission_number',
                    'subjects.course_name', 'subjects.course_code',
                    'grades.class_name', 'grades.class_code',
                    'examinations.exam_type',
                    'users.first_name as teacher_first_name', 'users.last_name as teacher_last_name',
                    'schools.school_name', 'schools.school_reg_no', 'schools.postal_address', 'schools.postal_name', 'schools.logo', 'schools.country',
                )
                ->where('examination_results.student_id', $studentId->id)
                ->where('examination_results.exam_type_id', $exam_id[0])
                ->where('examination_results.class_id', $class_id[0])
                // ->whereYear('examination_results.exam_date', $year)
                // ->whereMonth('examination_results.exam_date', $monthValue)
                ->where('examination_results.school_id', $schools->id)
                ->where('examination_results.exam_date', $date)
                ->get();

        // return $results;
        // Calculate the sum of all scores
        $totalScore = $results->sum('score');

        // Calculate the average score
        $averageScore = $totalScore / $results->count();

        // Determine the student's overall rank based on their total score
        $rankings = Examination_result::query()
                ->where('class_id', $studentId->class_id) // Angalia wanafunzi wa darasa hili pekee
                ->whereDate('exam_date', Carbon::parse($date)) // Angalia mtihani wa tarehe husika pekee
                ->join('students', 'students.id', '=', 'examination_results.student_id') // Kuunganisha na jedwali la wanafunzi
                ->select('student_id', DB::raw('SUM(score) as total_score'), 'students.first_name')
                ->groupBy('student_id', 'students.first_name')
                ->orderByDesc('total_score') // Pangilia kwa score kwanza
                ->orderBy('students.first_name') // Ikiwa score zinafanana, panga kwa jina
                ->get();

        $studentRank = $rankings->pluck('student_id')->search($studentId->id) + 1;


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
                ->where('class_id', $studentId->class_id) // Angalia wanafunzi wa darasa hili pekee
                ->whereDate('exam_date', Carbon::parse($date)) // Angalia mtihani wa tarehe husika pekee
                ->join('students', 'students.id', '=', 'examination_results.student_id') // Kuunganisha na students
                ->select('student_id', DB::raw('SUM(score) as total_score'), 'students.first_name')
                ->groupBy('student_id', 'students.first_name')
                ->orderByDesc('total_score') // Pangilia kwa score kwanza
                ->orderBy('students.first_name') // Ikiwa score zinafanana, panga kwa jina
                ->get();

            $result->courseRank = $courseRankings->pluck('student_id')->search($studentId->id) + 1;


        }

        // Pass the calculated data to the view
        $pdf = \PDF::loadView('Results.parent_results', compact('results', 'year', 'date', 'examType', 'studentId', 'student', 'month', 'totalScore', 'averageScore', 'studentRank', 'rankings'));

        return $pdf->stream($results->first()->first_name .' Results '.$month. ' '. $year. '.pdf');
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

            if($user->school_id != $schools->id){
                Alert()->toast('You are not authorized to view this page', 'error');
                return redirect()->route('error.page');
            }

            // Fetch student information
            $studentInfo = Student::findOrFail($student[0]);

            // Map month names to their numeric values
            $monthsArray = [
                'January' => 1, 'February' => 2, 'March' => 3, 'April' => 4, 'May' => 5, 'June' => 6,
                'July' => 7, 'August' => 8, 'September' => 9, 'October' => 10, 'November' => 11, 'December' => 12
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
                            'students.first_name', 'students.middle_name', 'students.last_name', 'students.status',
                            'subjects.course_name', 'subjects.course_code',
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
                Alert()->toast('No results found for the selected student or Selected results are blocked', 'error');
                return redirect()->back();
            }

            // Calculate total score and average score
            $totalScore = $results->sum('score');
            $averageScore = $results->count() > 0 ? $totalScore / $results->count() : 0;

            // Determine the student's overall rank
            $rankings = Examination_result::query()
                ->where('class_id', $studentInfo->class_id) // Angalia wanafunzi wa darasa hili pekee
                ->whereDate('exam_date', Carbon::parse($date)) // Angalia mtihani wa tarehe husika pekee
                ->join('students', 'students.id', '=', 'examination_results.student_id') // Kuunganisha na jedwali la wanafunzi
                ->select('student_id', DB::raw('SUM(score) as total_score'), 'students.first_name')
                ->groupBy('student_id', 'students.first_name')
                ->orderByDesc('total_score') // Pangilia kwa score kwanza
                ->orderBy('students.first_name') // Ikiwa score zinafanana, panga kwa jina
                ->get();

            $studentRank = $rankings->search(function ($item) use ($studentInfo) {
                return $item->student_id === $studentInfo->id;
            }) + 1;

            // Prepare the message content
            $fullName = $studentInfo->first_name. ' '. $studentInfo->last_name;
            $examination = $results->first()->exam_type;
            $term = $results->first()->Exam_term;
            $schoolName = $results->first()->school_name;

            $courseScores = [];
            foreach ($results as $result) {
                $courseScores[] = "{$result->course_code} - {$result->score}";
            }

            $totalStudents = $rankings->count();
            $url = 'https://shuleapp.tech';
            $beemSmsService = new BeemSmsService();
            $messageContent = "Matokeo ya ". strtoupper($fullName ).", Mtihani: ". strtoupper($examination)." => ". Carbon::parse($date)->format('d/m/Y'). " ni:- ". implode(', ', array_map('strtoupper', $courseScores));
            $messageContent .= ". Jumla: $totalScore, Wastani: ". number_format($averageScore, 1) .", Nafasi: $studentRank kati ya: $totalStudents. Zaidi tembelea: $url";

            // Output the message content (or send it via SMS)
            // return $messageContent;

            // find the parent phone number
            $parent = Parents::where('id', $studentInfo->parent_id)->first();
            // return $parent;
            //find phone related to parent in users table
            $users = User::where('id', $parent->user_id)->first();
            // return $users->phone;

            //prepare send sms payload to send via Beem API *************************************
            $sourceAddr = $school->sender_id ?? 'shuleApp';
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
            $sender = $school->sender_id ?? "SHULE APP";
            $destination = $this->formatPhoneNumber($users->phone);
            $messageContent = "Matokeo ya ". strtoupper($fullName ).", Mtihani wa: ". strtoupper($examination)." => ". Carbon::parse($date)->format('d/m/Y'). ", ni:- ". implode(', ', array_map('strtoupper', $courseScores));
            $messageContent .= ". Jumla: $totalScore, Wastani: ". number_format($averageScore) .", Nafasi: $studentRank kati ya: $totalStudents. Zaidi tembelea: $url";
            $reference = uniqid();

            $payload = [
                'from' => $sender,
                'to' => $destination,
                'text' => $messageContent,
                'reference' => $reference
            ];

            $response = $nextSmsService->sendSmsByNext($payload['from'], $payload['to'], $payload['text'], $payload['reference']);
            // return $response;
            Alert()->toast('Message sent successfully', 'success');
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
            'score' => 'required|numeric',
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

    //edit saved results


}
