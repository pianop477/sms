<?php

namespace App\Http\Controllers;

use App\Events\PasswordResetEvent;
use App\Models\Class_teacher;
use App\Models\Grade;
use App\Models\Role;
use App\Models\school;
use App\Models\Teacher;
use App\Models\User;
use App\Services\BeemSmsService;
use App\Services\NextSmsService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use RealRashid\SweetAlert\Facades\Alert;
use Vinkla\Hashids\Facades\Hashids;

class RolesController extends Controller
{
    /**
     * Show the form for creating the resource.
     */

     //shows class teachers lists ======================================
    public function index ($class)
    {
        $decoded = Hashids::decode($class);

        $user = Auth::user();
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

        $classes = Grade::findOrFail($decoded[0]);

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
        $decoded = Hashids::decode($classes);
        $class = Grade::findOrFail($decoded[0]);
        $user = Auth::user();

        if($class->school_id != $user->school_id) {
            Alert()->toast('You are not authorized to perform this action', 'error');
            return back();
        }
         $this->validate($request, [
             'teacher' => 'integer|exists:teachers,id',
             'group' => 'required|string|max:1',
         ], [
             'teacher.integer' => 'Please select a valid teacher',
             'teacher.exists' => 'The selected teacher does not exist',
             'group.required' => 'Group is required',
             'group.max' => 'Group must be a single character',
         ]);

         // Check if the selected teacher is already assigned to another class in the same school
         $ifExisting = Class_teacher::where('teacher_id', $request->teacher)
                                     ->where('school_id', Auth::user()->school_id)
                                     ->exists();
         if ($ifExisting) {
             Alert()->toast('Selected Teacher already assigned in another class', 'error');
             return back();
         }

         // Check if the selected teacher has a conflicting role
         $ifTeacherHasRole = Teacher::where('id', $request->teacher)
                                    ->whereIn('role_id', [2, 3])
                                    ->exists();
         if ($ifTeacherHasRole) {
             Alert()->toast('Selected teacher has been assigned another role, cannot be a class teacher!', 'error');
             return back();
         }

         // Check if the maximum number of teachers is reached for the same class_id and group
         $teacherCount = Class_teacher::where('class_id', $class->id)
                                       ->where('group', $request->group)
                                       ->where('school_id', Auth::user()->school_id)
                                       ->count();
         if ($teacherCount >= 2) {
             Alert()->toast('Maximum number of class teachers has reached to 2', 'error');
             return back();
         }

         // Assign the class teacher
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

         Alert()->toast('Class Teacher assigned Successfully', 'success');
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
        try {
            $id = Hashids::decode($user);
            $users = User::findOrFail($id[0]);
            $loggedUser = Auth::user();

            $school = school::findOrFail($loggedUser->school_id);
            if($users->school_id != $loggedUser->school_id) {
                Alert()->toast('You are not authorized to perform this action', 'error');
                return back();
            }

            $users->password = Hash::make($request->input('password', 'shule2025'));
            $users->save();
            //dispatch event to logout user after password reset
            event(new PasswordResetEvent($users->id));

            //notify via SMS after password reset
            $nextSmsService = new NextSmsService();
            $link = "https://shuleapp.tech/Login";

            $senderId = $school->sender_id ?? "SHULE APP";
            $message = "Hello {$users->first_name} \n";
            $message .= "your username: {$users->phone}\n";
            $message .="Password: shule2025\n";
            $message .= "Login at {$link} to change your password.";
            $destination = $this->formatPhoneNumber($users->phone);
            $reference = uniqid();
            $payload = [
                'from' => $senderId,
                'to' => $destination,
                'text' => $message,
                'reference' => $reference,
            ];

            // Log::info("Sending to ". $payload['to']. " Message says ". $payload['text']);

            $response = $nextSmsService->sendSmsByNext($payload['from'], $payload['to'], $payload['text'], $payload['reference']);

            Alert()->toast('Password reset successfully', 'success');
            return back();

        } catch(Exception $e) {
                Alert()->toast($e->getMessage(), 'error');
                return back();
        }
    }


    private function formatPhoneNumber($phone)
    {
        // Remove any non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Ensure the number starts with the country code (e.g., 255 for Tanzania)
        if (strlen($phone) == 9) {
            $phone = '255' . $phone;
        } elseif (strlen($phone) == 10 && substr($phone, 0, 1) == '0') {
            $phone = '255' . substr($phone, 1);
        }

        return $phone;
    }

    /**
     * Show the form for editing the resource.
     */


     //edit class teachers detail if you want to change class teacher name ============================
    public function edit($teacher)
    {
        //
        $id = Hashids::decode($teacher);
        $user = Auth::user();
        $teacherId = Teacher::findOrFail($id[0]);
        if($teacherId->school_id != $user->school_id) {
            Alert()->toast('You are not authorized to perform this action', 'error');
            return back();
        }

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
                                ->whereNotIn('teachers.role_id', [2, 3, 4])
                                ->where('teachers.school_id', '=', Auth::user()->school_id)
                                ->orderBy('users.first_name')
                                ->get();
        // return $classTeacher;
        return view('Roles.edit', ['classTeacher' => $classTeacher, 'teachers' => $teachers]);
    }

    /**
     * Update the resource in storage.
     */
    public function update(Request $request, $classTeacher)
    {
        $id = Hashids::decode($classTeacher);

        $request->validate(['teacher' => 'required|integer|exists:teachers,id']);

        $class_teacher = Class_teacher::findOrFail($id[0]);
        $user = Auth::user();

        if($class_teacher->school_id != $user->school_id) {
            Alert()->toast('You are not authorized to perform this action', 'error');
            return back();
        }

        $ifExisting = Class_teacher::where('teacher_id', '=', $request->teacher)
                                    ->where('school_id', Auth::user()->school_id)->exists();
        if($ifExisting) {
            Alert()->toast('Selected Teacher already assigned in another class', 'error');
            return back();
        }

        $ifTeacherHasRole = Teacher::where('role_id', '=', 2)
                                    ->where('role_id', '=', 3)
                                    ->exists();
        if($ifTeacherHasRole) {
            Alert()->toast('Selected teacher has been assigned another role, cannot be a class teacher', 'error');
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

        Alert()->toast('Class teacher Changed successfully', 'success');
        return redirect()->route('Class.Teachers', $class_teacher->class_id);
    }

    /**
     * Remove the resource from storage.
     */
    public function destroy($teacher)
    {
        $id = Hashids::decode($teacher);
        $user = Auth::user();
        $teachers = Teacher::findOrFail($id[0]);

        if($teachers->school_id != $user->school_id) {
            Alert()->toast('You are not authorized to perform this action', 'error');
            return back();
        }

        $class_teacher = Class_teacher::where('teacher_id', '=', $teachers->id)->firstOrFail();
        $class_teacher->delete();

        $currentTeacherId = $teachers->id;
        $isCurrentTeacherAssigned = Class_teacher::where('teacher_id', $currentTeacherId)->exists();

        // Update role_id to 1 only if the teacher is not assigned to any other class
        if (!$isCurrentTeacherAssigned) {
            $teachers->role_id = 1;
            $teachers->save();
        }

        Alert()->toast('Class teacher removed successfully','success');
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

    public function assignRole($user)
    {
        $id = Hashids::decode($user);
        $authUser = Auth::user();
        $teachers = Teacher::query()->join('users', 'users.id', '=', 'teachers.user_id')
                                    ->join('roles', 'roles.id', '=', 'teachers.role_id')
                                    ->select('teachers.*', 'users.first_name', 'users.last_name', 'users.usertype', 'roles.role_name')
                                    ->findOrFail($id[0]);
        // return $teachers;
        if($teachers->school_id != $authUser->school_id) {
            Alert()->toast('You are not authorized to perform this action', 'error');
            return back();
        }

        $roles = Role::where('role_name', '!=', 'class teacher')->orderBy('role_name')->get();

        return view('Roles.assign_roles', compact('teachers', 'roles'));
    }

    public function AssignNewRole($user, Request $request)
    {
        $id = Hashids::decode($user);

        $userId = Auth::user(); // Logged-in user ID

        $teacher = Teacher::findorFail($id[0]);

        if($teacher->school_id != $userId->school_id) {
            Alert()->toast('You are not authorized to perform this action', 'error');
            return back();
        }

        $request->validate([
            'role' => 'required|exists:roles,id',
        ], [
            'role.required' => 'Please select a role',
            'role.exists' => 'The selected role does not exist',
        ]);


        if ($teacher->user_id == $userId->id) {
            Alert()->toast('Permission denied, you cannot change your role', 'error');
            return back();
        }


        // Check if user already has another role
        if ($teacher->role_id == 2 || $teacher->role_id == 3 || $teacher->role_id == 4) {
            session(['confirm_role_change' => [
                'teacher_id' => $teacher->id,
                'new_role' => $request->role
            ]]);
            return redirect()->route('roles.confirmation');
        }

        // Assign the new role
        $teacher->role_id = $request->role;
        $teacher->save();

        Alert()->toast('Role has been assigned successfully', 'success');
        return redirect()->route('roles.updateRole');
    }

    public function confirmProceed(Request $request)
    {
        $user = Auth::user();
        $teacher = Teacher::findOrFail($request->teacher_id);

        if($teacher->school_id != $user->school_id) {
            Alert()->toast('You are not authorized to perform this action', 'error');
            return back();
        }

        //check if the teacher_id has role 4 as class teacher, delete from classTeachers table first
        if($teacher->role_id == 4) {
            $classTeacher = Class_teacher::where('teacher_id', $teacher->id)->delete();
        }
        $teacher->role_id = $request->new_role;
        $teacher->save();

        session()->forget('confirm_role_change'); // Clear session after confirmation

        Alert()->toast('Role has been assigned successfully', 'success');
        return redirect()->route('roles.updateRole');
    }

    public function extendSession(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['session_expired' => true], 401);
        }

        Session::put('last_activity', time());
        Session::forget('session_warning_shown'); // Reset warning flag

        return response()->json(['success' => true]);
    }


}
