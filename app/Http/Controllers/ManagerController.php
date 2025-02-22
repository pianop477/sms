<?php

namespace App\Http\Controllers;

use App\Events\PasswordResetEvent;
use App\Models\Parents;
use App\Models\school;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Grade;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Transport;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use RealRashid\SweetAlert\Facades\Alert;

class ManagerController extends Controller
{

    public function index() {
        $managers = User::where('usertype', '=', 2, 'AND', 'status', '=', 1)->get();
        return view('Schools.create', ['managers' => $managers]);
    }
    /**
     * Show the form for creating the resource.
     */
    public function create(): never
    {
        abort(404);
    }

    /**
     * Store the newly created resource in storage.
     */
    public function store(Request $request)
    {
        // abort(404);
        $request->validate([
            'fname' => 'required|string|max:255',
            'lname' => 'required|string|max:255',
            'email' => 'nullable|string|unique:users,email',
            'phone' => 'required|regex:/^[0-9]{10}$/|unique:users,phone',
            'gender' => 'required|string|max:255',
            'usertype' => 'required',
            'school' => 'required|exists:schools,id',
            'password' => 'required|min:8',
        ]);


        $users = new User();
        $users->first_name = $request->fname;
        $users->last_name = $request->lname;
        $users->email = $request->email;
        $users->phone = $request->phone;
        $users->gender = $request->gender;
        $users->usertype = $request->usertype;
        $users->school_id = $request->school;
        $users->password = Hash::make($request->password);
        $users->school_id = $request->school;
        $saveData = $users->save();

        if($saveData) {
            Alert()->toast('User registered successfully', 'success');
            return back();
        } else {
            Alert()->toast('Something went wrong, try again', 'error');
            return back();
        }
    }

    /**
     * Display the resource.
     */
    public function reset()
    {
        //
        $users = User::query()->join('schools', 'schools.id', '=', 'users.school_id')
                        ->select('users.*', 'schools.school_name', 'schools.school_reg_no')
                        ->where('users.usertype', 2)
                        ->orderBy('users.first_name')
                        ->get();
        return view('Managers.managers_password_reset', compact('users'));
    }

    /**
     * Show the form for editing the resource.
     */
    public function resetPassword(Request $request, $user)
    {
        //
        $users = User::findOrFail($user);
        $users->password = Hash::make($request->input('password', 'shule@2024'));
        $users->save();
        event(new PasswordResetEvent($user));
        Alert()->toast('User Password reset successfully', 'success');
        return back();
    }

    /**
     * Update the resource in storage.
     */
    public function update(Request $request)
    {
        //
    }

    /**
     * Remove the resource from storage.
     */
    public function destroy(): never
    {
        abort(404);
    }

    public function updateStatus($school, Request $request) {
        $user_status = User::where('school_id', $school)->firstOrFail();
        // $school_id = $user_status->school_id;
        $status = $request->input('status', 0);

        // Update the status of the user
        $user_status->status = $status;
        $user_status->update();

        // Update the status of the school associated with the user
        $school_info = School::findOrFail($school);
        $school_info->status = $status;
        $school_info->update();

            // Update the status of all users associated with the school
            User::where('school_id', $school_info->id)->update(['status' => $status]);

            // Update the status of all teachers associated with the school
            Teacher::where('school_id', $school_info->id)->update(['status' => $status]);

            // Update the status of all parents associated with the school
            Parents::where('school_id', $school_info->id)->update(['status' => $status]);

            //update the status of all classes associated with the school
            Grade::where('school_id', $school_info->id)->update(['status' => $status]);

            //update the status of all subjects associated with the school
            Subject::where('school_id', $school_info->id)->update(['status' => $status]);

            //update the status of all transport associated with the school
            Transport::where('school_id', $school_info->id)->update(['status' => $status]);
        if($school_info) {
            Alert()->toast('User Blocked Successfully', 'success');
            return back();
        } else {
            Alert()->toast('Something went wrong, try again', 'error');
            return back();
        }

    }

    public function activateStatus($school, Request $request) {
        $user_status = User::where('school_id', $school)->firstOrFail();
        // $school_id = $user_status->school_id;
        $status = $request->input('status', 1);

        // Update the status of the user
        $user_status->status = $status;
        $user_status->update();

        // Update the status of the school associated with the user
        $school_info = School::findOrFail($school);
        $school_info->status = $status;
        $school_info->update();

            // Update the status of all users associated with the school
            User::where('school_id', $school_info->id)->update(['status' => $status]);

            // Update the status of all teachers associated with the school
            Teacher::where('school_id', $school_info->id)->update(['status' => $status]);

            // Update the status of all parents associated with the school
            Parents::where('school_id', $school_info->id)->update(['status' => $status]);

            //update the status of all classes associated with the school
            Grade::where('school_id', $school_info->id)->update(['status' => $status]);

            //update the status of all subjects associated with the school
            Subject::where('school_id', $school_info->id)->update(['status' => $status]);

            //update the status of all transport associated with the school
            Transport::where('school_id', $school_info->id)->update(['status' => $status]);

        if($school_info) {
            Alert()->toast('User Unblocked successfully', 'success');
            return back();
        }
    }

}
