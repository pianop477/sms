<?php

namespace App\Http\Controllers;

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

class TeachersController extends Controller
{

    public function index() {
        $userLogged = Auth::user();
        $teachers = Teacher::query()
                            ->join('users', 'users.id', '=', 'teachers.user_id')
                            ->join('schools', 'schools.id', '=', 'teachers.school_id')
                            ->select('teachers.*', 'users.first_name', 'users.last_name', 'users.gender', 'users.phone', 'users.email')
                            ->where('teachers.school_id', '=', $userLogged->school_id)
                            ->orderBy('users.first_name', 'ASC')
                            ->get();
        return view('Teachers.index', ['teachers' => $teachers]);
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
    public function store(Request $request)
    {
        // abort(404);
        $request->validate([
            'fname' => 'required|string|max:255',
            'lname' => 'required|string|max:255',
            'email' => 'required|string|unique:users,email',
            'gender' => 'required|string',
            'dob' => 'required',
            'phone' => 'required|string|max:10|min:10',
            'qualification' => 'required',
            'street' => 'required|string',
            'joined' => 'required'
        ]);

        $existingRecords = Teacher::where('dob', '=', $request->dob)
                                    ->where('qualification', '=', $request->qualification)
                                    ->where('address', '=', $request->street)
                                    ->where('school_id', '=', Auth::user()->school_id)
                                    ->exists();
                if($existingRecords){
                    // return back()->with('error', 'Teacher with the same records already exist in our records');
                    Alert::error('Error', 'Teacher with the same records already exist in our records');
                    return back();
                }

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

        $teachers = new Teacher();
        $teachers->user_id = $users->id;
        $teachers->school_id = $users->school_id;
        $teachers->dob = $request->dob;
        $teachers->qualification = $request->qualification;
        $teachers->address = $request->street;
        $teachers->joined = $request->joined;
        $teachers->save();
        Alert::success('Success', 'Teacher records saved successfully');
        return back();

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
                ->select('teachers.*', 'users.first_name', 'users.last_name', 'users.gender', 'users.phone', 'users.usertype', 'users.image', 'schools.school_reg_no', 'schools.school_name')
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

            $validated = $request->validate([
                'fname' => 'required|string|max:255',
                'lname' => 'required|string|max:255',
                'dob' => 'required',
                'phone' => 'required|string|max:10|min:10',
                'qualification' => 'required',
                'street' => 'required|string',
                'gender' => 'required',
                'joined_at' => 'required',
                'image' => 'nullable|image|max:2048',
            ]);

            try {
                $teacher = Teacher::findOrFail($teachers);

                $user = User::findOrFail($teacher->user_id);

                $user->first_name = $request->fname;
                $user->last_name = $request->lname;
                $user->phone = $request->phone;
                $user->gender = $request->gender;

                if($request->hasFile('image')) {
                    // Log::info('Image upload detected');
                    $image = $request->file('image');
                    $imageName = time() . '.' . $image->getClientOriginalExtension();
                    $imageDestinationPath = public_path('assets/img/profile');

                    // Ensure the directory exists
                    if (!file_exists($imageDestinationPath)) {
                        mkdir($imageDestinationPath, 0775, true);
                    }

                    // Move the file
                    $image->move($imageDestinationPath, $imageName);

                    // Save the file name to the database
                    $user->image = $imageName;
                }

                if ($user->save()) {
                    $teacher->dob = $request->dob;
                    $teacher->address = $request->street;
                    $teacher->joined = $request->joined_at;
                    $teacher->qualification = $request->qualification;

                    if ($teacher->save()) {
                        Alert::success('Success', 'Teacher records updated successfully');
                        return back();
                    } else {
                        Alert::error('Error', 'Failed to updated teacher records');
                        return back();
                    }
                } else {
                    Alert::error('Error', 'Failed to update teachers records');
                    return back();
                }
            } catch (\Exception $e) {
                Alert::error('Error', 'An error occured: ' . $e->getMessage());
                return back();
            }
        }

    public function updateStatus(Request $request, $teacher)
    {
        // Find the teacher record
        $teacher = Teacher::findOrFail($teacher);

        // Find the associated user record
        $user = User::findOrFail($teacher->user_id);

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
        Alert::success('Success', 'Teacher status updated successfully');

        // Redirect back
        return back();
    }

    public function restoreStatus(Request $request, $teacher) {
        $teachers = Teacher::findOrFail($teacher);
        $user = User::findOrFail($teachers->user_id);
        $user->status = $request->input('status', 1);
        if($user->save()) {
            $teachers->status = $request->input('status', 1);

            if($teachers->save()) {
                Alert::success('Success', 'Teacher unblocked successfully');
                return back();
            }
        }
    }

    public function destroy($teacher)
    {
        // Find the teacher record or fail
        $teacher = Teacher::findOrFail($teacher);

        // Find the associated user or fail
        $user = User::findOrFail($teacher->user_id);

        // Check the image path
        $userImgPath = public_path('assets/img/profile/' . $user->image);

        // Begin a database transaction
        DB::beginTransaction();

        try {
            // Delete related records in class_teachers table
            DB::table('class_teachers')->where('teacher_id', $teacher->id)->delete();

            // Delete teacher's record
            $teacher->delete();

            // Check if the image file exists and is not a default image, then delete it
            $defaultImages = ['avatar.jpg', 'female-avatar.jpg'];
            if (file_exists($userImgPath) && !in_array(basename($userImgPath), $defaultImages)) {
                unlink($userImgPath);
            }

            // Delete user's record
            $user->delete();

            // Commit the transaction
            DB::commit();

            Alert::success('Success', 'Teacher records deleted successfully');
        } catch (\Exception $e) {
            // Rollback the transaction if there's an error
            DB::rollBack();

            Alert::error('Error', 'Failed to delete teacher records');
        }

        return back();
    }

}
