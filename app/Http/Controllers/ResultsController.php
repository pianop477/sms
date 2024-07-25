<?php

namespace App\Http\Controllers;

use App\Models\Examination_result;
use App\Models\Grade;
use App\Models\school;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ResultsController extends Controller
{
    public function index (Student $student)
    {
        $results = Examination_result::where('student_id', $student->id)->get();
        // return $results;
        $groupedData = $results->groupBy(function($item) {
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
        // abort(404);
        $examType = Examination_result::query()->join('examinations', 'examinations.id', '=', 'examination_results.exam_type_id')
                                        ->distinct()
                                        ->select('examination_results.*', 'examinations.id as exam_id', 'examinations.exam_type')
                                        ->whereYear('exam_date', $year)
                                        ->where('student_id', $student->id)
                                        ->paginate(10);
        return view('Results.result_type', compact('student', 'year', 'examType'));
    }

    /**
     * Store the newly created resource in storage.
     */
    public function viewStudentResult(Student $student, $year, $type, $month)
    {

        // Fetch individual student results with year, exam type, and month filter
        $results = Examination_result::query()
            ->join('students', 'students.id', '=', 'examination_results.student_id')
            ->join('subjects', 'subjects.id', '=', 'examination_results.course_id')
            ->join('grades', 'grades.id', '=', 'examination_results.class_id')
            ->leftJoin('schools', 'schools.id', '=', 'students.school_id')
            ->join('examinations', 'examinations.id', '=', 'examination_results.exam_type_id')
            ->select(
                'examination_results.*',
                'students.id as studentId', 'students.first_name', 'students.middle_name', 'students.last_name', 'students.image',
                'subjects.course_name', 'subjects.course_code',
                'grades.class_name', 'grades.class_code',
                'schools.school_name', 'schools.school_reg_no', 'schools.postal_address', 'schools.postal_name', 'schools.country', 'schools.logo',
                'examinations.exam_type'
            )
            ->whereYear('examination_results.exam_date', $year)
            ->where('examination_results.exam_type_id', $type)
            ->where('examination_results.student_id', $student->id)
            ->whereMonth('examination_results.exam_date', $month) // Filter by month
            ->get();

        // Determine grades and remarks based on marking style
        $results->each(function ($result) {
            if ($result->marking_style == 1) {
                if ($result->score >= 41) {
                    $result->grade = 'A';
                    $result->remark = 'Excellent';
                } elseif ($result->score >= 31) {
                    $result->grade = 'B';
                    $result->remark = 'Good';
                } elseif ($result->score >= 21) {
                    $result->grade = 'C';
                    $result->remark = 'Pass';
                } elseif ($result->score >= 11) {
                    $result->grade = 'D';
                    $result->remark = 'Unsatisfactory';
                } else {
                    $result->grade = 'E';
                    $result->remark = 'Fail';
                }
            } else {
                if ($result->score >= 81) {
                    $result->grade = 'A';
                    $result->remark = 'Excellent';
                } elseif ($result->score >= 61) {
                    $result->grade = 'B';
                    $result->remark = 'Good';
                } elseif ($result->score >= 41) {
                    $result->grade = 'C';
                    $result->remark = 'Pass';
                } elseif ($result->score >= 21) {
                    $result->grade = 'D';
                    $result->remark = 'Unsatisfactory';
                } else {
                    $result->grade = 'E';
                    $result->remark = 'Fail';
                }
            }
        });

        // Calculate summary for the individual student
        $summary = [
            'average' => $results->avg('score'),
            'total_marks' => $results->sum('score')
        ];

        // Fetch total marks for each student and rank them
       // Fetch total marks for each student and rank them
        $studentsTotalMarks = Examination_result::query()
                            ->join('examinations', 'examinations.id', '=', 'examination_results.exam_type_id')
                            ->select('student_id', DB::raw('SUM(score) as total_marks'))
                            ->whereYear('exam_date', $year)
                            ->where('examinations.exam_type', $type)
                            ->whereMonth('exam_date', $month)
                            ->groupBy('student_id')
                            ->orderBy('total_marks', 'desc')
                            ->get();

        // Debug the total marks
        // dd($studentsTotalMarks);

        // Assign positions
        $positions = [];
        foreach ($studentsTotalMarks as $index => $studentTotal) {
            $positions[$studentTotal->student_id] = $index + 1;
        }

        // Get the current student's position
        $currentStudentPosition = $positions[$student->id] ?? 'Position not available';

        // Pass data to the view
        $data = [
            'results' => $results,
            'summary' => $summary,
            'currentStudentPosition' => $currentStudentPosition,
            'student' => $student,
            'year' => $year,
            'type' => $type,
            'month' => $month
        ];

        // Load view and generate PDF
        $pdf = \PDF::loadView('Results.parent_results', $data);
        // Return the PDF as a download
        return $pdf->stream('student_report.pdf');
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
        return $pdf->stream('General_results.pdf');
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
}
