<?php

namespace App\Http\Controllers;

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

         if($students->parent_id != $parent->id) {
            Alert()->toast('You are not authorized to access this page', 'error');
            return to_route('home');
        }

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

        $students = Student::findOrFail($decoded[0]);

        //make variables for compact logic

        if(! $students ) {
            Alert()->toast('No such student was found', 'error');
            return back();
        }
        $user = Auth::user();
        $parent = Parents::where('user_id', $user->id)->first();

        if($students->parent_id != $parent->id) {
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
                    ->where('examination_results.class_id', $students->class_id)
                    ->orderBy('examinations.exam_type', 'asc')
                    ->get();

        // Check for combined examination results
        $reports = generated_reports::where('school_id', $students->school_id)
                                    ->where('class_id', $students->class_id)
                                    ->where('status', 1)
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
                'label' => $report->title ?? 'Generated Report'
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

         if($students->parent_id != $parent->id) {
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
            ->where('examination_results.class_id', $students->class_id)
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
        // return $classes;

        if($user->school_id != $schools->id){
            Alert()->toast('You are not authorized to view this page', 'error');
            return redirect()->route('error.page');
        }

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
                                                ->orderBy('generated_reports.title')
                                                ->paginate(5);

                return view('Results.general_result_type', compact('schools', 'reports', 'groupedByMonth', 'compiledGroupByExam', 'year', 'exams', 'grades', 'classes', 'groupedByExamType'));
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


        // Generate the PDF
        $pdf = \PDF::loadView('Results.results_by_month', compact(
            'school', 'year', 'class', 'examType', 'month', 'results', 'totalMaleStudents', 'totalFemaleStudents', 'totalMaleGrades', 'totalFemaleGrades',
            'averageScoresByCourse', 'evaluationScores', 'totalAverageScore', 'date', 'sortedStudentsResults', 'sumOfCourseAverages', 'sortedCourses',
            'totalUniqueStudents', 'subjectGradesByGender', 'courses', 'generalClassAvg',
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
                    ->map(fn($result) => "{$result->course_code}={$result->score}")
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
                $fullname = $student->first_name . ', '. $student->last_name[0];
                if (!$phoneNumber) {
                    // Log::error("Invalid phone number for {$student->first_name}: {$student->phone}");
                    return response()->json([
                        'success' => false,
                        'message' => "Invalid phone number for {$student->first_name}",
                        'type' => 'error'
                    ]);
                }

                // Construct the SMS message
                $messageContent = "Matokeo ya ". strtoupper($fullname).", \n";
                $messageContent .= "Mtihani wa ". strtoupper($student->exam_type).", Tar. {$dateFormat} ni: \n";
                $messageContent .= "Jumla {$student->total_marks}, Wastani " . number_format($student->average_marks) . ", Nafasi ya {$student->rank} kati ya {$totalStudents}. \n";
                $messageContent .= "Tembelea {$url} kuona ripoti";

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

                // Log::info("Send SMS: ". $payload['text']);
                Alert()->toast('Results published and sent to parents successfully', 'success');
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

            $existInCompile = generated_reports::where('class_id', $class_id[0])
                                                ->whereJsonContains('exam_dates', $date)
                                                ->where('school_id', $school_id[0])
                                                ->exists();

            if ($existInCompile) {
                Alert()->toast('Results already exist in the compiled reports. Cannot delete.', 'error');
                return to_route('results.monthsByExamType',[$school, 'year' => $year, 'class' => $class, 'examType' => $examType]);
            }
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

        $user = Auth::user();
        $schools = school::find($school_id[0]);

        if($user->school_id != $schools->id){
            Alert()->toast('You are not authorized to view this page', 'error');
            return redirect()->route('error.page');
        }

        $studentId = Student::findOrFail($student_id[0]);

        $monthsArray = [
            'January' => 1, 'February' => 2, 'March' => 3, 'April' => 4, 'May' => 5, 'June' => 6,
            'July' => 7, 'August' => 8, 'September' => 9, 'October' => 10, 'November' => 11, 'December' => 12
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
                Alert()->toast('This data set is locked 🔐', 'error');
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
            $fullName = $studentInfo->first_name. ', '. $studentInfo->last_name[0];
            $examination = $results->first()->exam_type;
            $term = $results->first()->Exam_term;
            $schoolName = $results->first()->school_name;

            $courseScores = [];
            foreach ($results as $result) {
                $courseScores[] = "{$result->course_code}={$result->score}";
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
            $messageContent = "Matokeo ya ". strtoupper($fullName )." Mtihani wa ". strtoupper($examination).",\n";
            $messageContent .= "Tar. {$dateFormat} ni: \n";
            $messageContent .= "Jumla $totalScore, Wastani ". number_format($averageScore) .", Nafasi $studentRank kati ya $totalStudents. Tembelea {$url} kuona ripoti";
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
        if($alreadyExists) {
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
    public function studentGeneratedCombinedReport ($class, $year, $school, $report)
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
                            'students.first_name', 'students.middle_name', 'students.last_name', 'students.admission_number', 'students.gender', 'students.id as studentId',
                            'students.class_id as student_class_id',
                            'grades.class_name', 'grades.class_code', 'students.school_id as student_school_id',
                            'users.first_name as user_first_name', 'users.last_name as user_last_name', 'users.phone',
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

        return view('Results.combined_result_month', compact('reports', 'classes', 'class', 'report', 'allScores', 'myReportData', 'year', 'school',));

    }
    // end of compiled results by month

    //function for showing individual student report which is already compiled
    public function showStudentCompiledReport($school, $year, $class, $report, $student)
    {
        $studentId = Hashids::decode($student)[0];
        $schoolId = Hashids::decode($school)[0];
        $classId = Hashids::decode($class)[0];
        $reportId = Hashids::decode($report)[0];

        $reports = generated_reports::find($reportId);
        $examDates = $reports->exam_dates; // array

        // return $reports;

        $results = Examination_result::query()
            ->join('students', 'students.id', '=', 'examination_results.student_id')
            ->join('grades', 'grades.id', '=', 'examination_results.class_id')
            ->join('subjects', 'subjects.id', '=', 'examination_results.course_id')
            ->join('examinations', 'examinations.id', '=', 'examination_results.exam_type_id')
            ->join('schools', 'schools.id', '=', 'examination_results.school_id')
            ->join('teachers', 'teachers.id', '=', 'examination_results.teacher_id')
            ->leftJoin('users', 'users.id', '=', 'teachers.user_id')
            ->select(
                'students.id as studentId', 'students.first_name', 'students.middle_name', 'students.last_name', 'students.group', 'students.gender', 'students.image',
                'subjects.id as subjectId', 'subjects.course_name', 'subjects.course_code', 'students.admission_number',
                'grades.class_name', 'grades.class_code',
                'examination_results.*',
                'examinations.exam_type', 'examinations.symbolic_abbr',
                'schools.school_name', 'schools.school_reg_no', 'schools.postal_address', 'schools.postal_name', 'schools.logo', 'schools.country',
                'users.first_name as teacher_first_name', 'users.last_name as teacher_last_name'
            )
            ->where('examination_results.student_id', $studentId)
            ->where('examination_results.class_id', $classId)
            ->where('examination_results.school_id', $schoolId)
            ->whereIn(DB::raw('DATE(exam_date)'), $examDates)
            ->get();

        // return $results;

        $classResultsGrouped = $results->groupBy('subjectId');

        $examHeaders = $results
            ->pluck('symbolic_abbr', 'exam_type_id')
            ->unique()
            ->values();

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
                foreach ($examHeaders as $examTypeId => $abbr) {
                    $score = $subjectResults->where('symbolic_abbr', $abbr)->first()->score ?? null;
                    $examScores[$abbr] = $score;
                }
                $total = collect($examScores)->filter()->sum();
                $average = count(array_filter($examScores)) > 0 ? $total / count(array_filter($examScores)) : 0;

            } elseif ($combineOption == 'sum') {
                foreach ($examHeaders as $examTypeId => $abbr) {
                    $score = $subjectResults->where('symbolic_abbr', $abbr)->first()->score ?? 0;
                    $examScores[$abbr] = $score;
                }
                $total = collect($examScores)->sum();
                $average = count(array_filter($examScores)) > 0 ? $total / count(array_filter($examScores)) : 0;

            } elseif ($combineOption == 'average') {
                foreach ($examHeaders as $examTypeId => $abbr) {
                    $score = $subjectResults->where('symbolic_abbr', $abbr)->first()->score ?? null;
                    $examScores[$abbr] = $score;
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
                            ->map(function ($scores) use ($combineOption) {
                                if ($combineOption == 'sum') {
                                    return $scores->sum('score');
                                } elseif ($combineOption == 'average') {
                                    return $scores->avg('score');
                                } else {
                                    return $scores->sum('score');
                                }
                            })
                            ->sortDesc()
                            ->values();

            $position = $allScores->search($combineOption == 'average' ? $average : $total) + 1;

            $finalData[] = compact('subjectName', 'teacher', 'subjectCode', 'examScores', 'total', 'average', 'position');
        }

        $student = $results->first();
        $schoolInfo = $results->first();
        // return $schoolInfo;

        // =================== EXAM HEADERS WITH DATES ===================
        $examHeadersWithDates = $results
            ->mapWithKeys(function ($item) {
                return [$item->symbolic_abbr => $item->exam_date];
            })->unique()->toBase(); // toBase() to allow ->values()

        // =================== EXAM AVERAGE PER EXAM DATE ===================
        $examAverages = [];
        foreach ($examHeadersWithDates as $abbr => $date) {
            $totalPerExam = 0;
            $countPerExam = 0;

            foreach ($finalData as $subject) {
                $score = $subject['examScores'][$abbr] ?? null;
                if (is_numeric($score)) {
                    $totalPerExam += $score;
                    $countPerExam++;
                }
            }

            $examAverages[$abbr] = $countPerExam > 0 ? round($totalPerExam / $countPerExam, 1) : 0;
        }

        // =================== GENERAL AVERAGE ===================
        $sumOfAverages = array_sum($examAverages);
        $studentGeneralAverage = count($examAverages) > 0 ? round($sumOfAverages / count($examAverages), 1) : 0;

        // =================== GENERAL POSITION ===================
        $studentId = $results->first()->student_id ?? null;

        $studentTotalScores = Examination_result::where('class_id', $classId)
                            ->where('school_id', $schoolId)
                            ->whereIn(DB::raw('DATE(exam_date)'), $examDates)
                            ->get()
                            ->groupBy('student_id')
                            ->map(function ($studentResults) {
                                // Sum all scores for this student
                                return $studentResults->sum('score');
                            })->sortDesc();

        $allStudentIds = $studentTotalScores->keys()->values()->all(); // sorted student IDs

        $index = array_search($studentId, $allStudentIds);
        $generalPosition = $index !== false ? $index + 1 : '-';
        $totalStudents = count($allStudentIds);
        $totalScoreForStudent = $results->sum('score');

        return view('generated_reports.index', compact(
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
                'student', 'studentId',
                'reports',
                'schoolInfo',
                'school', 'report', 'class', 'studentTotalScores', 'totalScoreForStudent'
        ));
    }

    public function sendSmsForCombinedReport($school, $year, $class, $report, $student)
    {
        $studentId = Hashids::decode($student)[0];
        $schoolId = Hashids::decode($school)[0];
        $classId = Hashids::decode($class)[0];
        $reportId = Hashids::decode($report)[0];

        $reports = generated_reports::findOrFail($reportId);
        $examDates = $reports->exam_dates; // array of dates

        $results = Examination_result::query()
                        ->join('students', 'students.id', '=', 'examination_results.student_id')
                        ->join('grades', 'grades.id', '=', 'examination_results.class_id')
                        ->join('subjects', 'subjects.id', '=', 'examination_results.course_id')
                        ->join('examinations', 'examinations.id', '=', 'examination_results.exam_type_id')
                        ->join('schools', 'schools.id', '=', 'examination_results.school_id')
                        ->join('teachers', 'teachers.id', '=', 'examination_results.teacher_id')
                        ->leftJoin('users', 'users.id', '=', 'teachers.user_id')
                        ->select(
                            'students.id as studentId', 'students.first_name', 'students.middle_name', 'students.last_name', 'students.group', 'students.gender', 'students.image',
                            'students.parent_id', // for fetching parent
                            'subjects.id as subjectId', 'subjects.course_name', 'subjects.course_code', 'students.admission_number',
                            'grades.class_name', 'grades.class_code',
                            'examination_results.*',
                            'examinations.exam_type', 'examinations.symbolic_abbr',
                            'schools.school_name', 'schools.school_reg_no', 'schools.postal_address', 'schools.postal_name', 'schools.logo', 'schools.country',
                            'users.first_name as teacher_first_name', 'users.last_name as teacher_last_name'
                        )
                        ->where('examination_results.student_id', $studentId)
                        ->where('examination_results.class_id', $classId)
                        ->where('examination_results.school_id', $schoolId)
                        ->whereIn(DB::raw('DATE(exam_date)'), $examDates)
                        ->get();


        if ($results->isEmpty()) {
            Alert()->toast('No results found for this student.', 'error');
            return to_route('students.combined.report', ['school' => $school, 'year' => $year, 'class' => $class, 'report' => $report]);
        }

        $classResultsGrouped = $results->groupBy('subjectId');
        $examHeaders = $results->pluck('symbolic_abbr', 'exam_type_id')->unique()->values();

        $finalData = [];
        $combineOption = $reports->combine_option ?? 'individual';

        //find total score of a student
        $studentTotal = $results->sum('score');

        foreach ($classResultsGrouped as $subjectId => $subjectResults) {
            $subjectName = $subjectResults->first()->course_name;
            $subjectCode = $subjectResults->first()->course_code;
            $teacher = $subjectResults->first()->teacher_first_name . '. ' . $subjectResults->first()->teacher_last_name[0];

            $examScores = [];
            $total = 0;
            $average = 0;

            foreach ($examHeaders as $abbr) {
                $score = $subjectResults->where('symbolic_abbr', $abbr)->first()->score ?? null;
                $examScores[$abbr] = $score;
            }

            if ($combineOption === 'sum' || $combineOption === 'individual') {
                $total = collect($examScores)->filter()->sum();
            }

            if ($combineOption === 'average') {
                $filtered = collect($examScores)->filter();
                $average = $filtered->count() > 0 ? $filtered->avg() : 0;
            } else {
                $average = count(array_filter($examScores)) > 0 ? $total / count(array_filter($examScores)) : 0;
            }

            $allScores = Examination_result::where('course_id', $subjectId)
                            ->where('class_id', $classId)
                            ->where('school_id', $schoolId)
                            ->whereIn(DB::raw('DATE(exam_date)'), $examDates)
                            ->get()
                            ->groupBy('student_id')
                            ->map(fn($scores) => $combineOption === 'average' ? $scores->avg('score') : $scores->sum('score'))
                            ->sortDesc()
                            ->values();

            $position = $allScores->search($combineOption === 'average' ? $average : $total) + 1;

            $finalData[] = compact('subjectName', 'teacher', 'subjectCode', 'examScores', 'total', 'average', 'position');
        }

        $student = $results->first();
        $schoolInfo = $results->first();

        // ======= GENERAL AVERAGE ==========
        $examHeadersWithDates = $results->mapWithKeys(fn($item) => [$item->symbolic_abbr => $item->exam_date])->unique()->toBase();
        $examAverages = [];
        foreach ($examHeadersWithDates as $abbr => $date) {
            $scores = array_filter(array_column($finalData, 'examScores'));
            $subjectTotal = 0;
            $subjectCount = 0;
            foreach ($finalData as $subject) {
                $score = $subject['examScores'][$abbr] ?? null;
                if (is_numeric($score)) {
                    $subjectTotal += $score;
                    $subjectCount++;
                }
            }
            $examAverages[$abbr] = $subjectCount > 0 ? round($subjectTotal / $subjectCount, 1) : 0;
        }

        $sumOfAverages = array_sum($examAverages);
        $studentGeneralAverage = count($examAverages) > 0 ? round($sumOfAverages / count($examAverages), 1) : 0;

        // ======= GENERAL POSITION ==========
        $studentTotalScores = Examination_result::where('class_id', $classId)
                                        ->where('school_id', $schoolId)
                                        ->whereIn(DB::raw('DATE(exam_date)'), $examDates)
                                        ->get()
                                        ->groupBy('student_id')
                                        ->map(fn($results) => $results->sum('score'))
                                        ->sortDesc();

        // 1. Panga alama za kila mwanafunzi kwa mpangilio (DESC) na uhifadhi kama array
        $sortedScores = $studentTotalScores->values()->all();

        // 2. Chukua alama zisizorudiwa (unique scores) na zipange kubwa → ndogo
        $uniqueScores = array_values(array_unique($sortedScores));
        rsort($uniqueScores); // Sort high to low

        // 3. Tafuta nafasi ya mwanafunzi kwa kurejelea alama yake
        $studentScore = $studentTotalScores->get($studentId, 0);
        $generalPosition = $studentScore ? array_search($studentScore, $uniqueScores) + 1 : '-';

        // 4. Jumla ya wanafunzi (totalStudents)
        $totalStudents = $studentTotalScores->count();

        // ========== FETCH PHONE NUMBER ==========
        $parentId = $student->parent_id ?? null;
        $phoneNumber = null;

        if ($parentId) {
            $parent = Parents::query()->join('users', 'users.id', '=', 'parents.user_id')
                                ->where('parents.id', $parentId)
                                ->select('parents.*', 'users.phone')
                                ->first();

            $phoneNumber = $this->formatPhoneNumber($parent->phone);
        }

        if (!$phoneNumber) {
            // return response()->json(['message' => 'Phone number not found for parent.'], 404);
            Alert()->toast('Phone number not found for parent.', 'error');
            return to_route('students.combined.report', ['school' => $school, 'year' => $year, 'class' => $class, 'report' => $report]);
        }

        // ========== BUILD SMS MESSAGE ==========
        $studentName = $student->first_name . ', ' . $student->last_name[0];
        $studentGender = strtolower($student->gender) == 'male' ? 'He' : 'She';
        $className = $student->class_name;
        $schoolName = $schoolInfo->school_name;
        $reportName = $reports->title;
        $date = Carbon::parse($reports->created_at)->format('d-m-Y');
        $url = 'https://shuleapp.tech';

        $message = "Matokeo ya ". strtoupper($studentName)."\n";
        $message .= "Mtihani wa ". strtoupper($reportName)."\n";
        $message .= "Tar. {$date} ni: \n";
        $message .= "Jumla: {$studentTotal}, Wastani {$studentGeneralAverage}, Nafasi ya {$generalPosition} kati ya {$totalStudents}\n";
        $message .= "Tembelea {$url} kuona ripoti";

        // ========== SEND SMS ==========
        try {
            // Simulate SMS for now
            $nextSmsService = new NextSmsService();
            $payload = [
                'from' => $schoolInfo->sender_id ?? "SHULE APP",
                'to' => $phoneNumber,
                'text' => $message,
                'reference' => uniqid()
            ];

            $response = $nextSmsService->sendSmsByNext($payload['from'], $payload['to'], $payload['text'], $payload['reference']);

            // Log::info("SMS to $phoneNumber: $message");
            // return response()->json(['message' => 'SMS sent successfully.']);
            Alert()->toast('Results SMS has been Re-sent successfully', 'success');
            return to_route('students.combined.report', ['school'=>$school, 'year'=>$year, 'class'=> $class, 'report'=>$report]);

        } catch (\Exception $e) {
            Log::error("Failed to send SMS: " . $e->getMessage());
            // return response()->json(['message' => 'Failed to send SMS.'], 500);
            Alert()->toast('Failed to send SMS', 'error');
            return to_route('students.combined.report', ['school'=>$school, 'year'=>$year, 'class'=> $class, 'report'=>$report]);
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
                'students.id as studentId', 'students.first_name', 'students.middle_name', 'students.last_name', 'students.group', 'students.gender', 'students.image',
                'subjects.id as subjectId', 'subjects.course_name', 'subjects.course_code', 'students.admission_number',
                'grades.class_name', 'grades.class_code',
                'examination_results.*',
                'examinations.exam_type', 'examinations.symbolic_abbr',
                'schools.school_name', 'schools.school_reg_no', 'schools.postal_address', 'schools.postal_name', 'schools.logo', 'schools.country',
                'users.first_name as teacher_first_name', 'users.last_name as teacher_last_name'
            )
            ->where('examination_results.student_id', $studentId)
            ->where('examination_results.class_id', $classId)
            ->where('examination_results.school_id', $schoolId)
            ->whereIn(DB::raw('DATE(exam_date)'), $examDates)
            ->get();

        $classResultsGrouped = $results->groupBy('subjectId');

        $examHeaders = $results
            ->pluck('symbolic_abbr', 'exam_type_id')
            ->unique()
            ->values();

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
                foreach ($examHeaders as $examTypeId => $abbr) {
                    $score = $subjectResults->where('symbolic_abbr', $abbr)->first()->score ?? null;
                    $examScores[$abbr] = $score;
                }
                $total = collect($examScores)->filter()->sum();
                $average = count(array_filter($examScores)) > 0 ? $total / count(array_filter($examScores)) : 0;

            } elseif ($combineOption == 'sum') {
                foreach ($examHeaders as $examTypeId => $abbr) {
                    $score = $subjectResults->where('symbolic_abbr', $abbr)->first()->score ?? 0;
                    $examScores[$abbr] = $score;
                }
                $total = collect($examScores)->sum();
                $average = count(array_filter($examScores)) > 0 ? $total / count(array_filter($examScores)) : 0;

            } elseif ($combineOption == 'average') {
                foreach ($examHeaders as $examTypeId => $abbr) {
                    $score = $subjectResults->where('symbolic_abbr', $abbr)->first()->score ?? null;
                    $examScores[$abbr] = $score;
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
                            ->map(function ($scores) use ($combineOption) {
                                if ($combineOption == 'sum') {
                                    return $scores->sum('score');
                                } elseif ($combineOption == 'average') {
                                    return $scores->avg('score');
                                } else {
                                    return $scores->sum('score');
                                }
                            })
                            ->sortDesc()
                            ->values();

            $position = $allScores->search($combineOption == 'average' ? $average : $total) + 1;

            $finalData[] = compact('subjectName', 'teacher', 'subjectCode', 'examScores', 'total', 'average', 'position');
        }

        $students = $results->first();
        $schoolInfo = school::findOrFail($schoolId);

        // =================== EXAM HEADERS WITH DATES ===================
        $examHeadersWithDates = $results
            ->mapWithKeys(function ($item) {
                return [$item->symbolic_abbr => $item->exam_date];
            })->unique()->toBase(); // toBase() to allow ->values()

        // =================== EXAM AVERAGE PER EXAM DATE ===================
        $examAverages = [];
        foreach ($examHeadersWithDates as $abbr => $date) {
            $totalPerExam = 0;
            $countPerExam = 0;

            foreach ($finalData as $subject) {
                $score = $subject['examScores'][$abbr] ?? null;
                if (is_numeric($score)) {
                    $totalPerExam += $score;
                    $countPerExam++;
                }
            }

            $examAverages[$abbr] = $countPerExam > 0 ? round($totalPerExam / $countPerExam, 1) : 0;
        }

        // =================== GENERAL AVERAGE ===================
        $sumOfAverages = array_sum($examAverages);
        $studentGeneralAverage = count($examAverages) > 0 ? round($sumOfAverages / count($examAverages), 1) : 0;

        // =================== GENERAL POSITION ===================
        $studentId = $results->first()->student_id ?? null;

        $studentTotalScores = Examination_result::where('class_id', $classId)
                                        ->where('school_id', $schoolId)
                                        ->whereIn(DB::raw('DATE(exam_date)'), $examDates)
                                        ->get()
                                        ->groupBy('student_id')
                                        ->map(function ($studentResults) {
                                            return $studentResults->sum('score'); // Jumla ya alama za kila mwanafunzi
                                        });

        // 1. Panga alama kwa mpangilio wa kubwa hadi ndogo (DESC)
        $sortedScores = $studentTotalScores->sortDesc()->values()->all();

        // 2. Tafuta alama zisizorudiwa (unique scores) kwa mpangilio
        $uniqueScores = array_unique($sortedScores);
        rsort($uniqueScores); // Sort high to low

        // 3. Tafuta nafasi ya mwanafunzi kwa kurejelea alama zake
        $studentScore = $studentTotalScores->get($studentId, 0);
        $generalPosition = array_search($studentScore, $uniqueScores) + 1;

        // 4. Jumla ya wanafunzi (totalStudents)
        $totalStudents = count($studentTotalScores);

        $totalScoreForStudent = $results->sum('score');

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
            'school', 'report', 'class', 'studentTotalScores', 'totalScoreForStudent'
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
            $report->update(['status' => $status]);

            // STEP 2: Get exam dates from the report
            $examDates = $report->exam_dates; // Array of dates

            // STEP 3: Fetch all exam results for these dates
            $results = Examination_result::query()
                                ->join('students', 'students.id', '=', 'examination_results.student_id')
                                ->join('grades', 'grades.id', '=', 'examination_results.class_id')
                                ->join('subjects', 'subjects.id', '=', 'examination_results.course_id')
                                ->where('examination_results.class_id', $classId)
                                ->where('examination_results.school_id', $schoolId)
                                ->whereIn(DB::raw('DATE(exam_date)'), $examDates)
                                ->select(
                                    'students.id as student_id',
                                    'students.first_name',
                                    'students.middle_name',
                                    'students.last_name',
                                    'students.parent_id',
                                    'subjects.id as course_id',
                                    'subjects.course_name',
                                    'examination_results.score',
                                    'examination_results.exam_date'
                                )
                                ->get();

            // STEP 4: Group results by student and course, then calculate averages
            $studentsData = $results->groupBy('student_id')->map(function ($studentResults) {
                // Group scores by course_id and calculate average per subject
                $courseAverages = $studentResults->groupBy('course_id')->map(function ($scores) {
                    return $scores->avg('score'); // Avg score per course
                });

                $totalScore = $studentResults->sum('score'); // Total score for the student

                // Calculate total average (average of all subject averages)
                $totalAverage = $courseAverages->avg();

                return [
                    'student_id' => $studentResults->first()->student_id,
                    'first_name' => $studentResults->first()->first_name,
                    'middle_name' => $studentResults->first()->middle_name,
                    'last_name' => $studentResults->first()->last_name,
                    'parent_id' => $studentResults->first()->parent_id,
                    'course_averages' => $courseAverages,
                    'total_average' => $totalAverage,
                    'total_score' => $totalScore,
                ];
            });

            // STEP 5: Sort students by total_average (descending) and assign ranks
            $sortedStudents = $studentsData->sortByDesc('total_score')->values();

            $rank = 1;
            $previousScore = null;
            $previousRank = 1;

            $studentsWithRank = $sortedStudents->map(function ($student, $index) use (&$rank, &$previousScore, &$previousRank) {
                if ($previousScore !== null && $student['total_score'] < $previousScore) {
                    // Increase rank based on the index (position in sorted array)
                    $rank = $index + 1;
                }

                $student['rank'] = $rank;
                $previousScore = $student['total_score'];
                $previousRank = $rank;

                return $student;
            });

            // STEP 6: Prepare SMS payload for each parent
            $parentsPayload = $studentsWithRank->map(function ($student) use ($studentsWithRank) {
                $totalStudents = $studentsWithRank->count();
                $positionText = "{$student['rank']} kati ya {$totalStudents}";

                return [
                    'parent_id' => $student['parent_id'],
                    'student_name' => strtoupper($student['first_name'] . ', '. $student['last_name'][0]),
                    'position' => $positionText,
                    'total_average' => round($student['total_average'], 1),
                    'course_averages' => $student['course_averages'],
                    'total_score' => $student['total_score'],
                    'total_course_average' => $student['course_averages']->sum(),
                ];
            });

            // STEP 7: Send SMS to parents (Assuming you have SMS logic)
            foreach ($parentsPayload as $payload) {
                $schoolInfo = school::find($schoolId);
                $link = "https://shuleapp.tech";
                $nextSmsService = new NextSmsService();
                $sender = $schoolInfo->sender_id ?? "SHULE APP";
                $parent = Parents::find($payload['parent_id']);
                $user = User::findOrFail($parent->user_id); // Assuming relationship exists
                $phoneNumber = $this->formatPhoneNumber($user->phone);
                // return $user;
                $message = "Matokeo ya {$payload['student_name']}\n"
                    ."Mtihani wa ". strtoupper($report->title)."\n"
                    ."Tar. " .Carbon::parse($report->created_at)->format('d-m-Y'). " ni:\n"
                    . "Jumla: {$payload['total_course_average']}, Wastani {$payload['total_average']}\n"
                    . "Nafasi ya {$payload['position']}.\n"
                    . "Tembelea {$link} kuona ripoti.";

                // Send SMS (Example using a hypothetical SMS service)
                // Log::info("Sending SMS to {$user->phone}: $message");

                $response = $nextSmsService->sendSmsByNext($sender, $phoneNumber, $message, uniqid());
            }

            Alert()->toast('Report has been published and sent to parents successfully!', 'success');
            return to_route('results.examTypesByClass', ['school' => $school, 'year' => $year, 'class' => $class]);

        } catch (Exception $e) {
            Alert()->toast($e->getMessage(), 'error');
            return redirect()->back();
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
        }
        catch(Exception $e) {
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
                    ->orderBy('admission_number')
                    ->get();

        // 2. GET ALL SUBJECTS FOR THIS CLASS
        $subjects = Subject::whereHas('examination_results', function($query) use ($classId, $schoolId, $examDates) {
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
                        $roundedAverage = round($average);
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

            $overallAverage = $subjectCount > 0 ? round($totalScore / $subjectCount, 1) : 0;

            $studentData[] = [
                'student_id' => $student->id,
                'admission_number' => $student->admission_number,
                'gender' => $student->gender,
                'student_name' => $student->first_name . ' ' . ($student->middle_name ? $student->middle_name . ' ' : '') . $student->last_name,
                'subject_averages' => $studentSubjectAverages,
                'total' => round($totalScore, 1),
                'average' => $overallAverage,
                'grade' => $this->calculateGrade($overallAverage, $results->first()->marking_style),
            ];
        }

        // 5. CALCULATE STUDENT RANKS WITH TIE HANDLING
        $groupedByTotal = collect($studentData)->groupBy('total');
        $sortedGroups = $groupedByTotal->sortKeysDesc();
        $rank = 1;
        $rankedStudents = [];

        foreach ($sortedGroups as $totalScore => $studentsWithSameScore) {
            $count = count($studentsWithSameScore);
            foreach ($studentsWithSameScore as $student) {
                $student['rank'] = $rank;
                $rankedStudents[] = $student;
            }
            $rank += $count;
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
                $subjectAverage = round($subjectTotal / $studentCount, 1);
                $subjectAverages[$subject->course_code] = [
                    'average' => $subjectAverage,
                    'grade' => $this->calculateGrade($subjectAverage, $results->first()->marking_style)
                ];
                $overallTotalAverage += $subjectAverage;
                $subjectAveragesSum += $subjectAverage; // Ongeza wastani wa kila somo kwenye jumla
                $subjectCount++;
            }
        }

        $overallTotalAverage = $subjectCount > 0 ? round($overallTotalAverage / $subjectCount, 1) : 0;
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

            $subjectAverage = count($subjectAverages) > 0 ? round(array_sum($subjectAverages) / count($subjectAverages), 2) : 0;

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
    public function parentDownloadStudentCombinedReport($school, $year, $class, $report, $student)
    {
        $studentId = Hashids::decode($student)[0];
        // return $studentId;
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
                        'students.id as studentId', 'students.first_name', 'students.middle_name', 'students.last_name', 'students.group', 'students.gender', 'students.image',
                        'subjects.id as subjectId', 'subjects.course_name', 'subjects.course_code', 'students.admission_number',
                        'grades.class_name', 'grades.class_code',
                        'examination_results.*',
                        'examinations.exam_type', 'examinations.symbolic_abbr',
                        'schools.school_name', 'schools.school_reg_no', 'schools.postal_address', 'schools.postal_name', 'schools.logo', 'schools.country',
                        'users.first_name as teacher_first_name', 'users.last_name as teacher_last_name'
                    )
                    ->where('examination_results.student_id', $studentId)
                    ->where('examination_results.class_id', $classId)
                    ->where('examination_results.school_id', $schoolId)
                    ->whereIn(DB::raw('DATE(exam_date)'), $examDates)
                    ->get();

        $classResultsGrouped = $results->groupBy('subjectId');

        $examHeaders = $results
            ->pluck('symbolic_abbr', 'exam_type_id')
            ->unique()
            ->values();

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
                foreach ($examHeaders as $examTypeId => $abbr) {
                    $score = $subjectResults->where('symbolic_abbr', $abbr)->first()->score ?? null;
                    $examScores[$abbr] = $score;
                }
                $total = collect($examScores)->filter()->sum();
                $average = count(array_filter($examScores)) > 0 ? $total / count(array_filter($examScores)) : 0;

            } elseif ($combineOption == 'sum') {
                foreach ($examHeaders as $examTypeId => $abbr) {
                    $score = $subjectResults->where('symbolic_abbr', $abbr)->first()->score ?? 0;
                    $examScores[$abbr] = $score;
                }
                $total = collect($examScores)->sum();
                $average = count(array_filter($examScores)) > 0 ? $total / count(array_filter($examScores)) : 0;

            } elseif ($combineOption == 'average') {
                foreach ($examHeaders as $examTypeId => $abbr) {
                    $score = $subjectResults->where('symbolic_abbr', $abbr)->first()->score ?? null;
                    $examScores[$abbr] = $score;
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
                            ->map(function ($scores) use ($combineOption) {
                                if ($combineOption == 'sum') {
                                    return $scores->sum('score');
                                } elseif ($combineOption == 'average') {
                                    return $scores->avg('score');
                                } else {
                                    return $scores->sum('score');
                                }
                            })
                            ->sortDesc()
                            ->values();

            $position = $allScores->search($combineOption == 'average' ? $average : $total) + 1;

            $finalData[] = compact('subjectName', 'teacher', 'subjectCode', 'examScores', 'total', 'average', 'position');
        }

        $students = $results->first();
        $schoolInfo = $results->first();

        // =================== EXAM HEADERS WITH DATES ===================
        $examHeadersWithDates = $results
            ->mapWithKeys(function ($item) {
                return [$item->symbolic_abbr => $item->exam_date];
            })->unique()->toBase(); // toBase() to allow ->values()

        // =================== EXAM AVERAGE PER EXAM DATE ===================
        $examAverages = [];
        foreach ($examHeadersWithDates as $abbr => $date) {
            $totalPerExam = 0;
            $countPerExam = 0;

            foreach ($finalData as $subject) {
                $score = $subject['examScores'][$abbr] ?? null;
                if (is_numeric($score)) {
                    $totalPerExam += $score;
                    $countPerExam++;
                }
            }

            $examAverages[$abbr] = $countPerExam > 0 ? round($totalPerExam / $countPerExam, 1) : 0;
        }

        // =================== GENERAL AVERAGE ===================
        $sumOfAverages = array_sum($examAverages);
        $studentGeneralAverage = count($examAverages) > 0 ? round($sumOfAverages / count($examAverages), 1) : 0;

        // =================== GENERAL POSITION ===================
        $studentId = $results->first()->student_id ?? null;

        $studentTotalScores = Examination_result::where('class_id', $classId)
                                        ->where('school_id', $schoolId)
                                        ->whereIn(DB::raw('DATE(exam_date)'), $examDates)
                                        ->get()
                                        ->groupBy('student_id')
                                        ->map(function ($studentResults) {
                                            return $studentResults->sum('score'); // Jumla ya alama za kila mwanafunzi
                                        });

        // 1. Panga alama kwa mpangilio wa kubwa hadi ndogo (DESC)
        $sortedScores = $studentTotalScores->sortDesc()->values()->all();

        // 2. Tafuta alama zisizorudiwa (unique scores) kwa mpangilio
        $uniqueScores = array_unique($sortedScores);
        rsort($uniqueScores); // Sort high to low

        // 3. Tafuta nafasi ya mwanafunzi kwa kurejelea alama zake
        $studentScore = $studentTotalScores->get($studentId, 0);
        $generalPosition = array_search($studentScore, $uniqueScores) + 1;

        // 4. Jumla ya wanafunzi (totalStudents)
        $totalStudents = count($studentTotalScores);

        $totalScoreForStudent = $results->sum('score');

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
            'school', 'report', 'class', 'studentTotalScores', 'totalScoreForStudent'
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
        $school_id = Hashids::decode($school);
        $class_id = Hashids::decode($class);
        $report_id = Hashids::decode($reportId);

        $report = generated_reports::find($report_id[0]);
        // delete the report
        if ($report) {
            $report->delete();
            Alert()->toast('Report deleted successfully.', 'success');
            return to_route('results.examTypesByClass', ['school' => $school, 'year' => $year, 'class' => $class]);
        } else {
            Alert()->toast('Report not found.', 'error');
            return to_route('results.examTypesByClass', ['school' => $school, 'year' => $year, 'class' => $class]);
        }
    }

}
