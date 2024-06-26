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
    ]);

    // Capture values
    $courseId = $request->course_id;
    $classId = $request->class_id;
    $teacherId = $request->teacher_id;
    $schoolId = $request->school_id;
    $examTypeId = $request->exam_type;
    $examDate = $request->exam_date;
    $term = $request->term;

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
    ]);
}



    //store examination scores ==================================
    public function storeScore(Request $request)
    {
        // Define validation rules
        $rules = [
            'students.*.student_id' => 'required|exists:students,id',
            'students.*.score' => 'required|numeric|min:0|max:50',  // Validation for score
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
        $students = $request->input('students');

        // Debugging output
        // \Log::info('Exam Type ID: ' . $examTypeId);
        // \Log::info('Exam Date: ' . $examDate);
        // \Log::info('Course ID: ' . $courseId);
        // \Log::info('Teacher ID: ' . $teacherId);
        // \Log::info('Class ID: ' . $classId);
        // \Log::info('School ID: ' . $schoolId);

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
                // Log the data before creating the record
                // \Log::info('Creating record with data: ', [
                //     'student_id' => $studentId,
                //     'course_id' => $courseId,
                //     'class_id' => $classId,
                //     'teacher_id' => $teacherId,
                //     'exam_type_id' => $examTypeId,
                //     'school_id' => $schoolId,
                //     'exam_date' => $examDate,
                //     'score' => $score,
                //     'Exam_term' => $term
                // ]);

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
                    'Exam_term' => $term
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

    public function viewResultCourse(Subject $courses)
    {
        $results = Examination_result::query()
            ->join('students', 'students.id', '=', 'examination_results.student_id')
            ->join('subjects', 'subjects.id', '=', 'examination_results.course_id')
            ->join('grades', 'grades.id', '=', 'examination_results.class_id')
            ->join('examinations', 'examinations.id', '=', 'examination_results.exam_type_id')
            ->join('teachers', 'teachers.id', '=', 'examination_results.teacher_id')
            ->leftJoin('users', 'users.id', '=', 'teachers.user_id')
            ->select(
                'examination_results.*',
                'grades.class_name', 'grades.class_code',
                'students.first_name', 'students.middle_name', 'students.id as studentId',  'students.last_name', 'students.class_id', 'students.group', 'students.gender',
                'examinations.exam_type', 'examinations.id as examId',
                'users.first_name as teacher_firstname', 'users.last_name as teacherlastname', 'users.phone as teacher_phone'
            )
            ->where('examination_results.course_id', $courses->id)
            ->where('examination_results.teacher_id', $courses->teacher_id)
            ->where('examination_results.class_id', '=', $courses->class_id)
            ->get();

        // Group results by year and examination type
        $groupedResults = $results->groupBy(function ($item) {
            return Carbon::parse($item->exam_date)->format('Y');
        })->map(function ($yearGroup) {
            return $yearGroup->groupBy('exam_type');
        });

        return view('Examinations.teacher_results', compact('groupedResults', 'courses'));
    }

//
    public function viewResultsByYear($year)
    {
        // Retrieve distinct exam types and their corresponding months for the specified year
        $examTypes = Examination_result::query()
            ->join('examinations', 'examinations.id', '=', 'examination_results.exam_type_id')
            ->whereYear('examination_results.exam_date', $year)
            ->distinct()
            ->select('examinations.exam_type', DB::raw('MONTH(examination_results.exam_date) as exam_month'))
            ->orderBy(DB::raw('MONTH(examination_results.exam_date)'))
            ->paginate(5);

        return view('Examinations.results_by_year', compact('examTypes', 'year'));
    }



    public function viewResultsByType($year, $type)
    {
        $user = Auth::user()->id;
        $teacher = Teacher::where('user_id', $user)->firstOrFail();
        // return $teacher;
        $course = Subject::where('teacher_id', $teacher->id)->firstOrFail();
        // return $course;
        $results = Examination_result::query()
            ->join('students', 'students.id', '=', 'examination_results.student_id')
            ->join('subjects', 'subjects.id', '=', 'examination_results.course_id')
            ->join('grades', 'grades.id', '=', 'examination_results.class_id')
            ->join('examinations', 'examinations.id', '=', 'examination_results.exam_type_id')
            ->join('teachers', 'teachers.id', '=', 'examination_results.teacher_id')
            ->leftJoin('users', 'users.id', '=', 'teachers.user_id')
            ->select(
                'examination_results.*',
                'subjects.course_name', 'subjects.course_code',
                'grades.class_name', 'grades.class_code',
                'students.first_name', 'students.id as studentId', 'students.middle_name', 'students.last_name', 'students.class_id', 'students.group', 'students.gender',
                'examinations.exam_type',
                'users.first_name as teacher_firstname', 'users.last_name as teacherlastname', 'users.phone as teacher_phone',
                DB::raw('MONTH(examination_results.exam_date) as exam_month')
            )
            ->whereYear('examination_results.exam_date', $year)
            ->where('examinations.exam_type', $type)
            ->where('examination_results.course_id', $course->id)
            ->where('examination_results.teacher_id', $course->teacher_id)
            ->where('examination_results.class_id', $course->class_id)
            ->orderBy('students.first_name', 'ASC')
            ->get();

        // Calculate summary statistics
        $total_male = $results->where('gender', 'male')->count();
        $total_female = $results->where('gender', 'female')->count();

        $summary = [
            'total_students' => $results->count(),
            'total_male' => $total_male,
            'total_female' => $total_female,
            'grades' => [
                'male_E' => $results->where('score', '<=', 10)->where('gender', 'male')->count(),
                'female_E' => $results->where('score', '<=', 10)->where('gender', 'female')->count(),
                'male_D' => $results->whereBetween('score', [11, 20])->where('gender', 'male')->count(),
                'female_D' => $results->whereBetween('score', [11, 20])->where('gender', 'female')->count(),
                'male_C' => $results->whereBetween('score', [21, 30])->where('gender', 'male')->count(),
                'female_C' => $results->whereBetween('score', [21, 30])->where('gender', 'female')->count(),
                'male_B' => $results->whereBetween('score', [31, 40])->where('gender', 'male')->count(),
                'female_B' => $results->whereBetween('score', [31, 40])->where('gender', 'female')->count(),
                'male_A' => $results->whereBetween('score', [41, 50])->where('gender', 'male')->count(),
                'female_A' => $results->whereBetween('score', [41, 50])->where('gender', 'female')->count(),
            ],
            'average_score' => $results->avg('score'),
            'course_name' => $results->first()->course_name ?? '',
            'class_name' => $results->first()->class_name ?? ''
        ];

        return view('Examinations.results_by_type', compact('results', 'summary', 'year', 'type'));
    }



}
