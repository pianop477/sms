<?php

namespace App\Http\Controllers;

use App\Models\class_learning_courses;
use App\Models\Class_teacher;
use App\Models\Grade;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use Hashids\Hashids as HashidsHashids;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use RealRashid\SweetAlert\Facades\Alert;
use Vinkla\Hashids\Facades\Hashids;

class CoursesController extends Controller
{

    public function index() {
        $user = Auth::user();
        $classes = Grade::where('school_id', '=', Auth::user()->school_id)->orderBy('id', 'ASC')->get();

        //fetch all registered subjects in the institution
        $subjects = Subject::where('school_id', $user->school_id)->orderBy('course_name')->get();
        return view('Subjects.index', ['subjects' => $subjects, 'classes' => $classes]);
    }
    /**
     * Show the form for creating the resource.
     */

     //view courses by each class============================
     public function classCourses($id)
     {
        $class = Grade::find($id);
        if(! $class) {
            // Alert::error('Error!', 'No such class was found');
            Alert()->toast('No such class was found', 'error');
            return back();
        }

        $classCourse = class_learning_courses::query()
                                                ->join('teachers', 'teachers.id', '=', 'class_learning_courses.teacher_id')
                                                ->leftJoin('users', 'users.id', '=', 'teachers.user_id')
                                                ->join('subjects', 'subjects.id', '=', 'class_learning_courses.course_id')
                                                ->join('grades', 'grades.id', '=', 'class_learning_courses.class_id')
                                                ->join('schools', 'schools.id', '=', 'class_learning_courses.school_id')
                                                ->select(
                                                    'users.first_name', 'users.last_name', 'users.phone',
                                                    'grades.class_name', 'subjects.course_name', 'subjects.course_code',
                                                    'class_learning_courses.*'
                                                )
                                                ->where('class_learning_courses.class_id', $class->id)
                                                ->where('class_learning_courses.school_id', $class->school_id)
                                                ->orderBy('subjects.course_name')
                                                ->get();
        $courses = Subject::where('school_id', $class->school_id)->where('status', 1)->orderBy('course_name')->get();
        $teachers = Teacher::query()->join('users', 'users.id', '=', 'teachers.user_id')
                                    ->select('users.first_name', 'users.last_name', 'teachers.*')
                                    ->orderBy('users.first_name')
                                    ->where('teachers.school_id', $class->school_id)
                                    ->whereIn('role_id', [1,3,4])
                                    ->get();
        return view('Courses.index', compact('classCourse', 'class', 'courses', 'teachers'));
     }


    /**
     * Store the newly created resource in storage.
     */


    /**
     * Display the resource.
     */

    public function blockCourse($id, Request $request)
    {
        $course = Subject::find($id);
        if(! $course) {
            // Alert::error('Error', 'No such course was found');
            Alert()->toast('No such course was found', 'error');
            return back();
        }
        $course->update([
            'status' => $request->input('status', 0)
        ]);

        // Alert::success('Success!', 'Course has been blocked successfully');
        Alert()->toast('Course has been blocked successfully', 'success');
        return back();
    }

    public function unblockCourse(Request $request, $id)
    {
        $course = Subject::find($id);
        if(! $course) {
            // Alert::error('Error', 'No such course was found');
            Alert()->toast('No such course was found', 'error');
            return back();
        }
        $course->update([
            'status' => $request->input('status', 1)
        ]);

        // Alert::success('Success!', 'Course has been unblocked successfully');
        Alert()->toast('Course has been unblocked successfully', 'success');
        return back();
    }

    public function assign($id)
    {
        $user = Auth::user();
        $classCourse = class_learning_courses::query()
                                            ->join('subjects', 'subjects.id', '=', 'class_learning_courses.course_id')
                                            ->join('grades', 'grades.id', '=', 'class_learning_courses.class_id')
                                            ->join('teachers', 'teachers.id', '=', 'class_learning_courses.teacher_id')
                                            ->leftJoin('users', 'users.id', '=', 'teachers.user_id')
                                            ->select(
                                                'class_learning_courses.*',
                                                'grades.class_name', 'subjects.course_name', 'users.first_name', 'users.last_name',
                                                'teachers.id as teacherId'
                                            )
                                            ->find($id);
        if(! $classCourse) {
            // Alert::error('Error', 'No such course was found');
            Alert()->toast('No such course was found', 'error');
            return back();
        }
        $teachers = Teacher::query()->join('users', 'users.id', '=', 'teachers.user_id')
                                    ->select('teachers.*', 'users.first_name', 'users.last_name')
                                    ->where('teachers.id', '!=', $classCourse->teacherId)
                                    ->where('teachers.role_id', '!=', 2)
                                    ->where('teachers.school_id', $user->school_id)
                                    ->get();

        return view('Courses.admin-edit', compact('classCourse', 'teachers'));

    }

    public function assignedTeacher(Request $request, $id)
    {
        $class_course = class_learning_courses::find($id);
        if(! $class_course) {
            // Alert::error('Error!', 'No such course was found');
            Alert()->toast('No such course was found', 'error');
            return back();
        }

        $class_course->update([
            'teacher_id' => $request->teacher_id
        ]);
        // Alert::success('Success!', 'Subject teacher has been saved successfully');
        Alert()->toast('Subject teacher has been saved successfully', 'success');
        return redirect()->route('courses.view.class', $class_course->class_id);
    }

    //teacher remove courses from its lists
    public function removeCourse(Subject $course, Request $request)
    {
        $courses = Subject::findOrFail($course->id);
        $courses->status = $request->input('status', 2);
        $courses->save();
        // Alert::success('Success!', 'Your course has been moved to trash!');
        Alert()->toast('Your course has been moved to trash!', 'success');
        return back();
    }

    //add course by either head teacher or academic teacher usertype
    public function addCourse(Request $request)
    {
        $this->validate($request, [
            'sname' => 'required|string|max:255',
            'scode' => 'required|string|max:10',
            'school_id' => 'integer|exists:schools,id'
        ]);

        $ifExists = Subject::where('course_name', $request->sname)
                            ->where('school_id', $request->school_id)
                            ->exists();

        if($ifExists) {
            // Alert::error('Error!', 'Course details already exists');
            Alert()->toast('Course details already exists', 'error');
            return back();
        }
        $course = Subject::create([
            'course_name' => $request->sname,
            'course_code' => $request->scode,
            'school_id' => $request->school_id
        ]);

        Alert()->toast('Course has been saved successfully', 'success');
        return back();
    }

    public function editCourse($id)
    {
        $course = Subject::find($id);
        return view('Subjects.edit', compact('course'));
    }

    public function updateCourse (Request $request, $id)
    {
        $course = Subject::find($id);

        if(! $course) {
            Alert()->toast('No such course was found', 'error');
            return back();
        }

        $this->validate($request, [
            'sname' => 'required|string|max:255',
            'scode' => 'required|string|max:10'
        ]);

        $course->update([
            'course_name' => $request->sname,
            'course_code' => $request->scode,
        ]);

        Alert()->toast('Course information has been updated successfully', 'success');
        return redirect()->route('courses.index');
    }

    public function assignClassCourse (Request $request)
    {
        $this->validate($request, [
            'course_id' => 'integer|exists:subjects,id',
            'class_id' => 'integer|exists:grades,id',
            'teacher_id' => 'integer|exists:teachers,id',
            'school_id' => 'integer|exists:schools,id'
        ]);

        //check if course already exists to that class
        $hasAlreadyAssigned = class_learning_courses::where('course_id', $request->course_id)
                                                        ->where('class_id', $request->class_id)
                                                        ->where('school_id', $request->school_id)
                                                        ->exists();
        if($hasAlreadyAssigned) {
            Alert()->toast('Course already exists to this class', 'error');
            return back();
        }
        //check for if teacher id has reached to maximum of 3 classes
        $teacher = class_learning_courses::where('teacher_id', $request->teacher_id)
                                            ->where('school_id', $request->school_id)
                                            ->count();
        if($teacher >= 3) {
            Alert()->toast('The selected teacher has already assigned to maximum of 3 subjects to teach', 'error');
            return back();
        }
        $class_course = class_learning_courses::create([
            'course_id' => $request->course_id,
            'class_id' => $request->class_id,
            'teacher_id' => $request->teacher_id,
            'school_id' => $request->school_id
        ]);

        Alert()->toast('Subject teacher saved successfully', 'success');
        return back();
    }

    public function deleteCourse($id)
    {
        $class_course = class_learning_courses::find($id);

        if(! $class_course) {
            Alert()->toast('No such course was found in the records', 'error');
            return back();
        }

        $class_course->delete();

        Alert()->toast('Subject deleted successfully to this class', 'success');
        return back();
    }

    //show lists of courses enrolled by students in a class
    public function viewStudentCourses ($student)
    {
        $decoded = Hashids::decode($student);
        $student = Student::find($decoded[0]);
        // return $student;
        if(! $student) {
            Alert()->toast('No such student was found', 'error');
            return back();
        }

        $class = Grade::find($student->class_id);

        $user = Auth::user();

        if(! $class) {
            Alert()->toast('No class details was found', 'error');
            return back();
        }
        $class_course = class_learning_courses::query()
                                            ->join('subjects', 'subjects.id', '=', 'class_learning_courses.course_id')
                                            ->join('grades', 'grades.id', '=', 'class_learning_courses.class_id')
                                            ->join('teachers', 'teachers.id', '=', 'class_learning_courses.teacher_id')
                                            ->leftJoin('users', 'users.id', '=', 'teachers.user_id')
                                            ->select(
                                                'class_learning_courses.*', 'grades.class_name', 'subjects.course_name', 'subjects.course_code',
                                                'users.first_name', 'users.last_name', 'users.phone'
                                            )
                                            ->where('class_learning_courses.class_id', $class->id)
                                            ->where('class_learning_courses.school_id', $user->school_id)
                                            ->get();
        //fetch class teacher details

        $myClassTeacher = Class_teacher::query()
                                        ->join('teachers', 'teachers.id', '=', 'class_teachers.teacher_id')
                                        ->leftJoin('users', 'users.id', '=', 'teachers.user_id')
                                        ->join('grades', 'grades.id', '=', 'class_teachers.class_id')
                                        ->select('users.first_name', 'users.last_name', 'users.phone', 'users.gender', 'users.image', 'class_teachers.*', 'grades.class_name')
                                        ->where('class_teachers.class_id', $class->id)
                                        ->where('class_teachers.group', $student->group)
                                        ->where('class_teachers.school_id', $user->school_id)
                                        ->get();
        // return ['data' => $myClassTeacher];

        return view('Subjects.student_subject', compact('class', 'class_course', 'myClassTeacher'));
    }

    public function blockAssignedCourse($id, Request $request)
    {
        $class_course = class_learning_courses::find($id);

        if(! $class_course) {
            Alert()->toast('No such class course was found', 'error');
            return back();
        }

        $status = 0;

        $class_course->update([
            'status' => $status,
        ]);

        Alert()->toast('Class course has been blocked successfully', 'success');
        return back();
    }

    public function unblockAssignedCourse($id, Request $request)
    {
        $class_course = class_learning_courses::find($id);

        if(! $class_course) {
            Alert()->toast('No such class course was found', 'error');
            return back();
        }

        $status = 1;

        $class_course->update([
            'status' => $status,
        ]);

        Alert()->toast('Class course has been blocked successfully', 'success');
        return back();
    }
}
