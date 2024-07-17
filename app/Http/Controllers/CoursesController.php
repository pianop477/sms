<?php

namespace App\Http\Controllers;

use App\Models\Grade;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class CoursesController extends Controller
{

    public function index() {
        $user = Auth::user();
        $classes = Grade::where('school_id', '=', Auth::user()->school_id)->orderBy('id', 'ASC')->get();
        $subjects = Subject::where('school_id', '=', $user->school_id)->get();
        return view('Subjects.index', ['subjects' => $subjects, 'classes' => $classes]);
    }
    /**
     * Show the form for creating the resource.
     */

     //view courses by each class============================
     public function classCourses(Grade $class)
     {
        $courses = Subject::query()->join('grades', 'grades.id', '=', 'subjects.class_id')
                                    ->join('teachers', 'teachers.id', 'subjects.teacher_id')
                                    ->leftJoin('users', 'users.id', '=', 'teachers.user_id')
                                    ->select(
                                        'subjects.*',
                                        'grades.class_name', 'grades.class_code',
                                        'users.first_name', 'users.last_name'
                                    )
                                    ->where('subjects.class_id', $class->id)
                                    ->orderBy('subjects.course_name', 'ASC')
                                    ->get();
        return view('Courses.index', compact('courses', 'class'));
     }


    /**
     * Store the newly created resource in storage.
     */
    public function store(Request $request)
    {
        // abort(404);
        $request->validate([
            'name' => 'required|string',
            'code' => 'string|required',
            'class' => 'required|integer',
            // 'teacher_id' => 'required|integer',
        ]);

        $user = Auth::user()->id;
        $teacher = Teacher::where('user_id', '=', $user)->firstOrFail();

        $courseExisting = Subject::where('course_name', '=', $request->name)
                                    ->where('course_code', '=', $request->code)
                                    ->where('class_id', '=', $request->class)
                                    ->where('school_id', '=', Auth::user()->school_id)
                                    ->exists();
        if($courseExisting) {
            Alert::error('Error', 'Same Course Details already Exists');
            return back();
        }
        $courses = new Subject();
        $courses->course_name = $request->name;
        $courses->course_code = $request->code;
        $courses->class_id = $request->class;
        $courses->teacher_id = $teacher->id;
        $courses->school_id = Auth::user()->school_id;
        $courses->save();
        Alert::success('Success!', 'Course saved successfully');
        return back();
    }

    /**
     * Display the resource.
     */
    public function deleteCourse($course)
    {
        //
        $courses = Subject::findOrFail($course);
        // return $courses;
        $courses->delete();
        Alert::success('Success!', 'Course deleted successfully');
        return back();
    }

    /**
     * Show the form for editing the resource.
     */
    public function edit(Subject $course)
    {
        //
        $courses = Subject::query()->join('grades', 'grades.id', '=', 'subjects.class_id')
                                    ->select(
                                        'subjects.*', 'grades.class_name'
                                    )->findOrFail($course->id);
        $classes = Grade::where('school_id', '=', Auth::user()->school_id)->where('status', '=', 1)->orderBy('id', 'ASC')->get();
        return view('Courses.edit', compact('course', 'courses', 'classes'));
    }

    /**
     * Update the resource in storage.
     */
    public function update(Request $request, $courses)
    {
        //
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:4',
            // 'class'  => 'required|integer|exists:grades,id',
        ]);
        $user = Auth::user()->id;
        $teacher = Teacher::where('user_id', '=', $user)->firstOrFail();
        $course = Subject::findOrFail($courses);
        $course->course_name = $request->name;
        $course->course_code = $request->code;
        // $course->class_id = $request->class;
        $course->teacher_id = $teacher->id;
        $course->save();
        Alert::success('Success!', 'Course details Updated successfully');
        return redirect()->route('home');
    }

    /**
     * Remove the resource from storage.
     */
    public function destroy($course)
    {
        // abort(404);
        $courses = Subject::findOrFail($course);
        $courses->delete();
        Alert::success('Success!', 'Courses deleted permanent successfully');
        return back();
    }

    public function blockCourse($course, Request $request)
    {
        $courses = Subject::findOrFail($course);
        $courses->status = $request->input('status', 0);
        $courses->save();
        Alert::success('Success!', 'Course has been blocked successfully');
        return back();
    }

    public function unblockCourse(Request $request, $course)
    {
        $courses = Subject::findOrFail($course);
        $courses->status = $request->input('status', 1);
        $courses->save();
        Alert::success('Success!', 'Course has been unblocked successfully');
        return back();
    }

    public function assign($course)
    {
        $courses = Subject::query()->join('grades', 'grades.id', '=', 'subjects.class_id')
                                    ->join('teachers', 'teachers.id', '=', 'subjects.teacher_id')
                                    ->leftJoin('users', 'users.id', '=', 'teachers.user_id')
                                    ->select('subjects.*', 'grades.class_name', 'teachers.id as teacher_id', 'users.first_name', 'users.last_name')
                                    ->findOrFail($course);
        $teachers = Teacher::query()->join('users', 'users.id', '=', 'teachers.user_id')
                                    ->select('teachers.id', 'users.first_name', 'users.last_name')
                                    ->where('teachers.status', '=', 1)
                                    ->where('teachers.school_id', '=', Auth::user()->school_id)
                                    ->orderBy('users.first_name', 'ASC')
                                    ->get();
        return view('Courses.admin-edit', ['courses' => $courses, 'teachers' => $teachers]);
    }

    public function assignedTeacher(Request $request, $courses)
    {
        $request->validate([
            'teacher' => 'required|integer|exists:teachers,id'
        ]);

        $course = Subject::findOrFail($courses);
        $course->teacher_id = $request->teacher;
        $course->status = $request->input('status', 1);
        $course->save();
        Alert::success('Success!', 'Subject Teacher updated successfully');
        return redirect()->route('courses.view.class', $course->class_id);

    }

    //teacher remove courses from its lists
    public function removeCourse(Subject $course, Request $request)
    {
        $courses = Subject::findOrFail($course->id);
        $courses->status = $request->input('status', 2);
        $courses->save();
        Alert::success('Success!', 'Your course has been moved to trash!');
        return back();
    }
}
