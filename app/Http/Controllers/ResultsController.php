<?php

namespace App\Http\Controllers;

use App\Models\Examination;
use App\Models\Examination_result;
use App\Models\Grade;
use App\Models\school;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use RealRashid\SweetAlert\Facades\Alert as FacadesAlert;
use RealRashid\SweetAlert\Facades\Alert;

class ResultsController extends Controller
{

    public function index(Student $student)
    {
        $results = Examination_result::where('student_id', $student->id)->get();

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
         $examTypes = Examination_result::query()
             ->join('examinations', 'examinations.id', '=', 'examination_results.exam_type_id')
             ->select('examinations.exam_type', 'examinations.id as exam_id')
             ->distinct()
             ->whereYear('exam_date', $year)
             ->where('student_id', $student->id)
             ->orderBy('examinations.exam_type', 'asc')
             ->paginate(6);

         return view('Results.result_type', compact('student', 'year', 'examTypes'));
     }

     public function resultByMonth(Student $student, $year, $exam_type)
{
    $months = Examination_result::query()
        ->selectRaw('MONTH(exam_date) as month')
        ->distinct()
        ->whereYear('exam_date', $year)
        ->where('exam_type_id', $exam_type)
        ->where('student_id', $student->id)
        ->where('status', 2)
        ->orderBy('month')
        ->get();

        $examType = Examination::find($exam_type);

    return view('Results.result_months', compact('student', 'year', 'examType', 'months'));
}


    /**
     * Store the newly created resource in storage.
     */
    public function viewStudentResult(Student $student, $year, $type, $month)
    {
        // Retrieve examination results for the specific student
        $results = Examination_result::query()
            ->join('students', 'students.id', '=', 'examination_results.student_id')
            ->join('subjects', 'subjects.id', '=', 'examination_results.course_id')
            ->join('grades', 'grades.id', '=', 'examination_results.class_id')
            ->join('examinations', 'examinations.id', '=', 'examination_results.exam_type_id')
            ->join('schools', 'schools.id', '=', 'examination_results.school_id')
            ->select(
                'examination_results.*',
                'students.id as studentId', 'students.first_name', 'students.middle_name', 'students.last_name',
                'students.group', 'students.image', 'students.gender',
                'subjects.course_name', 'subjects.course_code',
                'grades.class_name', 'grades.class_code',
                'examinations.exam_type',
                'schools.school_name', 'schools.school_reg_no', 'schools.postal_address', 'schools.postal_name', 'schools.logo', 'schools.country',
            )
            ->where('examination_results.student_id', $student->id)
            ->where('examination_results.exam_type_id', $type)
            ->whereYear('examination_results.exam_date', $year)
            ->whereMonth('examination_results.exam_date', $month)
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

        $studentRank = $rankings->pluck('student_id')->search($student->id) + 1;

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
                    $result->remarks = 'Unsatisfactory';
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
                    $result->remarks = 'Unsatisfactory';
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

            $result->courseRank = $courseRankings->pluck('student_id')->search($student->id) + 1;
        }

        // Pass the calculated data to the view
        $pdf = \PDF::loadView('Results.parent_results', compact('results', 'year', 'type', 'student', 'month', 'totalScore', 'averageScore', 'studentRank', 'rankings'));

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

        $groupedByExamType = $results->groupBy('exam_type_id');

        return view('Results.general_result_type', compact('school', 'year', 'class', 'groupedByExamType'));
    }

    public function monthsByExamType(School $school, $year, Grade $class, $examType)
    {
        $results = Examination_result::query()
            ->join('students', 'students.id', '=', 'examination_results.student_id')
            ->join('grades', 'grades.id', '=', 'examination_results.class_id')
            ->join('examinations', 'examinations.id', '=', 'examination_results.exam_type_id')
            ->select(
                'examination_results.*',
                'students.first_name', 'students.middle_name', 'students.last_name', 'students.id as student_id',
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
        $results = Examination_result::query()
                            ->join('students', 'students.id', '=', 'examination_results.student_id')
                            ->join('subjects', 'subjects.id', '=', 'examination_results.course_id')
                            ->join('grades', 'grades.id', '=', 'examination_results.class_id')
                            ->join('examinations', 'examinations.id', '=', 'examination_results.exam_type_id')
                            ->select(
                                'examination_results.*',
                                'students.first_name', 'students.middle_name', 'students.last_name', 'students.gender', 'students.id as student_id', 'students.group',
                                'grades.class_name',
                                'examinations.exam_type',
                                'subjects.course_name', 'subjects.course_code'
                            )
                            ->where('examination_results.school_id', $school->id)
                            ->whereYear('examination_results.exam_date', $year)
                            ->where('examination_results.class_id', $class)
                            ->where('examination_results.exam_type_id', $examType)
                            ->whereMonth('examination_results.exam_date', Carbon::parse($month)->month)
                            ->get();

        // Total number of students by gender
        $totalMaleStudents = $results->where('gender', 'male')->groupBy('student_id')->count();
        $totalFemaleStudents = $results->where('gender', 'female')->groupBy('student_id')->count();

        // Average score per course with grade and course name
        $averageScoresByCourse = $results->groupBy('course_id')->map(function ($courseResults) {
                            $averageScore = $courseResults->avg('score');
                            return [
                                'course_name' => $courseResults->first()->course_name,
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

        $pdf = \PDF::loadView('Results.results_by_month', compact(
            'school', 'year', 'class', 'examType', 'month', 'results', 'totalMaleStudents', 'totalFemaleStudents',
            'averageScoresByCourse', 'evaluationScores', 'totalAverageScore', 'sortedStudentsResults', 'sumOfCourseAverages', 'sortedCourses'
        ));
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

    public function publishResult(School $school, $year, $class, $examType, $month)
    {
        try {
            // Convert month name to month number
            $monthNumber = date('m', strtotime($month));

            \Log::info('Publish Result called', [
                'school_id' => $school->id,
                'year' => $year,
                'class_id' => $class,
                'exam_type_id' => $examType,
                'month' => $monthNumber
            ]);

            // Update the query to correctly match the month
            $updatedRows = Examination_result::where('school_id', $school->id)
                                            ->whereYear('exam_date', $year)
                                            ->where('class_id', $class)
                                            ->where('exam_type_id', $examType)
                                            ->whereMonth('exam_date', $monthNumber)
                                            ->update(['status' => 2]);

            if ($updatedRows > 0) {
                // return redirect()->back()->with('success', 'Results published successfully.');
                Alert::success('Sucess!', 'Results published successfully');
                return back();
            } else {
                Alert::error('Error!', 'No results found to publish.' );
                return redirect()->back();
            }
        } catch (\Exception $e) {
            Alert::error('Error publishing results', ['error' => $e->getMessage()]);
            return back();
        }
    }

    public function unpublishResult (School $school, $year, $class, $examType, $month)
    {
        try {
            // Convert month name to month number
            $monthNumber = date('m', strtotime($month));

            \Log::info('Publish Result called', [
                'school_id' => $school->id,
                'year' => $year,
                'class_id' => $class,
                'exam_type_id' => $examType,
                'month' => $monthNumber
            ]);

            // Update the query to correctly match the month
            $updatedRows = Examination_result::where('school_id', $school->id)
                                            ->whereYear('exam_date', $year)
                                            ->where('class_id', $class)
                                            ->where('exam_type_id', $examType)
                                            ->whereMonth('exam_date', $monthNumber)
                                            ->update(['status' => 1]);

            if ($updatedRows > 0) {
                // return redirect()->back()->with('success', 'Results published successfully.');
                Alert::success('Sucess!', 'Results Blocked successfully');
                return back();
            } else {
                Alert::error('Error!', 'No results found to block.' );
                return redirect()->back();
            }
        } catch (\Exception $e) {
            Alert::error('Error blocking results', ['error' => $e->getMessage()]);
            return back();
        }
    }


}
