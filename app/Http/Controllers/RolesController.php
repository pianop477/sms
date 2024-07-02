<?php

namespace App\Http\Controllers;

use App\Models\Class_teacher;
use App\Models\Grade;
use App\Models\Role;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use RealRashid\SweetAlert\Facades\Alert;

class RolesController extends Controller
{
    /**
     * Show the form for creating the resource.
     */
    public function index ($class)
    {
        $teachers = Teacher::query()->join('users', 'users.id', '=', 'teachers.user_id')
                                    ->select('teachers.*', 'users.first_name as teacher_first_name', 'users.last_name as teacher_last_name', 'users.phone as teacher_phone', 'users.email as teacher_email')
                                    ->where('teachers.status', '=', 1)
                                    ->where('users.status', '=', 1)
                                    ->where('teachers.school_id', '=', Auth::user()->school_id)
                                    ->orderBy('users.first_name')
                                    ->get();
        $classes = Grade::findOrFail($class);
        $classTeacher = Class_teacher::query()->join('grades', 'grades.id', '=', 'class_teachers.class_id')
                                            ->join('teachers', 'teachers.id', '=', 'class_teachers.teacher_id')
                                            ->join('users', 'users.id', '=', 'teachers.user_id')
                                            ->select(
                                                'class_teachers.*',
                                                'teachers.*',
                                                'grades.class_name', 'grades.class_code',
                                                'users.first_name as teacher_first_name', 'users.last_name as teacher_last_name',
                                                'users.email as teacher_email', 'users.phone as teacher_phone',
                                            )->where('class_teachers.school_id', '=', Auth::user()->school_id)
                                            ->where('class_id', '=', $classes->id)
                                            ->orderBy('class_teachers.group', 'ASC')
                                            ->get();
        return view('Roles.classTeacher', ['teachers' => $teachers, 'classes' => $classes, 'classTeacher' => $classTeacher]);
    }
    public function create(): never
    {
        abort(404);
    }

    /**
     * Store the newly created resource in storage.
     */
    public function store(Request $request, $classes)
    {
        // abort(404);
        $request->validate([
            'teacher' => 'required',
            'group' => 'required'
        ]);

        $ifExisting = Class_teacher::where('teacher_id', '=', $request->teacher)
                                    ->where('school_id', '=', Auth::user()->school_id)
                                    ->exists();
        if($ifExisting) {
            Alert::error('Error', 'Selected Teacher already assigned in another class');
            return back();
        }

        $class = Grade::findOrFail($classes);
        // return $class->id;
        $assignedTeacher = new Class_teacher();
        $assignedTeacher->class_id = $class->id;
        $assignedTeacher->teacher_id = $request->teacher;
        $assignedTeacher->group = $request->group;
        $assignedTeacher->school_id = $request->school_id;
        $assignedTeacher->save();

        Alert::success('Success', 'Class Teacher assigned Successfully');
        return back();

    }

    /**
     * Display users resource corresponding to school.
     */
    public function userPassword()
    {
        // Get the school_id of the authenticated user
        $schoolId = Auth::user()->school_id;

        // Retrieve users with the specified school_id and usertype 3 or 4
        $users = User::where('school_id', $schoolId)
                    ->where(function ($query) {
                        $query->where('usertype', 3)
                            ->orWhere('usertype', 4);
                    })
                    ->orderBy('first_name')
                    ->get();

        // Return the view with the users data
        return view('Roles.users', ['users' => $users]);
    }

    //update password as password reset to default password============
    public function resetPassword (Request $request, $user)
    {
        $users = User::findOrFail($user);
        $users->password = Hash::make($request->input('password', 'shule123'));
        $users->save();
        Alert::success('Success!', 'Password reset successfully');
        return back();
    }

    /**
     * Show the form for editing the resource.
     */
    public function edit($teacher)
    {
        //
        $teacherId = Teacher::findOrFail($teacher);
        $classTeacher = Class_teacher::query()->join('grades', 'grades.id', '=', 'class_teachers.class_id')
                                            ->join('teachers', 'teachers.id', '=', 'class_teachers.teacher_id')
                                            ->leftJoin('users', 'users.id', '=', 'teachers.user_id')
                                            ->select(
                                                'class_teachers.*',
                                                'grades.id as class_id', 'grades.class_name', 'grades.class_code',
                                                'users.first_name', 'users.last_name', 'teachers.id as teacher_id'
                                            )
                                            ->where('class_teachers.teacher_id', '=', $teacherId->id)
                                            ->firstOrFail();
    $teachers = Teacher::query()->join('users', 'users.id', '=', 'teachers.user_id')
                                ->select('teachers.*', 'users.first_name', 'users.last_name')
                                ->where('teachers.status', '=', 1)
                                ->where('teachers.school_id', '=', Auth::user()->school_id)->get();
        // return $classTeacher;
        return view('Roles.edit', ['classTeacher' => $classTeacher, 'teachers' => $teachers]);
    }

    /**
     * Update the resource in storage.
     */
    public function update(Request $request, $classTeacher)
    {
        $request->validate(['teacher' => 'required|integer|exists:teachers,id']);

        $class_teacher = Class_teacher::findOrFail($classTeacher);
        $ifExisting = Class_teacher::where('teacher_id', '=', $request->teacher)
                                    ->where('school_id', Auth::user()->school_id)->exists();
        if($ifExisting) {
            Alert::error('Error!', 'Selected Teacher already assigned in another class');
            return back();
        }
        // return $class_teacher;
        $class_teacher->teacher_id = $request->teacher;
        $class_teacher->save();
        Alert::success('Success!', 'Class teacher  Changed successfully');
        return redirect()->route('Class.Teachers', $class_teacher->class_id);
    }

    /**
     * Remove the resource from storage.
     */
    public function destroy($teacher)
    {
        // abort(404);
        $teachers = Teacher::findOrFail($teacher);
        // return $teachers;
        $class_teacher = Class_teacher::where('teacher_id', '=', $teachers->id)->firstOrFail();
        $class_teacher->delete();
        Alert::success('Success!', "Class teacher removed successfully");
        return back();
    }

    public function updateRoles()
    {
        $users = Teacher::query()->join('users', 'users.id', '=', 'teachers.user_id')
                                ->join('roles', 'roles.id', '=', 'teachers.role_id')
                                ->select(
                                    'teachers.*', 'users.first_name', 'users.last_name',
                                    'roles.role_name'
                                )
                                ->where('teachers.school_id', Auth::user()->school_id)
                                ->where('teachers.role_id', '!=', 1)
                                ->get();
        $roles = Role::all();
        $teachers = Teacher::query()->join('users', 'users.id', '=', 'teachers.user_id')
                                    ->select(
                                        'teachers.*', 'users.first_name', 'users.last_name',
                                    )
                                    ->where('teachers.school_id', '=', Auth::user()->school_id)
                                    ->get();
        return view('Roles.index', compact('users', 'roles', 'teachers'));
    }

    public function assignRole(Teacher $teacher, Request $request)
    {
        if(empty($request->teacher)) {
            return "no teacher selected";
        } else {
            return $teacher;
        }
    }
}
