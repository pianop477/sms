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
        $results = Examination_result::query()->join('grades', 'grades.id', '=', 'examination_results.class_id')
                                                ->join('students', 'students.id', '=', 'examination_results.student_id')
                                                ->join('teachers', 'teachers.id', '=', 'examination_results.teacher_id')
                                                ->leftJoin('users', 'users.id', '=', 'teachers.user_id')
                                                ->join('subjects', 'subjects.id', '=', 'examination_results.course_id')
                                                ->join('examinations', 'examinations.id', '=', 'examination_results.exam_type_id')
                                                ->select(
                                                    'examination_results.*',
                                                    'students.id as studentId', 'students.first_name', 'students.middle_name', 'students.last_name', 'students.gender',
                                                    'grades.class_name', 'grades.class_code',
                                                    'users.first_name as teacher_firstname', 'users.last_name as teacher_lastname', 'users.phone as teacher_phone',
                                                    'subjects.course_name', 'subjects.course_code',
                                                    'examinations.exam_type'
                                                )
                                                ->where('examination_results.student_id', $student->id)
                                                ->get();
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
                                                ->join('students', 'students.id', '=', 'examination_results.student_id')
                                                ->whereYear('examination_results.exam_date', $year)
                                                ->where('examination_results.student_id', $student->id)
                                                ->distinct()
                                                ->select(
                                                    'examinations.exam_type', DB::raw('MONTH(examination_results.exam_date) as exam_month')
                                                )
                                                ->orderBy(DB::raw('MONTH(examination_results.exam_date)'))
                                                ->get();
        return view('Results.result_type', compact('examType', 'student', 'year'));
    }

    /**
     * Store the newly created resource in storage.
     */
    public function viewStudentResult(Student $student, $year, $type)
    {
        // Fetch individual student results
        $results = Examination_result::query()->join('students', 'students.id', '=', 'examination_results.student_id')
                                                ->join('grades', 'grades.id', '=', 'examination_results.class_id')
                                                ->join('subjects', 'subjects.id', '=', 'examination_results.course_id')
                                                ->join('examinations', 'examinations.id', '=', 'examination_results.exam_type_id')
                                                ->join('teachers', 'teachers.id', '=', 'examination_results.teacher_id')
                                                ->leftJoin('users', 'users.id', '=', 'teachers.user_id')
                                                ->select(
                                                    'examination_results.*', 'grades.class_name', 'grades.class_code',
                                                    'subjects.course_name', 'subjects.course_code',
                                                    'students.id as studentId', 'students.first_name', 'students.middle_name', 'students.last_name', 'students.gender', 'students.group',
                                                    'examinations.exam_type', 'users.first_name as teacher_firstname', 'users.last_name as teacher_lastname',
                                                    'users.gender as teacher_gender', 'users.phone as teacher_phone',
                                                    DB::raw('MONTH(examination_results.exam_date) as exam_month')
                                                )
                                                ->whereYear('examination_results.exam_date', $year)
                                                ->where('examinations.exam_type', $type)
                                                ->where('examination_results.student_id', $student->id)
                                                ->get();

        // Calculate summary for the individual student
        $summary = [
            'average' => $results->avg('score'),
            'total_marks' => $results->sum('score')
        ];

        // Fetch total marks for each student and rank them
        $studentsTotalMarks = Examination_result::query()->join('examinations', 'examinations.id', '=', 'examination_results.exam_type_id')
                                    ->select('student_id', DB::raw('SUM(score) as total_marks'))
                                    ->whereYear('exam_date', $year)
                                    ->where('examinations.exam_type', $type)
                                    ->groupBy('student_id')
                                    ->orderBy('total_marks', 'desc')
                                    ->get();

        // Assign positions
        $positions = [];
        foreach ($studentsTotalMarks as $index => $studentTotal) {
            $positions[$studentTotal->student_id] = $index + 1;
        }

        // Get the current student's position
        $currentStudentPosition = $positions[$student->id] ?? null;

        return view('Results.parent_results', compact('student', 'results', 'summary', 'year', 'type', 'currentStudentPosition', 'positions'));
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

    public function monthsByExamType(school $school, $year, $class, $examType)
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
            ->where('examination_results.class_id', $class)
            ->where('examination_results.exam_type_id', $examType)
            ->get();

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
                'grade' => $this->calculateGrade($averageScore)
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
                'A' => $courseResults->whereBetween('score', [41, 50])->count(),
                'B' => $courseResults->whereBetween('score', [31, 40])->count(),
                'C' => $courseResults->whereBetween('score', [21, 30])->count(),
                'D' => $courseResults->whereBetween('score', [11, 20])->count(),
                'E' => $courseResults->whereBetween('score', [0, 10])->count(),
            ];
            return $grades;
        });

        // Total average of all courses
        $totalAverageScore = $results->avg('score');

        // Student results with total marks, average, grade, and position
        $studentsResults = $results->groupBy('student_id')->map(function ($studentResults) {
            $totalMarks = $studentResults->sum('score');
            $average = $studentResults->avg('score');
            $grade = $this->calculateGrade($average);

            return [
                'student_id' => $studentResults->first()->student_id,
                'student_name' => $studentResults->first()->first_name . ' ' . $studentResults->first()->middle_name . ' ' . $studentResults->first()->last_name,
                'gender' => $studentResults->first()->gender,
                'group' => $studentResults->first()->group,
                'courses' => $studentResults->map(function ($result) {
                    return [
                        'course_name' => $result->course_name,
                        'score' => $result->score,
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

        return view('Results.results_by_month', compact(
            'school', 'year', 'class', 'examType', 'month', 'results', 'totalMaleStudents', 'totalFemaleStudents',
            'averageScoresByCourse', 'evaluationScores', 'totalAverageScore', 'sortedStudentsResults', 'sumOfCourseAverages', 'sortedCourses'
        ));
    }

    private function calculateGrade($average)
    {
        if ($average >= 41) {
            return 'A';
        } elseif ($average >= 31) {
            return 'B';
        } elseif ($average >= 21) {
            return 'C';
        } elseif ($average >= 11) {
            return 'D';
        } else {
            return 'E';
        }
    }
    //end of results in general ==============================================
}
