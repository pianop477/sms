<?php

namespace App\Http\Controllers;

use App\Models\compiled_results;
use App\Models\Examination;
use App\Models\Examination_result;
use App\Models\Grade;
use App\Models\Parents;
use App\Models\school;
use App\Models\Student;
use App\Models\User;
use App\Services\BeemSmsService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RealRashid\SweetAlert\Facades\Alert as FacadesAlert;
use RealRashid\SweetAlert\Facades\Alert;

class ResultsController extends Controller
{
    protected $beemSmsService;

    public function __construct(BeemSmsService $beemSmsService)
    {
        $this->beemSmsService = $beemSmsService;
    }

    public function index(Student $student)
    {
        $user = Auth::user();
        $results = Examination_result::where('student_id', $student->id)->where('school_id', $user->school_id)->orderBy('exam_date', 'DESC')->get();

        $groupedData = $results->groupBy(function ($item) {
            return Carbon::parse($item->exam_date)->format('Y');
        })->map(function ($yearGroup) {
            return $yearGroup->groupBy('exam_type');
        });

        return view('Results.parent_grouped_results', compact('groupedData', 'student'));
    }

    /**
     * Show the form for creating the resource.
     */


     public function resultByType(Student $student, $year)
     {
        $user = Auth::user();
         $examTypes = Examination_result::query()
             ->join('examinations', 'examinations.id', '=', 'examination_results.exam_type_id')
             ->select('examinations.exam_type', 'examinations.id as exam_id')
             ->distinct()
             ->whereYear('exam_date', $year)
             ->where('student_id', $student->id)
             ->where('examination_results.school_id', $user->school_id)
             ->orderBy('examinations.exam_type', 'asc')
             ->paginate(10);

         return view('Results.result_type', compact('student', 'year', 'examTypes'));
     }

     public function resultByMonth(Student $student, $year, $exam_type)
    {
        $user = Auth::user();
        $months = Examination_result::query()
            ->selectRaw('MONTH(exam_date) as month')
            ->distinct()
            ->whereYear('exam_date', $year)
            ->where('exam_type_id', $exam_type)
            ->where('student_id', $student->id)
            ->where('status', 2)
            ->where('school_id', $user->school_id)
            ->orderBy('month')
            ->get();

            $examType = Examination::find($exam_type);

        return view('Results.result_months', compact('student', 'year', 'examType', 'months'));
    }


    /**
     * Store the newly created resource in storage.
     */
    public function viewStudentResult($student, $year, $type, $month)
    {
        // Retrieve examination results for the specific student
        $studentId = Student::findOrFail($student);
        // return $studentId;
        $user = Auth::user();
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
                'students.group', 'students.image', 'students.gender', 'students.admission_number',
                'subjects.course_name', 'subjects.course_code',
                'grades.class_name', 'grades.class_code',
                'examinations.exam_type',
                'users.first_name as teacher_first_name', 'users.last_name as teacher_last_name',
                'schools.school_name', 'schools.school_reg_no', 'schools.postal_address', 'schools.postal_name', 'schools.logo', 'schools.country',
            )
            ->where('examination_results.student_id', $studentId->id)
            ->where('examination_results.exam_type_id', $type)
            ->whereYear('examination_results.exam_date', $year)
            ->whereMonth('examination_results.exam_date', $month)
            ->where('examination_results.school_id', $user->school_id)
            ->get();

        // Calculate the sum of all scores
        $totalScore = $results->sum('score');

        // Calculate the average score
        $averageScore = $totalScore / $results->count();

        // Determine the student's overall rank based on their total score
        $rankings = Examination_result::query()
            ->select('student_id', DB::raw('SUM(score) as total_score'))
            ->groupBy('student_id')
            ->orderBy('total_score', 'desc')
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
                    $result->remarks = 'Very Good';
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
                    $result->remarks = 'Very Good';
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

            // Determine the rank for the student in each course
            $courseRankings = Examination_result::query()
                ->where('course_id', $result->course_id)
                ->select('student_id', DB::raw('SUM(score) as total_score'))
                ->groupBy('student_id')
                ->orderBy('total_score', 'desc')
                ->get();

            $result->courseRank = $courseRankings->pluck('student_id')->search($studentId->id) + 1;
        }

        // Pass the calculated data to the view
        $pdf = \PDF::loadView('Results.parent_results', compact('results', 'year', 'studentId', 'type', 'student', 'month', 'totalScore', 'averageScore', 'studentRank', 'rankings'));

        return $pdf->stream($results->first()->first_name .' Results '.$month. ' '. $year. '.pdf');
    }

    //general results are intialized here ====================================
    public function general(school $school)
    {
        $results = Examination_result::query()
            ->join('students', 'students.id', '=', 'examination_results.student_id')
            ->join('grades', 'grades.id', '=', 'examination_results.class_id')
            ->select(
                'examination_results.*',
                'grades.id as class_id', 'grades.class_name', 'grades.class_code'
            )
            ->where('examination_results.school_id', $school->id)
            ->orderBy('examination_results.exam_date', 'DESC')
            ->get();

        $groupedData = $results->groupBy(function ($item) {
            return Carbon::parse($item->exam_date)->format('Y');
        })->map(function ($yearGroup) {
            return $yearGroup->groupBy('class_id');
        });

        return view('Results.general_year_result', compact('school', 'results', 'groupedData'));
    }

    public function classesByYear(school $school, $year)
    {
        $results = Examination_result::query()
                    ->join('grades', 'grades.id', '=', 'examination_results.class_id')
                    ->select(
                        'examination_results.*',
                        'grades.id as class_id', 'grades.class_name', 'grades.class_code'
                    )
                    ->where('examination_results.school_id', $school->id)
                    ->whereYear('examination_results.exam_date', $year)
                    ->orderBy('grades.class_code', 'ASC')
                    ->get();

        $groupedByClass = $results->groupBy('class_id');

        return view('Results.results_grouped_byYear', compact('school', 'year', 'groupedByClass'));
    }

    public function examTypesByClass(school $school, $year, $class)
    {
        $results = Examination_result::query()
            ->join('examinations', 'examinations.id', '=', 'examination_results.exam_type_id')
            ->select(
                'examination_results.*',
                'examinations.id as exam_type_id', 'examinations.exam_type'
            )
            ->where('examination_results.school_id', $school->id)
            ->whereYear('examination_results.exam_date', $year)
            ->where('examination_results.class_id', $class)
            ->get();
        $grades = Grade::find($class);

        $months = [
            'January' => 1, 'February' => 2, 'March' => 3, 'April' => 4, 'May' => 5, 'June' => 6,
            'July' => 7, 'August' => 8, 'September' => 9, 'October' => 10, 'November' => 11, 'December' => 12
        ];

        //query examination lists
        $exams = Examination::where('status', 1)->where('school_id', Auth::user()->school_id)->orderBy('exam_type')->get();

        //query examination_results by for the specific class which exists in the db table
        $monthsResult = Examination_result::query()
                                            ->join('examinations', 'examinations.id', '=', 'examination_results.exam_type_id')
                                            ->select('examination_results.*', 'examinations.exam_type')
                                            ->where('class_id', $class)
                                            ->whereYear('examination_results.exam_date', $year)
                                            ->where('examination_results.school_id', $school->id)
                                            ->orderBy('examination_results.exam_date')
                                            ->get();

        $groupedByMonth = $monthsResult->groupBy(function ($item) {
            return Carbon::parse($item->exam_date)->format('F');
        });

        //get compiled results
        $compiled_results = compiled_results::where('school_id', $school->id)
                                            ->where('class_id', $class)
                                            ->where('compiled_term', 'term1')
                                            ->whereRaw('JSON_CONTAINS(source_results, ?)', [json_encode([1, 2, 3])])
                                            ->get();


        $groupedByExamType = $results->groupBy('exam_type_id');

        return view('Results.general_result_type', compact('school', 'groupedByMonth', 'year', 'exams', 'grades', 'class', 'groupedByExamType'));
    }

    //send compiled results to the table compiled_results table
    public function saveCompiledResults(Request $request, school $school, $year, $class)
    {
        $selectedMonths = $request->input('months', []);
        $compiledTerm = $request->input('term');
        $examType = $request->input('exam_type');

        if (empty($selectedMonths)) {
            Alert::error('Fail', 'No months selected');
            return back();
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
            Alert::error('Fail', 'No results found for the selected months');
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
                                            ->where('exam_type_id', $examType)
                                            ->where('source_results', $sourceResults)
                                            ->exists();

                if ($exists) {
                    $duplicateFound = true;
                    break 2; // Exit both loops
                }
            }
        }

        if ($duplicateFound) {
            Alert::error('Fail', 'Some results already exist in the database');
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
                    'exam_type_id' => $examType,
                    'source_results' => $sourceResults,
                    'compiled_term' => $compiledTerm,
                    'total_score' => $studentResults->sum('score'),
                    'average_score' => $studentResults->avg('score'),
                ]);
            }
        }

        Alert::success('Done', 'Compiled results saved successfully');
        return redirect()->back();

    }

    public function monthsByExamType(School $school, $year, Grade $class, $examType)
    {
        $results = Examination_result::query()
                            ->join('students', 'students.id', '=', 'examination_results.student_id')
                            ->join('grades', 'grades.id', '=', 'examination_results.class_id')
                            ->join('examinations', 'examinations.id', '=', 'examination_results.exam_type_id')
                            ->select(
                                'examination_results.*',
                                'students.first_name', 'students.middle_name', 'students.last_name', 'students.id as student_id', 'students.admission_number',
                                'grades.class_name',
                                'examinations.exam_type'
                            )
                            ->where('examination_results.school_id', $school->id)
                            ->whereYear('examination_results.exam_date', $year)
                            ->where('examination_results.class_id', $class->id)
                            ->where('examination_results.exam_type_id', $examType)
                            ->get();

        // Debugging line
        // dd($results);

        $groupedByMonth = $results->groupBy(function ($item) {
            return Carbon::parse($item->exam_date)->format('F'); // Group by month name
        });

        return view('Results.months_by_exam_type', compact('school', 'year', 'class', 'examType', 'groupedByMonth'));
    }


    public function resultsByMonth(School $school, $year, $class, $examType, $month)
    {
        // return $month;
        $monthsArray = [
            'January' => 1, 'February' => 2, 'March' => 3, 'April' => 4,
            'May' => 5, 'June' => 6, 'July' => 7, 'August' => 8, 'September' => 9,
            'October' => 10, 'November' => 11, 'December' => 12,
        ];

        $monthNumber = $monthsArray[$month];
        // return $monthNumber;
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
                            ->where('examination_results.school_id', $school->id)
                            ->whereYear('examination_results.exam_date', $year)
                            ->where('examination_results.class_id', $class)
                            ->where('examination_results.exam_type_id', $examType)
                            ->whereMonth('examination_results.exam_date', $monthNumber)
                            ->get();

        // Total number of students by gender
        $totalMaleStudents = $results->where('gender', 'male')->groupBy('student_id')->count();
        $totalFemaleStudents = $results->where('gender', 'female')->groupBy('student_id')->count();

        // Average score per course with grade and course name
        $averageScoresByCourse = $results->groupBy('course_id')->map(function ($courseResults) {
                            $averageScore = $courseResults->avg('score');
                            return [
                                'course_name' => $courseResults->first()->course_name,
                                'course_code' =>$courseResults->first()->course_code,
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
            ];

            foreach ($courseResults as $result) {
                $grade = $this->calculateGrade($result->score, $result->marking_style);
                $grades[$grade]++;
            }

            return $grades;
        });

        // Total average of all courses
        $totalAverageScore = $results->avg('score');

        // Student results with total marks, average, grade, and position
        $studentsResults = $results->groupBy('student_id')->map(function ($studentResults) {
            $totalMarks = $studentResults->sum('score');
            $average = $studentResults->avg('score');
            $grade = $this->calculateGrade($average, $studentResults->first()->marking_style);

            return [
                'student_id' => $studentResults->first()->student_id,
                'admission_number' => $studentResults->first()->admission_number,
                'student_name' => $studentResults->first()->first_name . ' ' . $studentResults->first()->middle_name . ' ' . $studentResults->first()->last_name,
                'gender' => $studentResults->first()->gender,
                'group' => $studentResults->first()->group,
                'courses' => $studentResults->map(function ($result) {
                    return [
                        'course_name' => $result->course_name,
                        'score' => $result->score,
                        'grade' => $this->calculateGrade($result->score, $result->marking_style)
                    ];
                }),
                'total_marks' => $totalMarks,
                'average' => $average,
                'grade' => $grade,
            ];
        });

        // Sort students by total marks to determine position
        $sortedStudentsResults = $studentsResults->sortByDesc('total_marks')->values()->all();

        // Add position to each student result
        foreach ($sortedStudentsResults as $index => &$studentResult) {
            $studentResult['position'] = $index + 1;
        }

        // Count grades by gender based on overall student performance
        $gradesByGender = $studentsResults->groupBy('gender')->map(function ($group) {
            $grades = ['A' => 0, 'B' => 0, 'C' => 0, 'D' => 0, 'E' => 0];
            foreach ($group as $student) {
                $grades[$student['grade']]++; // Count the grade calculated for the student
            }
            return $grades;
        });

        // Separate counts for male and female grades
        $totalMaleGrades = $gradesByGender->get('male', ['A' => 0, 'B' => 0, 'C' => 0, 'D' => 0, 'E' => 0]);
        $totalFemaleGrades = $gradesByGender->get('female', ['A' => 0, 'B' => 0, 'C' => 0, 'D' => 0, 'E' => 0]);


        // Count unique students
        $totalUniqueStudents = $results->pluck('student_id')->unique()->count();

        $pdf = \PDF::loadView('Results.results_by_month', compact(
            'school', 'year', 'class', 'examType', 'month', 'results', 'totalMaleStudents', 'totalFemaleStudents', 'totalMaleGrades', 'totalFemaleGrades',
            'averageScoresByCourse', 'evaluationScores', 'totalAverageScore', 'sortedStudentsResults', 'sumOfCourseAverages', 'sortedCourses',
            'totalUniqueStudents',
        ));
        $pdf->setOption('isHtml5ParserEnabled', true);
        $pdf->setOption('isPhpEnabled', true);
        return $pdf->stream($results->first()->class_name. ' '. $results->first()->exam_type. ' '. $month. ' Results.pdf');
    }

    private function calculateGrade($score, $marking_style)
    {
        if ($marking_style == 1) {
            if ($score >= 41) {
                return 'A';
            } elseif ($score >= 31) {
                return 'B';
            } elseif ($score >= 21) {
                return 'C';
            } elseif ($score >= 11) {
                return 'D';
            } else {
                return 'E';
            }
        } else {
            if ($score >= 81) {
                return 'A';
            } elseif ($score >= 61) {
                return 'B';
            } elseif ($score >= 41) {
                return 'C';
            } elseif ($score >= 21) {
                return 'D';
            } else {
                return 'E';
            }
        }
    }
    //end of results in general ==============================================

    //publishing results to be visible to parents

    public function publishResult(School $school, $year, $class, $examType, $month, BeemSmsService $beemSmsService)
    {
        try {

            $monthsArray = [
                'January' => 1, 'February' => 2, 'March' => 3, 'April' => 4,
                'May' => 5, 'June' => 6, 'July' => 7, 'August' => 8, 'September' => 9,
                'October' => 10, 'November' => 11, 'December' => 12,
            ];

            // return $monthsArray;
            if(array_key_exists($month, $monthsArray)){
                $monthNumber = $monthsArray[$month];
                // return $monthNumber;

                $updatedRows = Examination_result::where('school_id', $school->id)
                                                ->whereYear('exam_date', $year)
                                                ->where('class_id', $class)
                                                ->where('exam_type_id', $examType)
                                                ->whereMonth('exam_date', $monthNumber)
                                                ->update(['status' => 2]);

                // Fetch student results from the database
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
                    ->where('examination_results.class_id', $class)
                    ->where('students.status', 1)
                    ->where('examination_results.school_id', $school->id)
                    ->where('examination_results.exam_type_id', $examType)
                    ->whereMonth('examination_results.exam_date', $monthNumber)
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

                // Panga wanafunzi kwa total marks kwa mpangilio wa kushuka
                $studentsData = $studentsData->sortByDesc('total_marks')->values();
                $term = $studentResults->first()->Exam_term;

                // Wapangie ranks
                $studentsData = $studentsData->map(fn($student, $index) => tap($student, fn($s) => $s->rank = $index + 1));

                // URL ya shule
                $url = "https://shuleapptech.rf.gd";

                //find total of students
                $totalStudents = $studentsData->count();

                // Loop through each student and prepare the payload for each parent
                foreach ($studentsData as $student) {
                    $phoneNumber = $this->formatPhoneNumber($student->phone);
                    if (!$phoneNumber) {
                        // Log::error("Namba ya simu si sahihi kwa {$student->first_name}: {$student->phone}");
                        Alert::error('Error!', "Invalid phone number for {$student->first_name}");
                        continue;
                    }

                    $messageContent = "Habari! Matokeo ya: " . strtoupper("{$student->first_name} {$student->last_name}"). ", ";
                    $messageContent .= "Mtihani: " . strtoupper($student->exam_type) ." => ". strtoupper($month) . "  => Muhula " . strtoupper($term) . " - {$year}: ";
                    $messageContent .= strtoupper("{$student->courses}") . ". ";
                    $messageContent .= "Jumla: {$student->total_marks}, Wastani: " . number_format($student->average_marks,1) . ", Nafasi: {$student->rank} kati ya: {$totalStudents}. ";
                    $messageContent .= "Asante kwa kuchagua " . strtoupper($student->school_name) . ". Zaidi bofya: $url.";

                    // Prepare the recipients array
                    $recipients = [
                        [
                            'recipient_id' => $student->student_id, // Unique ID for each recipient
                            'dest_addr' => $phoneNumber, // Parent's phone number
                        ]
                    ];

                    // Send SMS to each parent individually
                    $beemSmsService->sendSms('shuleApp', $messageContent, $recipients);
                }

                // return response()->json($response);

                if($updatedRows){
                    Alert::success('Success!', 'Results published successfully');
                    return back();
                } else {
                    Alert::error('Error!', 'No results found to publish.' );
                    return redirect()->back();
                }
            } else {
                Alert::error('Error!', 'Invalid month name provided.' );
                return redirect()->back();
            }

        }
        catch (\Exception $e) {
            Alert::error('Error!', $e->getMessage());
            return back();
        }
    }

    public function unpublishResult (School $school, $year, $class, $examType, $month)
    {
        try {
            $monthsArray = [
                'January' => 1, 'February' => 2, 'March' => 3, 'April' => 4,
                'May' => 5, 'June' => 6, 'July' => 7, 'August' => 8, 'September' => 9,
                'October' => 10, 'November' => 11, 'December' => 12,
            ];

            // return $monthsArray;
            if(array_key_exists($month, $monthsArray)){
                $monthNumber = $monthsArray[$month];
                // return $monthNumber;

                $updatedRows = Examination_result::where('school_id', $school->id)
                                                ->whereYear('exam_date', $year)
                                                ->where('class_id', $class)
                                                ->where('exam_type_id', $examType)
                                                ->whereMonth('exam_date', $monthNumber)
                                                ->update(['status' => 1]);
                if($updatedRows){
                    Alert::success('Success!', 'Results unpublished successfully');
                    return back();
                } else {
                    Alert::error('Error!', 'No results found to unpublish.' );
                    return redirect()->back();
                }
            } else {
                Alert::error('Error!', 'Invalid month name provided.' );
                return redirect()->back();
            }

        }
        catch (\Exception $e) {
            Alert::error('Error publishing results', ['error' => $e->getMessage()]);
            return back();
        }
    }

    public function deleteResults(School $school, $year, $class, $examType, $month)
    {
        try {
            $monthsArray = [
                'January' => 1, 'February' => 2, 'March' => 3, 'April' => 4,
                'May' => 5, 'June' => 6, 'July' => 7, 'August' => 8, 'September' => 9,
                'October' => 10, 'November' => 11, 'December' => 12,
            ];

            // return $monthsArray;
            if(array_key_exists($month, $monthsArray)){
                $monthNumber = $monthsArray[$month];
                // return $monthNumber;

                $results = Examination_result::where('school_id', $school->id)
                                                ->whereYear('exam_date', $year)
                                                ->where('class_id', $class)
                                                ->where('exam_type_id', $examType)
                                                ->whereMonth('exam_date', $monthNumber)
                                                ->delete();
                if($results) {
                    Alert::success('Done', 'Results has deleted successfully');
                    return redirect()->back();
                }

            } else {
                Alert::error('Error!', 'Invalid month name provided.' );
                return redirect()->back();
            }

        }
        catch(\Exception $e){
            Alert::error('Error', $e->getMessage());
            return back();
        }
    }

    //individual students reports show list
    public function individualStudentReports(school $school, $year, $class, $examType, $month)
    {
         // Mwezi kwa namba
        $monthsArray = [
            'January' => 1, 'February' => 2, 'March' => 3, 'April' => 4,
            'May' => 5, 'June' => 6, 'July' => 7, 'August' => 8, 'September' => 9,
            'October' => 10, 'November' => 11, 'December' => 12,
        ];

        $classId = Grade::findOrFail($class);

        $monthNumber = $monthsArray[$month];

        // Chagua wanafunzi wa kipekee kulingana na student_id
        $studentsResults = Examination_result::query()
                    ->join('students', 'students.id', '=', 'examination_results.student_id')
                    ->join('grades', 'grades.id', '=', 'examination_results.class_id')
                    ->join('parents', 'parents.id', '=', 'students.parent_id')
                    ->leftJoin('users', 'users.id', '=', 'parents.user_id')
                    ->select(
                        'students.id as student_id',
                        'students.first_name', 'students.middle_name', 'students.last_name', 'students.admission_number',
                        'students.gender', 'grades.class_name', 'users.phone'
                    )
                        ->whereYear('examination_results.exam_date', $year)
                        ->whereMonth('examination_results.exam_date', $monthNumber)
                        ->where('examination_results.class_id', $class)
                        ->distinct() // Hakikisha data ni ya kipekee kulingana na select fields
                        ->orderBy('students.first_name')
                        ->get();

        return view('Results.results_students_list', compact('school', 'year', 'class', 'classId', 'examType', 'studentsResults', 'month'));
    }

    public function downloadIndividualReport(school $school, $year, $class, $examType, $month, $student)
    {
        $studentId = Student::findOrFail($student);
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
                ->where('examination_results.exam_type_id', $examType)
                ->where('examination_results.class_id', $class)
                ->whereYear('examination_results.exam_date', $year)
                ->whereMonth('examination_results.exam_date', $monthValue)
                ->where('examination_results.school_id', $school->id)
                ->get();

        // return $results;
        // Calculate the sum of all scores
        $totalScore = $results->sum('score');

        // Calculate the average score
        $averageScore = $totalScore / $results->count();

        // Determine the student's overall rank based on their total score
        $rankings = Examination_result::query()
                        ->select('student_id', DB::raw('SUM(score) as total_score'))
                        ->groupBy('student_id')
                        ->orderBy('total_score', 'desc')
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
                    $result->remarks = 'Very Good';
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
                    $result->remarks = 'Very Good';
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

            // Determine the rank for the student in each course
            $courseRankings = Examination_result::query()
                ->where('course_id', $result->course_id)
                ->select('student_id', DB::raw('SUM(score) as total_score'))
                ->groupBy('student_id')
                ->orderBy('total_score', 'desc')
                ->get();

            $result->courseRank = $courseRankings->pluck('student_id')->search($studentId->id) + 1;
        }

        // Pass the calculated data to the view
        $pdf = \PDF::loadView('Results.parent_results', compact('results', 'year', 'examType', 'studentId', 'student', 'month', 'totalScore', 'averageScore', 'studentRank', 'rankings'));

        return $pdf->stream($results->first()->first_name .' Results '.$month. ' '. $year. '.pdf');
    }

    //Re-send sms results individually
    public function sendResultSms(School $school, $year, $class, $examType, $month, $student_id, BeemSmsService $beemSmsService)
    {
        try {
            // Fetch student information
            $studentInfo = Student::findOrFail($student_id);

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
                ->where('examination_results.exam_type_id', $examType)
                ->where('examination_results.class_id', $class)
                ->where('students.status', 1)
                ->whereYear('examination_results.exam_date', $year)
                ->whereMonth('examination_results.exam_date', $monthValue)
                ->where('examination_results.school_id', $school->id)
                ->where('examination_results.status', 2)
                ->get();

            // Hakikisha kuwa kuna data kwenye $results
            if ($results->isEmpty()) {
                // Log::error("Hakuna matokeo yaliyopatikana kwa mwanafunzi: {$studentInfo->id}");
                Alert::error('Error', 'No results found for the selected student or Selected results are blocked');
                return redirect()->back();
            }

            // Calculate total score and average score
            $totalScore = $results->sum('score');
            $averageScore = $results->count() > 0 ? $totalScore / $results->count() : 0;

            // Determine the student's overall rank
            $rankings = Examination_result::query()
                ->select('student_id', DB::raw('SUM(score) as total_score'))
                ->groupBy('student_id')
                ->orderBy('total_score', 'desc')
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
            $url = 'https://shuleapptech.rf.gd';
            $messageContent = "Habari! Matokeo ya ". strtoupper($fullName ).", Mtihani: ". strtoupper($examination)." => ". strtoupper($month). ", Muhula ". strtoupper($term). " - ". $year." ni:" . implode(', ', array_map('strtoupper', $courseScores));
            $messageContent .= ". Jumla: $totalScore, Wastani: ". number_format($averageScore, 1) .", Nafasi: $studentRank kati ya: $totalStudents. Asante kwa kuchagua ". strtoupper($schoolName) ." . Zaidi bofya: $url";

            // Output the message content (or send it via SMS)
            // return $messageContent;

            // find the parent phone number
            $parent = Parents::where('id', $studentInfo->parent_id)->first();
            // return $parent;
            //find phone related to parent in users table
            $users = User::where('id', $parent->user_id)->first();
            // return $users->phone;

            //prepare send sms payload
            $sourceAddr = 'shuleApp';
            $recipient_id = 1;
            $phone = $this->formatPhoneNumber($users->phone);
            $recipients = [
                [
                    'recipient_id' => $recipient_id++,
                    'dest_addr' => $phone
                ],
            ];

            //send sms
            $response = $beemSmsService->sendSms($sourceAddr, $messageContent, $recipients);
            Alert::success('Done', 'Message sent successfully');
            return redirect()->back();

        } catch (Exception $e) {
            Alert::error('Error', $e->getMessage());
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
}
