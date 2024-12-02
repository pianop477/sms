<?php

namespace App\Http\Controllers;

use App\Models\Class_teacher;
use App\Models\school;
use App\Models\Teacher;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\PDF as PDF;

class TeachersController extends Controller
{

    public function showTeachersList() {
        $userLogged = Auth::user();
        $teachers = Teacher::query()
                            ->join('users', 'users.id', '=', 'teachers.user_id')
                            ->join('schools', 'schools.id', '=', 'teachers.school_id')
                            ->select('teachers.*', 'users.first_name', 'users.last_name', 'users.gender', 'users.phone', 'users.email')
                            ->where('teachers.school_id', '=', $userLogged->school_id)
                            ->where(function ($query) {
                                $query->where('teachers.status', 1)
                                    ->orWhere('teachers.status', 0);
                            })
                            ->orderBy('users.first_name', 'ASC')
                            ->get();
        return view('Teachers.index', ['teachers' => $teachers]);
    }

    public function export ()
    {
        $userLogged = Auth::user();
        $teachers = Teacher::query()
                            ->join('users', 'users.id', '=', 'teachers.user_id')
                            ->join('schools', 'schools.id', '=', 'teachers.school_id')
                            ->select('teachers.*', 'users.first_name', 'users.last_name', 'users.gender', 'users.phone', 'users.email',
                            'schools.school_name', 'schools.school_reg_no')
                            ->where('teachers.school_id', '=', $userLogged->school_id)
                            ->where(function ($query) {
                                $query->where('teachers.status', 1)
                                    ->orWhere('teachers.status', 0);
                            })
                            ->orderBy('users.first_name', 'ASC')
                            ->get();
        $pdf = \PDF::loadView('Teachers.teachers_pdf', compact('teachers'));
         return $pdf->download('teachers.pdf');
    }
    /**
     * Show the form for creating the resource.
     */
    public function create()
    {
        // abort(404);
        // $school_id = Auth::user()->school_id;
        return view('Teachers.create');
    }

    /**
     * Store the newly created resource in storage.
     */
    public function registerTeachers(Request $request)
    {
        $validatedData = $request->validate([
            'fname' => 'required|string|max:255',
            'lname' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users,email',
            'gender' => 'required|string|max:255',
            'dob' => 'required|date|date_format:Y-m-d',
            'phone' => 'required|string|min:10|max:15',
            'qualification' => 'required|integer|max:20',
            'street' => 'required|string|max:255',
            'joined' => 'required|date_format:Y'
        ]);

        Log::info('Validation passed', $validatedData);

        $existingRecords = Teacher::where('dob', $request->dob)
            ->where('qualification', $request->qualification)
            ->where('address', $request->street)
            ->where('school_id', Auth::user()->school_id)
            ->exists();

        if ($existingRecords) {
            Log::info('Duplicate record detected for DOB: ' . $request->dob);

            Alert::error('Error', 'Teacher with the same records already exists in our records');
            return back();
        }

        try {
            DB::beginTransaction();

            // Create User
            $users = new User();
            $users->first_name = $request->fname;
            $users->last_name = $request->lname;
            $users->email = $request->email;
            $users->phone = $request->phone;
            $users->gender = $request->gender;
            $users->usertype = $request->usertype;
            $users->password = Hash::make($request->password);
            $users->school_id = $request->school_id;
            $users->save();

            Log::info('User created successfully', ['user_id' => $users->id]);

            // Create Teacher
            $teachers = new Teacher();
            $teachers->user_id = $users->id;
            $teachers->school_id = $users->school_id;
            $teachers->dob = $request->dob;
            $teachers->qualification = $request->qualification;
            $teachers->address = $request->street;
            $teachers->joined = $request->joined;
            $teachers->member_id = $this->getMemberId();
            $teachers->save();

            Log::info('Teacher record saved successfully', ['teacher_id' => $teachers->id]);

            DB::commit();
            Alert::success('Success', 'Teacher records saved successfully');
            return back();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saving teacher records: ' . $e->getMessage());

            Alert::error('Error', 'Something went wrong. Please try again.');
            return back();
        }
    }

    protected function getMemberId ()
    {
        do {
            // Generate a random 4-digit number between 1000 and 9999
            $memberIdNumber = mt_rand(1000, 9999);

            // Check if this admission number already exists
        } while (Teacher::where('member_id', $memberIdNumber)->exists());

        return $memberIdNumber; // Return the unique admission number
    }

    /**
     * Display the resource.
     */
    public function showProfile($teacher)
        {
            // Find the teacher by ID
            $teacher = Teacher::findOrFail($teacher);

            // Join the teacher with the users table and get the necessary fields
            $teachers = Teacher::query()
                ->join('users', 'users.id', '=', 'teachers.user_id')
                ->join('schools', 'schools.id', '=', 'teachers.school_id')
                ->join('roles', 'roles.id', '=', 'teachers.role_id')
                ->select(
                    'teachers.*', 'users.first_name', 'users.last_name', 'users.gender',
                    'users.phone', 'users.usertype', 'users.image', 'schools.school_reg_no',
                    'schools.school_name', 'roles.role_name')
                ->where('teachers.id', '=', $teacher->id)
                ->where('teachers.school_id', '=', Auth::user()->school_id)
                ->firstOrFail();

            return view('Teachers.show', ['teachers' => $teachers]);
        }


    /**
     * Show the form for editing the resource.
     */
    public function edit()
    {
        //
    }

    /**
     * Update the resource in storage.
     */

     public function updateTeachers(Request $request, $teachers)
     {
        try {
            // Log the start of the method
            Log::info('Update Teachers method started', ['teacher_id' => $teachers]);

            // Validation
            $validated = $request->validate([
                'fname' => 'required|string|max:255',
                'lname' => 'required|string|max:255',
                'dob' => 'required|date|date_format:Y-m-d',
                'phone' => 'required|string|min:10|max:15',
                'qualification' => 'required|integer|max:20',
                'street' => 'required|string|max:255',
                'gender' => 'required|max:20',
                'joined_at' => 'required|date_format:Y',
                'image' => 'nullable|image|max:2048',
            ]);

            Log::info('Validation passed', $validated);

            // Find teacher and user
            $teacher = Teacher::findOrFail($teachers);
            $user = User::findOrFail($teacher->user_id);

            Log::info('Teacher and user found', ['teacher' => $teacher, 'user' => $user]);

            // Update user
            $user->first_name = $request->fname;
            $user->last_name = $request->lname;
            $user->phone = $request->phone;
            $user->gender = $request->gender;

            // Handle image upload
            if ($request->hasFile('image')) {
                Log::info('Image upload detected');
                $image = $request->file('image');
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('assets/img/profile'), $imageName);
                $user->image = $imageName;
            }

            // Save user
            if ($user->save()) {
                Log::info('User updated successfully');

                // Update teacher
                $teacher->dob = $request->dob;
                $teacher->address = $request->street;
                $teacher->joined = $request->joined_at;
                $teacher->qualification = $request->qualification;

                if ($teacher->save()) {
                    Log::info('Teacher updated successfully');
                    Alert::success('Success', 'Teacher records updated successfully');
                    return back();
                } else {
                    Log::error('Failed to update teacher');
                    Alert::error('Error', 'Failed to update teacher records');
                    return back();
                }
            } else {
                Log::error('Failed to update user');
                Alert::error('Error', 'Failed to update user records');
                return back();
            }
        } catch (\Exception $e) {
            Log::error('An error occurred', ['message' => $e->getMessage()]);
            Alert::error('Error', 'An error occurred: ' . $e->getMessage());
            return back();
        }
     }


    public function updateStatus(Request $request, $teacher)
    {
        // Find the teacher record
        $teacher = Teacher::findOrFail($teacher);
        // return $teacher; user user_id

        // Find the associated user record
        $user = User::findOrFail($teacher->user_id);
        // return $user;

        if(Auth::user()->id == $user->id) {
            Alert::error('Error!', 'You cannot block your own account');
            return back();
        }

        // Retrieve the status from the request, default to 0 if not provided
        $status = $request->input('status', 0);

        // Wrap the updates in a transaction
        DB::transaction(function () use ($user, $teacher, $status) {
            // Update the user status
            $user->status = $status;
            $user->save();

            // Update the teacher status
            $teacher->status = $status;
            $teacher->save();
        });

        // Show success message
        Alert::success('Success', 'Teacher has been blocked successfully');

        // Redirect back
        return back();
    }

    public function restoreStatus(Request $request, $teacher) {
        // Find the teacher record
        $teacher = Teacher::findOrFail($teacher);

        // Find the associated user record
        $user = User::findOrFail($teacher->user_id);


        // Retrieve the status from the request, default to 0 if not provided
        $status = $request->input('status', 1);

        // Wrap the updates in a transaction
        DB::transaction(function () use ($user, $teacher, $status) {
            // Update the user status
            $user->status = $status;
            $user->save();

            // Update the teacher status
            $teacher->status = $status;
            $teacher->save();
        });

        // Show success message
        Alert::success('Success', 'Teacher has been unblocked successfully');

        // Redirect back
        return back();
    }

    public function deleteTeacher($teacher)
    {
        // Find the teacher record or fail
        $teacher = Teacher::findOrFail($teacher);

        // Find the associated user or fail
        $user = User::where('id', $teacher->user_id)->first();
        // return $user;

        //user id logged in
        $userId = Auth::user()->id;

        // Check if the selected teacher is logged in and trying to update their status themselves
        if ($teacher->user_id == $userId) {
            Alert::error('Error!', 'You cannot delete your own account');
            return back();
        }

         //check role of logged user compare with role of deleted user=======
         if($teacher->role_id != 1) {
            Alert::error('Error!', 'You do not have permission to delete this teacher.');
            return back();
         }

        // Check if the user is assigned as a class teacher to any class
        $assignedClassTeacher = Class_teacher::where('teacher_id', $teacher->id)->first();
        if ($assignedClassTeacher) {
            // Delete the class teacher record
            $assignedClassTeacher->delete();
        }

        // Begin a database transaction
        DB::beginTransaction();

        try {
            // Update the status column and role_id in the teachers table
            $teacher->status = 2;
            $teacher->role_id = 1; // Set to NULL
            $teacher->save();

            // Update the status column in the users table
            $user->status = 2;
            $user->save();

            // Commit the transaction
            DB::commit();

            Alert::success('Success', 'Teacher deleted successfully');
        } catch (\Exception $e) {
            // Rollback the transaction if there's an error
            DB::rollBack();

            Alert::error('Error', 'Failed to delete teacher');
        }

        return back();
    }

    public function trashedTeachers ()
    {
        $userLogged = Auth::user();
        $teachers = Teacher::query()
                            ->join('users', 'users.id', '=', 'teachers.user_id')
                            ->join('schools', 'schools.id', '=', 'teachers.school_id')
                            ->select('teachers.*', 'users.first_name', 'users.last_name', 'users.gender', 'users.phone', 'users.email')
                            ->where('teachers.school_id', '=', $userLogged->school_id)
                            ->where('teachers.status', 2)
                            ->orderBy('users.first_name', 'ASC')
                            ->get();
        return view('Teachers.trash', ['teachers' => $teachers]);
    }


}
