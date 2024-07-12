<?php

namespace App\Http\Controllers;

use App\Models\Examination;
use App\Models\Examination_result;
use App\Models\Grade;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\DB;
// use Barryvdh\DomPDF\PDF;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade as PDF;

class ExamController extends Controller
{
    public function index()
    {
        $exams = Examination::where('school_id', '=', Auth::user()->school_id)
                            ->orderBy('exam_type', 'ASC')
                            ->get();
        return view('Examinations.index', ['exams' => $exams]);
    }
    /**
     * Show the form for creating the resource.
     */
    public function prepare($course)
    {
        // abort(404)
        $courses = Subject::findOrFail($course);
        $exams = Examination::where('school_id', Auth::user()->school_id)->where('status', 1)->get();
        return view('Examinations.prepare_form', ['exams' => $exams, 'courses' => $courses]);
    }

    //capture values and send it to the next step===========================
    public function captureValues(Request $request)
    {
        $dataValidation = $request->validate([
            'exam_type' => 'required|exists:examinations,id',
            'exam_date' => 'required|date_format:Y-m-d',
            'term' => 'required',
            'marking_style' => 'required|integer'
        ]);

        // Capture values
        $courseId = $request->course_id;
        $classId = $request->class_id;
        $teacherId = $request->teacher_id;
        $schoolId = $request->school_id;
        $examTypeId = $request->exam_type;
        $examDate = $request->exam_date;
        $term = $request->term;
        $markingStyle = $request->marking_style;

        $students = Student::where('class_id', $classId)->where('status', 1)->orderBy('first_name', 'ASC')->get();
        $className = Grade::find($classId)->class_code;
        $courseName = Subject::find($courseId)->course_code;
        $examName = Examination::find($examTypeId)->exam_type;

        // Store the captured values into session
        $request->session()->put('course_id', $courseId);
        $request->session()->put('class_id', $classId);
        $request->session()->put('teacher_id', $teacherId);
        $request->session()->put('school_id', $schoolId);
        $request->session()->put('exam_type_id', $examTypeId);
        $request->session()->put('exam_date', $examDate);
        $request->session()->put('term', $term);
        $request->session()->put('marking_style', $markingStyle);

        return view('Examinations.register_score', [
            'courseId' => $courseId,
            'classId' => $classId,
            'teacherId' => $teacherId,
            'schoolId' => $schoolId,
            'examTypeId' => $examTypeId,
            'examDate' => $examDate,
            'term' => $term,
            'students' => $students,
            'className' => $className,
            'courseName' => $courseName,
            'examName' => $examName,
            'marking_style' => $markingStyle,
        ]);
    }

    //store examination scores ==================================
    public function storeScore(Request $request)
    {
        // Define validation rules
        $rules = [
            'students.*.student_id' => 'required|exists:students,id',
            'students.*.score' => 'required|numeric|min:0|max:100',  // Validation for score
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $examTypeId = $request->session()->get('exam_type_id');
        $examDate = $request->session()->get('exam_date');
        $courseId = $request->session()->get('course_id');
        $teacherId = $request->session()->get('teacher_id');
        $classId = $request->session()->get('class_id');
        $schoolId = $request->session()->get('school_id');
        $term = $request->session()->get('term');
        $markingStyle = $request->session()->get('marking_style');
        $students = $request->input('students');

        foreach ($students as $studentData) {
            $studentId = $studentData['student_id'];
            $score = $studentData['score'];

            // Check for duplicate records
            $existingRecord = Examination_result::where('student_id', $studentId)
                                                ->where('Exam_term', $term)
                                                ->where('course_id', $courseId)
                                               ->where('exam_date', $examDate)
                                               ->exists();

            if ($existingRecord) {
                Alert::error('Error!', 'Examination Results already submitted for this Course');
                return redirect()->route('score.prepare.form', $courseId);
            } else {
                // Create a new examination result entry
                Examination_result::create([
                    'student_id' => $studentId,
                    'course_id' => $courseId,
                    'class_id' => $classId,
                    'teacher_id' => $teacherId,
                    'exam_type_id' => $examTypeId,
                    'school_id' => $schoolId,
                    'exam_date' => $examDate,
                    'score' => $score,
                    'Exam_term' => $term,
                    'marking_style' => $markingStyle
                ]);
            }
        }

        Alert::success('Success!', 'Examination results have been submitted successfully');
        return redirect()->route('score.prepare.form', $courseId);
    }

    /**
     * Store the newly created resource in storage.
     */
    public function store(Request $request)
    {
        // abort(404);
        $request->validate([
            'name' => 'required|string|max:100'
        ]);

        $existingRecords = Examination::where('exam_type', '=', $request->name)->where('school_id', '='. Auth::user()->school_id)->exists();
        if($existingRecords) {
            Alert::error('Error!', 'The Examination type already Exists');
            return back();
        }

        $exams = new Examination();
        $exams->exam_type = $request->name;
        $exams->school_id = Auth::user()->school_id;
        $exams->save();
        Alert::success('Success!', 'Exmination test Saved successfully');
        return back();
    }

    /**
     * Display the resource.
     */
    public function unblockExams(Request $request, $exam)
    {
        //
        $exams = Examination::findOrFail($exam);
        $exams->status = $request->input('status', 1);
        $exams->save();
        Alert::success('Success!', 'Examination test Unblocked successfully');
        return back();
    }

    /**
     * Show the form for editing the resource.
     */
    public function edit($exam)
    {
        //
        $exams = Examination::findOrFail($exam);
        return view('Examinations.Edit', ['exams' => $exams]);
    }

    /**
     * Update the resource in storage.
     */
    public function update(Request $request, $exams)
    {
        $request->validate([
            'name' => 'required|string|max:100'
        ]);

        $exam = Examination::findOrFail($exams);
        $exam->exam_type = $request->name;
        $exam->save();
        Alert::success('Success!', 'Examination test updated successfully');
        return redirect()->route('exams.index');
    }


    public function blockExams(Request $request, $exam)
    {
        //
        $exams = Examination::findOrFail($exam);
        $exams->status = $request->input('status', 0);
        $exams->save();
        Alert::success('Success!', 'Examination test Blocked successfully');
        return back();

    }

    /**
     * Remove the resource from storage.
     */
    public function destroy($exam)
    {
        // abort(404);
        $exams = Examination::findOrFail($exam);
        $exams->delete();
        Alert::success('Success!', 'Examination test deleted successfully');
        return back();
    }

    //get results by its course=========================
    public function courseResults(Subject $courses)
    {
        $results = Examination_result::where('course_id', $courses->id)
            ->where('class_id', $courses->class_id) // Assuming class_id matches class_alias
            ->where('teacher_id', $courses->teacher_id)
            ->get();

        $groupedData = $results->groupBy(function ($item) {
            return Carbon::parse($item->exam_date)->format('Y');
        });

        return view('Examinations.teacher_results_by_year', compact('groupedData', 'courses'));
    }

    public function resultByYear(Subject $courses, $year)
    {
        $results = Examination_result::query()->join('examinations', 'examinations.id', '=', 'examination_results.exam_type_id')
            ->select('examination_results.*', 'examinations.exam_type')
            ->where('course_id', $courses->id)
            ->where('class_id', $courses->class_id) // Assuming class_id matches class_alias
            ->where('teacher_id', $courses->teacher_id)
            ->whereYear('exam_date', $year)
            ->get();

        $examTypes = $results->groupBy('exam_type_id');

        return view('Examinations.teacher_results_by_exam_type', compact('examTypes', 'year', 'courses'));
    }

    public function resultByExamType(Subject $courses, $year, $examType)
    {
        $results = Examination_result::where('course_id', $courses->id)
            ->where('class_id', $courses->class_id) // Assuming class_id matches class_alias
            ->where('teacher_id', $courses->teacher_id)
            ->whereYear('exam_date', $year)
            ->where('exam_type_id', $examType)
            ->get();

        $months = $results->groupBy(function ($item) {
            return Carbon::parse($item->exam_date)->format('F');
        });

        return view('Examinations.teacher_results_by_month', compact('months', 'year', 'examType', 'courses'));
    }


    public function resultByMonth(Subject $courses, $year, $examType, $month)
    {
        $results = Examination_result::query()
            ->join('students', 'students.id', '=', 'examination_results.student_id')
            ->join('subjects', 'subjects.id', '=', 'examination_results.course_id')
            ->join('grades', 'grades.id', '=', 'examination_results.class_id')
            ->join('schools', 'schools.id', '=', 'examination_results.school_id')
            ->join('examinations', 'examinations.id', '=', 'examination_results.exam_type_id')
            ->join('teachers', 'teachers.id', '=', 'examination_results.teacher_id')
            ->leftJoin('users', 'users.id', '=', 'teachers.user_id')
            ->select(
                'examination_results.*', 'grades.class_name', 'grades.class_code', 'examinations.exam_type',
                'students.first_name', 'students.id as studentId', 'students.middle_name', 'students.last_name', 'students.gender', 'students.group', 'students.class_id',
                'subjects.course_name', 'subjects.course_code', 'users.first_name as teacher_firstname',
                'users.last_name as teacher_lastname', 'users.gender as teacher_gender', 'users.phone as teacher_phone'
            )
            ->where('examination_results.course_id', $courses->id)
            ->where('examination_results.class_id', $courses->class_id) // Assuming class_id matches class_alias
            ->where('examination_results.teacher_id', $courses->teacher_id)
            ->whereYear('examination_results.exam_date', $year)
            ->where('examination_results.exam_type_id', $examType)
            ->whereMonth('examination_results.exam_date', Carbon::parse($month)->month)
            ->orderBy('examination_results.score', 'desc') // Sort by score descending
            ->get();

        // Initialize grade counts
        $gradeCounts = [
            'A' => 0,
            'B' => 0,
            'C' => 0,
            'D' => 0,
            'E' => 0
        ];

        // Calculate grades, positions, and average score
        $totalScore = 0;
        $totalRecords = $results->count();
        $position = 1;

        foreach ($results as $result) {
            if ($result->marking_style == 1) {
                if ($result->score >= 41) {
                    $result->grade = 'A';
                    $gradeCounts['A']++;
                } elseif ($result->score >= 31) {
                    $result->grade = 'B';
                    $gradeCounts['B']++;
                } elseif ($result->score >= 21) {
                    $result->grade = 'C';
                    $gradeCounts['C']++;
                } elseif ($result->score >= 11) {
                    $result->grade = 'D';
                    $gradeCounts['D']++;
                } else {
                    $result->grade = 'E';
                    $gradeCounts['E']++;
                }
            } else {
                if ($result->score >= 81) {
                    $result->grade = 'A';
                    $gradeCounts['A']++;
                } elseif ($result->score >= 61) {
                    $result->grade = 'B';
                    $gradeCounts['B']++;
                } elseif ($result->score >= 41) {
                    $result->grade = 'C';
                    $gradeCounts['C']++;
                } elseif ($result->score >= 21) {
                    $result->grade = 'D';
                    $gradeCounts['D']++;
                } else {
                    $result->grade = 'E';
                    $gradeCounts['E']++;
                }
            }
            $totalScore += $result->score;
            $result->position = $position++;
        }

        $averageScore = $totalRecords > 0 ? $totalScore / $totalRecords : 0;
        $averageGrade = $this->determineGrade($averageScore, $results->first()->marking_style);

        // Count number of students by gender
        $maleStudents = $results->where('gender', 'male')->count();
        $femaleStudents = $results->where('gender', 'female')->count();

        $pdf = \PDF::loadView('Examinations.teacher_results_by_type', compact(
            'results', 'year', 'examType', 'month', 'courses', 'maleStudents', 'femaleStudents', 'averageScore', 'averageGrade', 'gradeCounts'
        ));

        $pdfPath = public_path('results.pdf');
        $pdf->save($pdfPath);

        if (!file_exists($pdfPath)) {
            return response()->json(['error' => 'PDF file not found after generation'], 404);
        }

        return view('Examinations.teacher_results_pdf', compact('pdfPath'));
    }

    protected function determineGrade($score, $marking_style)
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
}
