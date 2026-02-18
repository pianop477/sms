<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;

class ResultsExport implements FromView
{
    protected $results;
    protected $totalUniqueStudents;
    protected $sumOfCourseAverages;
    protected $generalClassAvg;
    protected $totalFemaleGrades;
    protected $totalMaleGrades;
    protected $sortedStudentsResults;
    protected $sortedCourses;
    protected $subjectGradesByGender;
    protected $date;
    protected $courses;
    protected $marking_style;
    protected $divisionAggregate;

    public function __construct(
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
        $marking_style = 1,
        $divisionAggregate = []
    ) {
        $this->results = $results;
        $this->totalUniqueStudents = $totalUniqueStudents;
        $this->sumOfCourseAverages = $sumOfCourseAverages;
        $this->generalClassAvg = $generalClassAvg;
        $this->totalFemaleGrades = $totalFemaleGrades;
        $this->totalMaleGrades = $totalMaleGrades;
        $this->sortedStudentsResults = $sortedStudentsResults;
        $this->sortedCourses = $sortedCourses;
        $this->subjectGradesByGender = $subjectGradesByGender;
        $this->date = $date;
        $this->courses = $courses;
        $this->marking_style = $marking_style;
        $this->divisionAggregate = $divisionAggregate;
    }

    public function view(): View
    {
        return view('Results.results_excel', [
            'results' => $this->results,
            'totalUniqueStudents' => $this->totalUniqueStudents,
            'sumOfCourseAverages' => $this->sumOfCourseAverages,
            'generalClassAvg' => $this->generalClassAvg,
            'totalFemaleGrades' => $this->totalFemaleGrades,
            'totalMaleGrades' => $this->totalMaleGrades,
            'sortedStudentsResults' => $this->sortedStudentsResults,
            'sortedCourses' => $this->sortedCourses,
            'subjectGradesByGender' => $this->subjectGradesByGender,
            'date' => $this->date,
            'courses' => $this->courses,
            'marking_style' => $this->marking_style,
            'divisionAggregate' => $this->divisionAggregate,
        ]);
    }
}
