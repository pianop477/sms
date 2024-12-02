<?php

namespace App\Http\Controllers;

use App\Models\class_learning_courses;
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
    public function prepare($id)
    {
        // abort(404)
        $class_course = class_learning_courses::find($id);

        $exams = Examination::where('school_id', Auth::user()->school_id)->where('status', 1)->get();
        return view('Examinations.prepare_form', ['exams' => $exams, 'class_course' => $class_course]);
    }

    //navigate to the next page if there are saved data in the browser
    public function savedDataForm()
    {
        // Retrieve all necessary data from the session
        $courseId = session()->get('course_id');
        $classId = session()->get('class_id');
        $teacherId = session()->get('teacher_id');
        $schoolId = session()->get('school_id');
        $examTypeId = session()->get('exam_type_id');
        $examDate = session()->get('exam_date');
        $term = session()->get('term');
        $markingStyle = session()->get('marking_style');

        $students = Student::where('class_id', $classId)->where('status', 1)->orderBy('first_name', 'ASC')->get();
        $className = Grade::find($classId)->class_code;
        $courseName = Subject::find($courseId)->course_code;
        $examName = Examination::find($examTypeId)->exam_type;

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
            'savedData' => session()->get('saved_data') // Add this to pass saved data
        ]);
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
        $requestData = $request->all();
        $examTypeId = $request->session()->get('exam_type_id');
        $examDate = $request->session()->get('exam_date');
        $courseId = $request->session()->get('course_id');
        $teacherId = $request->session()->get('teacher_id');
        $classId = $request->session()->get('class_id');
        $schoolId = $request->session()->get('school_id');
        $term = $request->session()->get('term');
        $markingStyle = $request->session()->get('marking_style');
        $class_course = class_learning_courses::where('course_id', $courseId)->first();

        // Define validation rules conditionally based on marking style
        $scoreValidation = $markingStyle == 1 ? 'required|numeric|min:0|max:50' : 'required|numeric|min:0|max:100';

        $rules = [
            'students.*.student_id' => 'required|exists:students,id',
            'students.*.score' => $scoreValidation,  // Dynamic validation for score
        ];

        $validator = Validator::make($requestData, $rules);

        if ($validator->fails()) {
            $errorMessages = $validator->errors()->all();
            $errorMessage = implode(' ', $errorMessages);
            Alert::error('Validation Error!', $errorMessage);
            return redirect()->back()->withInput();
        }

        $students = $request->input('students');

        foreach ($students as $studentData) {
            $studentId = $studentData['student_id'];
            $score = $studentData['score'];

            // Check for duplicate records
            $existingRecord = Examination_result::where('student_id', $studentId)
                                            ->where('course_id', $courseId)
                                            ->where('exam_type_id', $examTypeId)
                                            ->whereMonth('exam_date', Carbon::parse($examDate)->month)
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
        return redirect()->route('score.prepare.form', $courseId)->with(['class_course' => $class_course]);
        // return redirect()->route('home');
    }


    /**
     * Store the newly created resource in storage examination type.
     */
    public function store(Request $request)
    {
        // abort(404);
        $request->validate([
            'name' => 'required|string|max:255'
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
    public function edit(Examination $exam)
    {
        return view('Examinations.Edit', compact('exam'));
    }

    /**
     * Update the resource in storage.
     */
    public function update(Request $request, $exams)
    {
        $request->validate([
            'name' => 'required|string|max:255'
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
       $examination = Examination::find($exam);
       if(! $examination) {
            Alert::error('Error', 'No such examination type was found');
            return back();
       }

       $hasResults = Examination_result::where('exam_type_id', $examination)->exists();
       if($hasResults) {
            Alert::info('Info', 'Cannot delete this examination because has results payload for this school');
            return back();
       }

       $examination->delete();
       Alert::success('Success!', 'Examination type has been deleted successfully');
       return back();
    }

    //get results by its course=========================
    public function courseResults($id)
    {
        $class_course = class_learning_courses::find($id);
        // return $class_course;

        if(! $class_course) {
            Alert::error('Error!', 'No such course was found');
            return back();
        }

        $results = Examination_result::where('course_id', $class_course->course_id)
                                        ->where('class_id', $class_course->class_id)
                                        ->where('teacher_id', $class_course->teacher_id)
                                        ->get();

        // Use distinct to ensure unique years
        $groupedData = $results->groupBy(function ($item) {
            return Carbon::parse($item->exam_date)->format('Y');
        });

        return view('Examinations.teacher_results_by_year', compact('groupedData', 'class_course'));
    }


    public function resultByYear(Subject $course, $year)
    {
        $courses = Subject::find($course->id);
        if(! $courses) {
            Alert::error('Error!', 'No such course was found');
            return back();
        }
        // return ['data' => $courses];
        $class_course = class_learning_courses::where('course_id', $courses->id)->first();
        // return ['data' => $class_course];
        $results = Examination_result::query()
                    ->join('examinations', 'examinations.id', '=', 'examination_results.exam_type_id')
                    ->select('examination_results.*', 'examinations.exam_type')
                    ->where('course_id', $class_course->course_id)
                    ->where('class_id', $class_course->class_id)
                    ->where('teacher_id', $class_course->teacher_id)
                    ->whereYear('exam_date', $year)
                    ->distinct()  // Ensure distinct exam types
                    ->get();

            // return ['results' => $results ];

        // Group by exam type ID
        $examTypes = $results->groupBy('exam_type_id');

        return view('Examinations.teacher_results_by_exam_type', compact('examTypes', 'year', 'class_course', 'course'));
    }


    public function resultByExamType($course, $year, $examType)
    {
        $class_course = class_learning_courses::where('course_id', $course)->first();
        // return ['data' => $class_course];
        $results = Examination_result::where('course_id', $class_course->course_id)
                                ->where('class_id', $class_course->class_id)
                                ->where('teacher_id', $class_course->teacher_id)
                                ->whereYear('exam_date', $year)
                                ->where('exam_type_id', $examType)
                                ->distinct()  // Ensure distinct months
                                ->get();

        // Group by month name
        $months = $results->groupBy(function ($item) {
            return Carbon::parse($item->exam_date)->format('F'); // Full month name
        });

        return view('Examinations.teacher_results_by_month', compact('months', 'year', 'examType', 'course', 'class_course'));
    }

    public function resultByMonth($course, $year, $examType, $month)
    {
        // return ['data' => $course];
        $subjectCourse = Subject::find($course);
        // return $subjectCourse;
        $class_course = class_learning_courses::where('course_id', $course)->first();
        // return ['kozi' => $class_course];
        $monthMap = [
            'January' => 1, 'February' => 2, 'March' => 3,
            'April' => 4, 'May' => 5, 'June' => 6,
            'July' => 7, 'August' => 8, 'September' => 9,
            'October' => 10, 'November' => 11, 'December' => 12
        ];

        $monthNumber = $monthMap[$month] ?? null;

        $results = Examination_result::query()
                        ->join('students', 'students.id', '=', 'examination_results.student_id')
                        ->join('subjects', 'subjects.id', '=', 'examination_results.course_id')
                        ->join('grades', 'grades.id', '=', 'examination_results.class_id')
                        ->join('examinations', 'examinations.id', '=', 'examination_results.exam_type_id')
                        ->join('teachers', 'teachers.id', '=', 'examination_results.teacher_id')
                        ->leftJoin('users', 'users.id', '=', 'teachers.user_id')
                        ->leftJoin('schools', 'schools.id', '=', 'students.school_id')
                        ->select(
                            'examination_results.*', 'grades.class_name', 'grades.class_code', 'examinations.exam_type',
                            'students.first_name', 'students.id as studentId', 'students.middle_name', 'students.last_name', 'students.gender', 'students.group', 'students.class_id', 'students.admission_number',
                            'subjects.course_name', 'subjects.course_code', 'users.first_name as teacher_firstname',
                            'users.last_name as teacher_lastname', 'users.gender as teacher_gender', 'users.phone as teacher_phone', 'schools.school_reg_no'
                        )
                        ->where('examination_results.course_id', $class_course->course_id)
                        ->where('examination_results.class_id', $class_course->class_id)
                        ->where('examination_results.teacher_id', $class_course->teacher_id)
                        ->whereYear('examination_results.exam_date', $year)
                        ->where('examination_results.exam_type_id', $examType)
                        ->whereMonth('examination_results.exam_date', $monthNumber)
                        ->distinct()  // Ensure distinct results
                        ->orderBy('examination_results.score', 'desc')
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
            'results', 'year', 'examType', 'month', 'course', 'subjectCourse', 'class_course', 'maleStudents', 'femaleStudents', 'averageScore', 'averageGrade', 'gradeCounts'
        ));

        return $pdf->stream('results_'.$subjectCourse->course_code.'_'.$month.'_'.$year.'.pdf');
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
