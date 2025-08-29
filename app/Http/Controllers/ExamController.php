<?php

namespace App\Http\Controllers;

use App\Models\class_learning_courses;
use App\Models\Examination;
use App\Models\Examination_result;
use App\Models\generated_reports;
use App\Models\Grade;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\temporary_results;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\DB;
// use Barryvdh\DomPDF\PDF;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade as PDF;
use Vinkla\Hashids\Facades\Hashids;

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
        $user = Auth::user();
        $loggedTeacher = Teacher::where('user_id', $user->id)->firstOrFail();
        $decoded = Hashids::decode($id);
        // return $decoded;
        $class_course = class_learning_courses::findOrFail($decoded[0]);
        // return $class_course;

        if($class_course->teacher_id != $loggedTeacher->id) {
            Alert()->toast('You are not authorized to view this page', 'error');
            return back();
        }

        $savedResults = temporary_results::where('course_id', $class_course->course_id)
                                        ->where('teacher_id', $class_course->teacher_id)
                                        ->where('class_id', $class_course->class_id)
                                        ->where('school_id', $user->school_id)
                                        ->where('status', 'draft')
                                        ->get();
        // return $savedResults;
        $exams = Examination::where('school_id', Auth::user()->school_id)->where('status', 1)->orderBy('exam_type')->get();
        return view('Examinations.prepare_form', ['exams' => $exams, 'class_course' => $class_course, 'saved_results' => $savedResults]);
    }

    //capture values and send it to the next step===========================
    public function captureValues(Request $request)
    {
        $dataValidation = $request->validate([
            'exam_type' => 'required|exists:examinations,id',
            'exam_date' => 'required|date_format:Y-m-d',
            'term' => 'required',
            'marking_style' => 'required|integer'
        ], [
            'exam_type.required' => 'Please select an examination type',
            'exam_date.required' => 'Please select an exam date',
            'term.required' => 'Please select a term',
            'marking_style.required' => 'Please select a marking style'
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

        $user = Auth::user();
        $loggedTeacher = Teacher::where('user_id', $user->id)->firstOrFail();

        // Pata wanafunzi wa darasa husika
        $students = Student::where('class_id', $classId)
                            ->where('status', 1)
                            ->where('graduated', 0)
                            ->orderBy('first_name', 'ASC')
                            ->get();

        // Pata majina ya darasa, somo, na mtihani
        $className = Grade::find($classId)->class_code;
        $courseName = Subject::find($courseId)->course_code;
        $examName = Examination::find($examTypeId)->exam_type;

        // Angalia kama mwalimu huyu bado ana matokeo ya muda kwenye database
        $existingSavedResults = temporary_results::where('course_id', $courseId)
                                                ->where('teacher_id', $teacherId)
                                                ->where('class_id', $classId)
                                                ->where('school_id', $schoolId)
                                                ->exists();

        if ($existingSavedResults) {
            Alert()->toast('You have pending results, please submit or delete first', 'error');
            return to_route('score.prepare.form', Hashids::encode($courseId));
        }

        $exams = Examination::where('school_id', Auth::user()->school_id)->where('status', 1)->orderBy('exam_type')->get();

        // Hakuna matokeo ya muda, waruhusu waingize matokeo mapya
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
            'exams' => $exams
        ]);
    }


    public function storeScore(Request $request)
    {
        $validated = $request->validate([
            'course_id' => 'required|integer|exists:subjects,id',
            'class_id' => 'required|integer|exists:grades,id',
            'teacher_id' => 'required|integer|exists:teachers,id',
            'school_id' => 'required|integer|exists:schools,id',
            'exam_id' => 'required|integer|exists:examinations,id',
            'exam_date' => 'required|date|date_format:Y-m-d',
            'term' => 'required|in:i,ii',
            'marking_style' => 'required|in:1,2',
        ], [
            'course_id.required' => 'Course is required',
            'class_id.required' => 'Class is required',
            'teacher_id.required' => 'Teacher is required',
            'school_id.required' => 'School is required',
            'exam_id.required' => 'Exam type is required',
            'exam_date.required' => 'Exam date is required',
            'term.required' => 'Term is required',
            'marking_style.required' => 'Marking style is required'
        ]);

        // Define validation rules conditionally based on marking style
        $scoreValidation = $request->marking_style == 1 ? 'nullable|numeric|min:0|max:50' : 'nullable|numeric|min:0|max:100';

        $rules = [
            'students.*.student_id' => 'required|exists:students,id',
            'students.*.score' => $scoreValidation,  // Dynamic validation for score
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $errorMessages = implode(' ', $validator->errors()->all());
            Alert::error('Validation Error!', $errorMessages);
            return to_route('score.prepare.form', Hashids::encode($request->course_id));
        }

        $students = $request->input('students');
        $action = $request->input('action'); // Save or Submit action
        // dd($action);

        if ($action === 'save') {
            // Save temporary results (draft)
            foreach ($students as $studentData) {
                $studentId = $studentData['student_id'];
                $score = $studentData['score'];

                // Insert or update the temporary results table
                temporary_results::updateOrCreate(
                    [
                        'student_id' => $studentId,
                        'course_id' => $request->course_id,
                        'class_id' => $request->class_id,
                        'teacher_id' => $request->teacher_id,
                        'exam_type_id' => $request->exam_id,
                        'school_id' => $request->school_id,
                        'exam_date' => $request->exam_date,
                        'exam_term' => $request->term,
                        'marking_style' => $request->marking_style,
                        'expiry_date' => now()->addHours(72)
                    ],
                    ['score' => $score]
                );
            }

            Alert::toast('Examination results has been saved to the draft', 'success');
            return to_route('home');
            // return to_route('score.prepare.form', Hashids::encode($request->course_id));
        }

        if ($action === 'submit') {
            // Check for existing results in examination_results table
            foreach ($students as $studentData) {
                $studentId = $studentData['student_id'];
                $score = $studentData['score'];

                // Check if the result already exists in the examination_results table
                $existingRecord = Examination_result::where('student_id', $studentId)
                                                    ->where('course_id', $request->course_id)
                                                    ->where('exam_type_id', $request->exam_id)
                                                    ->whereDate('exam_date', Carbon::parse($request->exam_date)->format('Y-m-d'))
                                                    ->exists();

                if ($existingRecord) {
                    Alert::toast('Examination results already submitted for this Course', 'error');
                    return redirect()->route('home');
                }

                // Insert the result into the examination_results table
                Examination_result::create([
                    'student_id' => $studentId,
                    'course_id' => $request->course_id,
                    'class_id' => $request->class_id,
                    'teacher_id' => $request->teacher_id,
                    'exam_type_id' => $request->exam_id,
                    'school_id' => $request->school_id,
                    'exam_date' => $request->exam_date,
                    'Exam_term' => $request->term,
                    'score' => $score,
                    'marking_style' => $request->marking_style
                ]);
            }

            Alert::toast('Examination results has been submitted successfully', 'success');
            // return redirect()->route('score.prepare.form', Hashids::encode($request->course_id));
            return redirect()->route('home');
        }

        // If action is not 'save' or 'submit'
        Alert::error('Invalid action', 'error');
        return redirect()->route('score.prepare.form', Hashids::encode($request->course_id));
    }


    /**
     * Store the newly created resource in storage examination type.
     */
    public function store(Request $request)
    {
        // abort(404);
        $request->validate([
            'name' => 'required|string|max:255',
            'abbreviation' => 'required|string|max:8'
        ], [
            'name.required' => 'Examination type name is required',
            'abbreviation.required' => 'Examination code is required'
        ]);

        $existingRecords = Examination::where('exam_type', '=', $request->name)->where('symbolic_abbr', $request->abbreviation)->where('school_id', '='. Auth::user()->school_id)->exists();
        if($existingRecords) {
            // Alert::error('Error!', 'The Examination type already Exists');
            Alert::toast('The Examination type already Exists', 'error');
            return back();
        }

        $exams = new Examination();
        $exams->exam_type = $request->name;
        $exams->symbolic_abbr = $request->abbreviation;
        $exams->school_id = Auth::user()->school_id;
        $exams->save();
        // Alert::success('Success!', 'Exmination test Saved successfully');
        Alert::toast('Examination test Saved successfully', 'success');
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
        // Alert::success('Success!', 'Examination test Unblocked successfully');
        Alert::toast('Examination test Unblocked successfully', 'success');
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
            'name' => 'required|string|max:255',
            'abbreviation' => 'required|string|max:8'
        ], [
            'name.required' => 'Examination type name is required',
            'abbreviation.required' => 'Examination code is required'
        ]);

        $exam = Examination::findOrFail($exams);
        $exam->exam_type = $request->name;
        $exam->symbolic_abbr = $request->abbreviation;
        $exam->save();
        // Alert::success('Success!', 'Examination test updated successfully');
        Alert::toast('Examination test updated successfully', 'success');
        return redirect()->route('exams.index');
    }


    public function blockExams(Request $request, $exam)
    {
        //
        $exams = Examination::findOrFail($exam);
        $exams->status = $request->input('status', 0);
        $exams->save();
        // Alert::success('Success!', 'Examination test Blocked successfully');
        Alert::toast('Examination test Blocked successfully', 'success');
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
            Alert()->toast('No such examination type was found', 'error');
            return back();
       }

       $hasTempResults = temporary_results::where('exam_type_id', $examination)->exists();
       $hasPermResults = Examination_result::where('exam_type_id', $examination)->exists();

       if($hasTempResults || $hasPermResults) {
            Alert()->toast('Cannot delete this examination because has results payload', 'info');
            return back();
       }

       $examination->delete();
       Alert()->toast('Examination type has been deleted successfully', 'success');
       return back();
    }

    //get results by its course=========================
    public function courseResults($id)
    {
        $decoded = Hashids::decode($id);
        // return $decoded;
        $user = Auth::user();
        $loggedTeacher = Teacher::where('user_id', $user->id)->firstOrFail(); //get teacher id from the logged in user
        $class_course = class_learning_courses::findOrFail($decoded[0]);
        // return $class_course;

        if(! $class_course) {
            Alert()->toast('You are not authorized to view this page', 'error');
            return back();
        }

        $results = Examination_result::where('course_id', $class_course->course_id)
                                        ->where('class_id', $class_course->class_id)
                                        // ->where('teacher_id', $loggedTeacher->id)
                                        ->where('school_id', $user->school_id)
                                        ->orderBy('exam_date', 'DESC')
                                        ->get();

        // Use distinct to ensure unique years
        $groupedData = $results->groupBy(function ($item) {
            return Carbon::parse($item->exam_date)->format('Y');
        });

        return view('Examinations.teacher_results_by_year', compact('groupedData', 'class_course'));
    }


    public function resultByYear($course, $year)
    {
        $id = Hashids::decode($course);
        // return $id;
        $user = Auth::user();
        $loggedTeacher = Teacher::where('user_id', $user->id)->firstOrFail();
        $class_course = class_learning_courses::findOrFail($id[0]);

        if($class_course->teacher_id != $loggedTeacher->id) {
            Alert()->toast('You are not authorized to view this page', 'error');
            return to_route('home');
        }
        // return ['data' => $class_course];
        $results = Examination_result::query()
                    ->join('examinations', 'examinations.id', '=', 'examination_results.exam_type_id')
                    ->select('examination_results.*', 'examinations.exam_type')
                    ->where('course_id', $class_course->course_id)
                    ->where('class_id', $class_course->class_id)
                    ->whereYear('exam_date', $year)
                    ->where('examination_results.school_id', $user->school_id)
                    ->orderBy('examination_results.exam_date', 'DESC')
                    ->distinct()  // Ensure distinct exam types
                    ->get();

            // return ['results' => $results ];

        // Group by exam type ID
        $examTypes = $results->groupBy('exam_type_id');

        return view('Examinations.teacher_results_by_exam_type', compact('examTypes', 'year', 'class_course'));
    }


    public function resultByExamType($course, $year, $examType)
    {
        $id = Hashids::decode($course);
        $exam_id = Hashids::decode($examType);
        $user = Auth::user();

        $loggedTeacher = Teacher::where('user_id', $user->id)->firstOrFail();
        $class_course = class_learning_courses::findOrFail($id[0]);

        if($class_course->teacher_id != $loggedTeacher->id) {
            Alert()->toast('You are not authorized to view this page', 'error');
            return back();
        }
        // return ['data' => $class_course];
        $results = Examination_result::where('course_id', $class_course->course_id)
                                ->where('class_id', $class_course->class_id)
                                ->whereYear('exam_date', $year)
                                ->where('exam_type_id', $exam_id[0])
                                ->where('school_id', $user->school_id)
                                ->distinct()  // Ensure distinct months
                                ->get();

        $months = $results->sortBy('exam_date') // Panga kwa tarehe
            ->groupBy(function ($item) {
                return Carbon::parse($item->exam_date)->format('F'); // Group by month
            })->map(function ($monthData) {

                return $monthData->sortBy('exam_date')->groupBy(function ($item) {
                 return Carbon::parse($item->exam_date)->format('d F Y'); // Group by specific date
            });
        });

        return view('Examinations.teacher_results_by_month', compact('months', 'year', 'examType', 'exam_id', 'course', 'class_course'));
    }

    public function resultByMonth($course, $year, $examType, $month, $date)
    {
        $id = Hashids::decode($course);
        $exam_id = Hashids::decode($examType);
        $user = Auth::user();
        $loggedTeacher = Teacher::where('user_id', $user->id)->firstOrFail();
        $class_course = class_learning_courses::findOrFail($id[0]);

        if($class_course->teacher_id != $loggedTeacher->id) {
            Alert()->toast('You are not authorized to view this page', 'error');
            return back();
        }

        $subjectCourse = Subject::findOrFail($class_course->course_id);

        $monthMap = [
            'January' => 1, 'February' => 2, 'March' => 3,
            'April' => 4, 'May' => 5, 'June' => 6,
            'July' => 7, 'August' => 8, 'September' => 9,
            'October' => 10, 'November' => 11, 'December' => 12
        ];

        $monthNumber = $monthMap[$month] ?? null;
        $resultDate = Carbon::parse($date)->format('Y-m-d');

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
                        'subjects.course_name', 'subjects.course_code', 'users.first_name as teacher_firstname', 'students.status',
                        'users.last_name as teacher_lastname', 'users.gender as teacher_gender', 'users.phone as teacher_phone', 'schools.school_reg_no'
                    )
                    ->where('examination_results.course_id', $class_course->course_id)
                    ->where('examination_results.class_id', $class_course->class_id)
                    ->where('students.status', 1)
                    ->where('examination_results.exam_type_id', $exam_id)
                    ->where('examination_results.school_id', $user->school_id)
                    ->whereDate('examination_results.exam_date', $resultDate)
                    ->distinct()
                    ->orderBy('examination_results.score', 'desc')
                    ->orderBy('students.first_name', 'asc')
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
        $validResults = $results
                        ->whereNotNull('score')
                        ->filter(fn($res) => is_numeric($res->score) && $res->score >= 0);

        $totalScore = $validResults->sum('score');
        $totalRecords = $validResults->count();
        $averageScore = $totalRecords > 0 ? $totalScore / $totalRecords : 0;

        // Sort results by score descending
        $results = $results->sortByDesc('score')->values();

        $lastScore = null;
        $position = 0;
        $counter = 0;

        foreach ($results as $result) {
            $counter++;

            // Determine position with tie logic
            if ($result->score !== $lastScore) {
                $position = $counter;
                $lastScore = $result->score;
            }

            if ($result->marking_style == 1) {
                if ($result->score >= 40.5) {
                    $result->grade = 'A';
                    $gradeCounts['A']++;
                } elseif ($result->score >= 30.5) {
                    $result->grade = 'B';
                    $gradeCounts['B']++;
                } elseif ($result->score >= 20.5) {
                    $result->grade = 'C';
                    $gradeCounts['C']++;
                } elseif ($result->score >= 10.5) {
                    $result->grade = 'D';
                    $gradeCounts['D']++;
                } elseif($result->score >= 0.1) {
                    $result->grade = 'E';
                    $gradeCounts['E']++;
                } else {
                    $result->grade = 'ABS';
                }
            } else {
                if ($result->score >= 80.5) {
                    $result->grade = 'A';
                    $gradeCounts['A']++;
                } elseif ($result->score >= 60.5) {
                    $result->grade = 'B';
                    $gradeCounts['B']++;
                } elseif ($result->score >= 40.5) {
                    $result->grade = 'C';
                    $gradeCounts['C']++;
                } elseif ($result->score >= 20.5) {
                    $result->grade = 'D';
                    $gradeCounts['D']++;
                } elseif($result->score >= 0.5) {
                    $result->grade = 'E';
                    $gradeCounts['E']++;
                } else {
                    $result->grade = 'ABS';
                }
            }

            // Assign position
            $result->position = $position;
            // Usiweke $totalScore += $result->score hapa kwa sababu tumehesabu tayari
        }

        // $averageScore = $totalRecords > 0 ? $totalScore / $totalRecords : 0;
        $averageGrade = $this->determineGrade($averageScore, $results->first()->marking_style);

        // Count number of students by gender
        $maleStudents = $results->where('gender', 'male')->count();
        $femaleStudents = $results->where('gender', 'female')->count();

        // Generate the PDF and store it in the 'exam_results' folder within 'public' directory
        $pdf = \PDF::loadView('Examinations.teacher_results_by_type', compact(
            'results', 'year', 'examType', 'month', 'course', 'subjectCourse', 'class_course', 'maleStudents', 'femaleStudents', 'averageScore', 'averageGrade', 'gradeCounts'
        ));

        // Generate a timestamp-based filename for the PDF
        $timestamp = now()->timestamp; // Current timestamp in seconds
        $pdfFileName = 'results_'.$timestamp.'.pdf'; // Using timestamp as the filename

        // Define the folder path where you want to store the PDF
        $folderPath = public_path('exam_results');

        // Create the 'exam_results' folder if it doesn't exist
        if (!file_exists($folderPath)) {
            mkdir($folderPath, 0755, true); // Creates the folder if it doesn't exist
        }

        // Store the generated PDF in the 'exam_results' folder
        $pdfPath = $folderPath.'/'.$pdfFileName;
        $pdf->save($pdfPath);

        // Return the URL of the generated PDF to be embedded in the iframe
        $pdfUrl = url('exam_results/'.$pdfFileName);

        // Optionally: You can schedule a task to delete the file after a certain time (e.g., after 30 days).
        // For example, you can schedule a cron job to delete old reports or use Laravel's task scheduling for automatic deletion.

        return view('Examinations.results_pdf_report', compact('pdfUrl', 'year', 'id', 'month', 'class_course', 'date', 'exam_id',));  // Pass the URL to the view
    }

    protected function determineGrade($score, $marking_style)
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
            } else {
                return 'ABS';
            }
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
            } else {
                return 'ABS';
            }
        }
    }

    public function editDraft(Request $request)
    {
        // Pata data zote kutoka request
        $courseId = $request->course_id;
        $classId = $request->class_id;
        $teacherId = $request->teacher_id;
        $schoolId = $request->school_id;
        $examTypeId = $request->exam_type_id;
        $examDate = $request->exam_date;
        $examTerm = $request->term;

        $markingStyle = $request->marking_style;

        $user = Auth::user();
        $loggedTeacher = Teacher::where('user_id', $user->id)->firstOrFail();

        $exists = class_learning_courses::where('course_id', $courseId)
                                        ->where('class_id', $classId)
                                        ->where('teacher_id', $loggedTeacher->id)
                                        ->exists();

        if (!$exists) {
            Alert()->toast('You are not authorized to view this page', 'error');
            return to_route('home');
            // return to_route('score.prepare.form', Hashids::encode($courseId));
        }

        // Pata matokeo yaliyohifadhiwa kwenye draft
        $draftResults = temporary_results::where('course_id', $courseId)
                                       ->where('teacher_id', $teacherId)
                                       ->where('exam_type_id', $examTypeId)
                                       ->where('class_id', $classId)
                                        ->where('exam_date', $examDate)
                                        ->where('school_id', $schoolId)
                                        ->where('marking_style', $markingStyle)
                                        // ->where('exam_term', $examTerm)
                                       ->get();
        // return $draftResults;

        // Pata wanafunzi wa darasa hili
        $students = Student::where('class_id', $classId)
                            ->where('status', 1)
                            ->where('graduated', 0)
                            ->orderBy('first_name', 'ASC')
                            ->get();

        // Pata majina ya darasa, somo, na mtihani
        $className = Grade::find($classId)->class_code;
        $courseName = Subject::find($courseId)->course_code;
        $examName = Examination::find($examTypeId)->exam_type;
        $exams = Examination::where('school_id', Auth::user()->school_id)->where('status', 1)->orderBy('exam_type')->get();

        // Load view ya ku-edit matokeo
        return view('Examinations.edit_score', [
            'courseId' => $courseId,
            'classId' => $classId,
            'teacherId' => $teacherId,
            'schoolId' => $schoolId,
            'examTypeId' => $examTypeId,
            'examDate' => $examDate,
            'term' => $examTerm,
            'students' => $students,
            'className' => $className,
            'courseName' => $courseName,
            'examName' => $examName,
            'marking_style' => $markingStyle,
            'draftResults' => $draftResults,
            'exams' => $exams
        ]);
    }

    public function updateDraftResults(Request $request)
    {
        $courseId = $request->course_id;
        $classId = $request->class_id;
        $teacherId = $request->teacher_id;
        $schoolId = $request->school_id;
        $examTypeId = $request->exam_type_id;
        $examDate = $request->exam_date;
        $examTerm = $request->term;
        $markingStyle = $request->marking_style;
        $scores = $request->scores;
        $action = $request->input('action');
        // return $examTerm;

        $user = Auth::user();
        $loggedTeacher = Teacher::where('user_id', $user->id)->firstOrFail();

        $exists = class_learning_courses::where('course_id', $courseId)
                                        ->where('class_id', $classId)
                                        ->where('teacher_id', $loggedTeacher->id)
                                        ->exists();

        if (!$exists) {
            Alert()->toast('You are not authorized to view this page', 'error');
            to_route('home');
            // return to_route('score.prepare.form', Hashids::encode($courseId));
        }

        if ($action === 'save') {
            // SAVE TO DRAFT
            foreach ($scores as $studentId => $score) {
                temporary_results::updateOrCreate(
                    [
                        'student_id' => $studentId,
                        'course_id' => $courseId,
                        'teacher_id' => $teacherId,
                    ],
                    [
                        'class_id' => $classId,
                        'exam_type_id' => $examTypeId,
                        'school_id' => $schoolId,
                        'exam_date' => $examDate,
                        'exam_term' => $examTerm,
                        'score' => $score,
                        'marking_style' => $markingStyle,
                    ]
                );
            }
            Alert()->toast('Results saved successfully, remember to submit before expiry date.', 'success');
            // return redirect()->route('score.prepare.form', Hashids::encode($courseId));
            return to_route('home');

        } elseif ($action === 'submit') {
            // CHECK IF RESULTS ALREADY EXIST IN EXAMINATION_RESULT TABLE
            foreach ($scores as $studentId => $score) {
                $existingResult = Examination_result::where('student_id', $studentId)
                                ->where('course_id', $courseId)
                                ->where('teacher_id', $teacherId)
                                ->where('exam_type_id', $examTypeId)
                                ->where('exam_date', $examDate)
                                ->first();

                if ($existingResult) {
                    // If result already exists, reject this submission
                    Alert()->toast('Results already exist. Please check before submitting.', 'error');
                    return to_route('home');
                }
            }

            // SUBMIT FINAL RESULTS & DELETE FROM DRAFT
            DB::transaction(function () use ($scores, $courseId, $classId, $teacherId, $examTypeId, $examDate, $examTerm, $schoolId, $markingStyle) {
                foreach ($scores as $studentId => $score) {
                    Examination_result::create([
                        'student_id' => $studentId,
                        'course_id' => $courseId,
                        'teacher_id' => $teacherId,
                        'exam_type_id' => $examTypeId,
                        'class_id' => $classId,
                        'school_id' => $schoolId,
                        'exam_date' => $examDate,
                        'Exam_term' => $examTerm,
                        'score' => $score,
                        'marking_style' => $markingStyle
                    ]);
                }

                // DELETE TEMPORARY RESULTS AFTER FINAL SUBMISSION
                temporary_results::where('course_id', $courseId)
                        ->where('teacher_id', $teacherId)
                        ->where('exam_type_id', $examTypeId)
                        ->where('class_id', $classId)
                        ->where('exam_date', $examDate)
                        ->where('school_id', $schoolId)
                        ->delete();
            });

            Alert()->toast('Results submitted successfully. Editing is no longer allowed.', 'success');
            // return redirect()->route('score.prepare.form', Hashids::encode($courseId));
            return redirect()->route('home');
        }

        Alert()->toast('Invalid action.', 'error');
        return to_route('home');
    }

    //pending results outside button
    public function continuePendingResults ($course, $teacher, $school, $class, $style, $term, $type,   $date)
    {
        // Decode the Hashids and get the first element of the returned array
        $courseId = Hashids::decode($course)[0];  // Get the first element
        $classId = Hashids::decode($class)[0];  // Get the first element
        $teacherId = Hashids::decode($teacher)[0];  // Get the first element
        $schoolId = Hashids::decode($school)[0];  // Get the first element
        $examTypeId = $type;
        $marking_style = $style;
        $exam_term = $term;
        $exam_date = Carbon::parse($date)->format('Y-m-d');
        // return $exam_date;
        $examDate = Carbon::parse($date)->format('Y-m-d');

        $user = Auth::user();
        $loggedTeacher = Teacher::where('user_id', $user->id)->firstOrFail();

        $exists = class_learning_courses::where('course_id', $courseId)
                                        ->where('class_id', $classId)
                                        ->where('teacher_id', $loggedTeacher->id)
                                        ->exists();

        if (!$exists) {
            Alert()->toast('You are not authorized to view this page', 'error');
            // return to_route('score.prepare.form', Hashids::encode($courseId));
            return to_route('home');
        }

        // Pata wanafunzi wa darasa husika
        $students = Student::where('class_id', $classId)
                            ->where('status', 1)
                            ->where('graduated', 0)
                            ->orderBy('first_name', 'ASC')
                            ->get();

        // Pata majina ya darasa, somo, na mtihani
        $className = Grade::find($classId)->class_code;
        $courseName = Subject::find($courseId)->course_code;
        $examName = Examination::find($examTypeId)->exam_type;

        // Ikiwa tayari kuna matokeo ambayo hayajahakikiwa, rudisha mtumiaji kwenye ukurasa wa uthibitisho
        $saved_results = temporary_results::where('course_id', $courseId)
                                    ->where('teacher_id', $teacherId)
                                    ->where('class_id', $classId)
                                    ->where('exam_term', $exam_term)
                                    ->where('school_id', $schoolId)
                                    ->where('exam_type_id', $examTypeId)
                                    ->where('exam_date', $examDate)
                                    ->where('marking_style', $marking_style)
                                    ->get();

        // Return the view with decoded IDs
        return view('Examinations.confirm_results',
                    compact('courseId', 'examTypeId', 'examDate', 'marking_style', 'examName',
                    'classId', 'teacherId', 'schoolId', 'courseName', 'className', 'students', 'saved_results'));
    }

    public function deleteDraftResults($course, $teacher, $type, $class, $date)
    {
        $course_id = Hashids::decode($course);
        $teacher_id = Hashids::decode($teacher);
        $class_id = Hashids::decode($class);
        $examDate = Carbon::parse($date)->format('Y-m-d');
        $examType = $type;
        $teacherInfo = Teacher::find($teacher_id[0]);
        $user = Auth::user();
        $loggedTeacher = Teacher::where('user_id', $user->id)->firstOrFail();

        $exists = class_learning_courses::where('course_id', $course_id[0])
                                        ->where('class_id', $class_id[0])
                                        ->where('teacher_id', $loggedTeacher->id)
                                        ->exists();

        if (!$exists) {
            Alert()->toast('You are not authorized to view this page', 'error');
            return to_route('score.prepare.form', Hashids::encode($course_id[0]));
        }
        // Check if there are any records that match the given parameters
        $results = temporary_results::where('course_id', $course_id[0])
            ->where('teacher_id', $teacher_id[0])
            ->where('exam_type_id', $examType)
            ->where('school_id', $teacherInfo->school_id)
            ->where('class_id', $class_id[0])
            ->where('exam_date', $examDate)
            ->get(); // Get all matching results

        if ($results->isEmpty()) {
            Alert()->toast('No results found in draft box', 'info');
            return redirect()->route('score.prepare.form', ['id' => $course]);
        }

        // Delete all the matching records
        temporary_results::where('course_id', $course_id[0])
            ->where('teacher_id', $teacher_id[0])
            ->where('exam_type_id', $examType)
            ->where('school_id', $teacherInfo->school_id)
            ->delete();

        Alert()->toast('All results deleted successfully from the draft box', 'success');
        return redirect()->route('score.prepare.form', ['id' => $course]);
    }

    public function TeacherDeleteResults($course, $year, $examType, $month, $date)
    {
        $couurse_id = Hashids::decode($course);
        $exam_id = Hashids::decode($examType);
        $user = Auth::user();
        $loggedTeacher = Teacher::where('user_id', $user->id)->firstOrFail();
        $class_course = class_learning_courses::findOrFail($couurse_id[0]);

        if($class_course->teacher_id != $loggedTeacher->id) {
            Alert()->toast('You are not authorized to view this page', 'error');
            return to_route('results.byExamType', ['course' => $course, 'year' => $year, 'examType' => $examType]);
        }
        $examDate = Carbon::parse($date)->format('Y-m-d');

        $existInCompile = generated_reports::where('class_id', $class_course->class_id)
                                    ->whereJsonContains('exam_dates', $examDate)
                                    ->where('school_id', $class_course->school_id)
                                    ->exists();

        if ($existInCompile) {
            Alert()->toast('Results already exist in the compiled reports. Cannot delete.', 'error');
            return to_route('results.byExamType', ['course' => $course, 'year' => $year, 'examType' => $examType]);
        }

        // already published results
        $isPublished = Examination_result::where('course_id', $class_course->course_id)
                                    ->where('class_id', $class_course->class_id)
                                    ->where('teacher_id', $loggedTeacher->id)
                                    ->where('exam_type_id', $exam_id[0])
                                    ->whereDate('exam_date', $examDate)
                                    ->where('status', 2)
                                    ->exists();
        if ($isPublished) {
            Alert()->toast('Results has already been published. Cannot delete.', 'error');
            return to_route('results.byExamType', ['course' => $course, 'year' => $year, 'examType' => $examType]);
        }

        try {
            // Delete the results for the specified course, year, and exam type
            Examination_result::where('course_id', $class_course->course_id)
                            ->where('class_id', $class_course->class_id)
                            ->where('teacher_id', $loggedTeacher->id)
                            ->where('exam_type_id', $exam_id[0])
                            ->whereDate('exam_date', $examDate)
                            ->where('status', 1)
                            ->delete();

            Alert()->toast('Results deleted successfully', 'success');
            return to_route('results.byExamType', ['course' => $course, 'year' => $year, 'examType' => $examType]);
        } catch (\Exception $e) {
            Alert()->toast('Error deleting results: ' . $e->getMessage(), 'error');
            return to_route('results.byExamType', ['course' => $course, 'year' => $year, 'examType' => $examType]);
        }
    }

}
