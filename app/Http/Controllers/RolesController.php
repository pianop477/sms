<?php

namespace App\Http\Controllers;

use App\Events\PasswordResetEvent;
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

     //shows class teachers lists ======================================
    public function index ($class)
    {
        $teachers = Teacher::query()->join('users', 'users.id', '=', 'teachers.user_id')
                                    ->select('teachers.*', 'users.first_name as teacher_first_name', 'users.last_name as teacher_last_name', 'users.phone as teacher_phone', 'users.email as teacher_email')
                                    ->where('teachers.status', '=', 1)
                                    ->where('users.status', '=', 1)
                                    ->where('teachers.school_id', '=', Auth::user()->school_id)
                                    ->where('role_id', '!=', 2)
                                    ->where('role_id', '!=', 3)
                                    ->where('role_id', '!=', 4)
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

     //register / assign class teachers ===============================================
     public function store(Request $request, $classes)
     {
         $this->validate($request, [
             'teacher' => 'integer|exists:teachers,id',
             'group' => 'required|string|max:1',
         ]);

         // Check if the selected teacher is already assigned to another class in the same school
         $ifExisting = Class_teacher::where('teacher_id', $request->teacher)
                                     ->where('school_id', Auth::user()->school_id)
                                     ->exists();
         if ($ifExisting) {
             Alert::error('Error', 'Selected Teacher already assigned in another class');
             return back();
         }

         // Check if the selected teacher has a conflicting role
         $ifTeacherHasRole = Teacher::where('id', $request->teacher)
                                    ->whereIn('role_id', [2, 3])
                                    ->exists();
         if ($ifTeacherHasRole) {
             Alert::error('Error!', 'Selected teacher has been assigned another role, cannot be a class teacher!');
             return back();
         }

         // Check if the maximum number of teachers is reached for the same class_id and group
         $teacherCount = Class_teacher::where('class_id', $classes)
                                       ->where('group', $request->group)
                                       ->where('school_id', Auth::user()->school_id)
                                       ->count();
         if ($teacherCount >= 2) {
             Alert::error('Error', 'Maximum number of class teachers has reached to 2');
             return back();
         }

         // Assign the class teacher
         $class = Grade::findOrFail($classes);
         $assignedTeacher = new Class_teacher();
         $assignedTeacher->class_id = $class->id;
         $assignedTeacher->teacher_id = $request->teacher;
         $assignedTeacher->group = $request->group;
         $assignedTeacher->school_id = Auth::user()->school_id;
         $assignedTeacher->save();

         // Update the teacher's role to 4
         $teacher = Teacher::findOrFail($request->teacher);
         $teacher->role_id = 4;
         $teacher->save();

         Alert::success('Success', 'Class Teacher assigned Successfully');
         return back();
     }

    /**
     * Display users resource corresponding to school.
     */

     //head teacher / manager to reset the its users password==============================
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

        $users->password = Hash::make($request->input('password', 'shule@2024'));
        $users->save();
        //dispatch event to logout user after password reset
        event(new PasswordResetEvent($user));
        Alert::success('Success!', 'Password reset successfully');
        return back();
    }

    /**
     * Show the form for editing the resource.
     */


     //edit class teachers detail if you want to change class teacher name ============================
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
                                            ->where('class_teachers.school_id', $teacherId->school_id)
                                            ->firstOrFail();
    $teachers = Teacher::query()->join('users', 'users.id', '=', 'teachers.user_id')
                                ->select('teachers.*', 'users.first_name', 'users.last_name')
                                ->where('teachers.status', '=', 1)
                                ->whereNotIn('teachers.role_id', [2, 3])
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

        $ifTeacherHasRole = Teacher::where('role_id', '=', 2)
                                    ->where('role_id', '=', 3)
                                    ->exists();
        if($ifTeacherHasRole) {
            Alert::error('Error!', 'Selected teacher has been assigned another role, cannot be a class teacher');
            return back();
        }

        // Get the current teacher assigned to this class
        $currentTeacherId = $class_teacher->teacher_id;

        // Update the class_teacher record
        $class_teacher->teacher_id = $request->teacher;
        $class_teacher->save();

        // Update the new teacher's role to 4
        $newTeacher = Teacher::findOrFail($request->teacher);
        $newTeacher->role_id = 4;
        $newTeacher->save();

        // Check if the old teacher is no longer assigned to any classes and reset their role_id if necessary
        $isCurrentTeacherAssigned = Class_teacher::where('teacher_id', $currentTeacherId)->exists();
        if (!$isCurrentTeacherAssigned) {
            $oldTeacher = Teacher::findOrFail($currentTeacherId);
            $oldTeacher->role_id = 1;
            $oldTeacher->save();
        }

        Alert::success('Success!', 'Class teacher Changed successfully');
        return redirect()->route('Class.Teachers', $class_teacher->class_id);
    }

    /**
     * Remove the resource from storage.
     */
    public function destroy($teacher)
    {
        $teachers = Teacher::findOrFail($teacher);
        $class_teacher = Class_teacher::where('teacher_id', '=', $teachers->id)->firstOrFail();
        $class_teacher->delete();

        $currentTeacherId = $teachers->id;
        $isCurrentTeacherAssigned = Class_teacher::where('teacher_id', $currentTeacherId)->exists();

        // Update role_id to 1 only if the teacher is not assigned to any other class
        if (!$isCurrentTeacherAssigned) {
            $teachers->role_id = 1;
            $teachers->save();
        }

        Alert::success('Success!', 'Class teacher removed successfully');
        return back();
    }


    public function updateRoles()
    {
        $users = Teacher::query()->join('users', 'users.id', '=', 'teachers.user_id')
                                ->join('roles', 'roles.id', '=', 'teachers.role_id')
                                ->select(
                                    'teachers.*', 'users.first_name', 'users.last_name', 'users.usertype', 'users.gender', 'users.phone', 'users.email',
                                    'roles.role_name'
                                )
                                ->where('teachers.school_id', Auth::user()->school_id)
                                ->where('teachers.status', 1)
                                ->orderBy('users.first_name')
                                ->paginate(6);
        $roles = Role::where('role_name', '!=', 'class teacher')->where('role_name', '!=', 'teacher')->orderBy('role_name')->get();
        $teachers = Teacher::query()->join('users', 'users.id', '=', 'teachers.user_id')
                                    ->select(
                                        'teachers.*', 'users.first_name', 'users.last_name', 'users.usertype',
                                    )
                                    ->where('teachers.school_id', '=', Auth::user()->school_id)
                                    ->where('teachers.status', 1)
                                    ->get();
        return view('Roles.index', compact('users', 'roles', 'teachers'));
    }

    public function assignRole(Teacher $user)
    {
        $teachers = Teacher::query()->join('users', 'users.id', '=', 'teachers.user_id')
                                    ->select('teachers.*', 'users.first_name', 'users.last_name', 'users.usertype')
                                    ->findOrFail($user->id);
        $roles = Role::where('role_name', '!=', 'class teacher')->orderBy('role_name')->get();
        return view('Roles.assign_roles', compact('user', 'teachers', 'roles'));
    }

    public function AssignNewRole(Teacher $user, Request $request)
    {
        $request->validate([
            'role' => 'required|exists:roles,id',
        ]);

        $userId = Auth::user()->id;
        if($user->user_id == $userId) {
            Alert::error('Error!', 'Permission denied, you cannot change your role');
            return back();
        }
        $user->role_id = $request->role; // Assuming 'role_id' is the correct field in the 'teachers' table
        $user->save();

        Alert::success('Success!', 'Role has been assigned successfully');
        return redirect()->route('roles.updateRole');
    }

}
