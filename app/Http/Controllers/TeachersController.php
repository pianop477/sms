<?php

namespace App\Http\Controllers;

use App\Events\PasswordResetEvent;
use App\Models\Class_teacher;
use App\Models\school;
use App\Models\Teacher;
use App\Models\User;
use App\Services\BeemSmsService;
use App\Services\NextSmsService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\PDF as PDF;
use Vinkla\Hashids\Facades\Hashids;

class TeachersController extends Controller
{
    protected $beemSmsService;
    protected $nextSmsService;

    public function __construct(BeemSmsService $beemSmsService, NextSmsService $nextSmsService)
    {
        $this->beemSmsService = $beemSmsService;
        $this->nextSmsService = $nextSmsService;
    }


    // Display teachers list in the school ***********************************************************
    public function showTeachersList() {
        $userLogged = Auth::user();
        $teachers = Teacher::query()
                            ->join('users', 'users.id', '=', 'teachers.user_id')
                            ->join('schools', 'schools.id', '=', 'teachers.school_id')
                            ->join('roles', 'roles.id', '=', 'teachers.role_id')
                            ->select('teachers.*', 'users.first_name', 'users.last_name', 'users.gender', 'users.phone', 'users.email', 'roles.role_name',)
                            ->where('teachers.school_id', '=', $userLogged->school_id)
                            ->whereIn('teachers.status', [0,1])
                            ->where('teachers.role_id', '!=', 2)
                            ->orderBy('users.first_name', 'ASC')
                            ->get();
        return view('Teachers.index', ['teachers' => $teachers]);
    }

    // Export teachers list to PDF in the school ***********************************************************
    public function export ()
    {
        $userLogged = Auth::user();
        $teachers = Teacher::query()
                            ->join('users', 'users.id', '=', 'teachers.user_id')
                            ->join('schools', 'schools.id', '=', 'teachers.school_id')
                            ->join('roles', 'roles.id', '=', 'teachers.role_id')
                            ->select('teachers.*', 'users.first_name', 'users.last_name', 'users.gender', 'users.phone', 'users.email',
                            'schools.school_name', 'schools.school_reg_no', 'roles.role_name')
                            ->where('teachers.school_id', '=', $userLogged->school_id)
                            ->whereIn('teachers.status', [0,1])
                            ->orderBy('users.first_name', 'ASC')
                            ->get();
        $pdf = \PDF::loadView('Teachers.teachers_pdf', compact('teachers'));
         return $pdf->stream('teachers.pdf');
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

    //  register new teacher in the school and send sms via  ***********************************************************
    public function registerTeachers(Request $request)
    {
        $validatedData = $request->validate([
            'fname' => 'required|string|max:255',
            'lname' => 'required|string|max:255',
            'email' => 'string|email|unique:users,email',
            'gender' => 'required|string|max:255',
            'dob' => 'required|date|date_format:Y-m-d',
            'phone' => 'required|regex:/^[0-9]{10}$/|unique:users,phone',
            'qualification' => 'required|integer|max:20',
            'street' => 'required|string|max:255',
            'joined' => 'required|date_format:Y',
            'school_id' => 'exists:schools, id'
        ]);

        $user = Auth::user();
        $school = school::findOrFail($user->school_id);

        $existingRecords = User::where('phone', $request->phone)
                    ->where('school_id', Auth::user()->school_id)
                    ->exists();

        if ($existingRecords) {
            // Log::info('Duplicate record detected for DOB: ' . $request->dob);

            Alert()->toast('Teacher with the same records already exists', 'error');
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
            $users->usertype = $request->input('usertype', 3);
            $users->password = Hash::make($request->input('password', 'shule2025'));
            $users->school_id = $user->school_id;
            $users->save();

            // Log::info('User created successfully', ['user_id' => $users->id]);

            // Create Teacher
            $teachers = new Teacher();
            $teachers->user_id = $users->id;
            $teachers->school_id = $user->school_id;
            $teachers->dob = $request->dob;
            $teachers->qualification = $request->qualification;
            $teachers->address = $request->street;
            $teachers->joined = $request->joined;
            $teachers->member_id = $this->getMemberId();
            $teachers->save();

            DB::commit();

            // notify teacher through sms using Beem API *************************************************
            $url = "https://shuleapp.tech";

            $beemSmsService = new BeemSmsService();
            $sourceAddr = $school->sender_id ?? 'shuleApp'; // Get sender ID
            $formattedPhone = $this->formatPhoneNumber($users->phone); // Validate phone before sending

            // Check if phone number is valid after formatting
            if (strlen($formattedPhone) !== 12 || !preg_match('/^255\d{9}$/', $formattedPhone)) {
                    // Alert::error('Invalid phone number format', ['phone' => $formattedPhone]);
                } else {
                    $recipients = [
                        [
                            'recipient_id' => 1,
                            'dest_addr' => $formattedPhone, // Use validated phone number
                        ]
                    ];

                }

                $message = 'Welcome Teacher '. strtoupper($users->first_name) .', to ShuleApp. Your Login Details are; username: {$users->phone}, password: shule2025. Visit {$url} to Login.';
                // $response = $beemSmsService->sendSms($sourceAddr, $message, $recipients);

                // send SMS using nextSMS API ***********************************************
                $nextSmsService = new NextSmsService();
                $destination = $this->formatPhoneNumber($users->phone);

                $payload = [
                    'from' => $school->sender_id ?? "SHULE APP",
                    'to' => $destination,
                    'text' => "Welcome Teacher '. strtoupper($users->first_name) .', to ShuleApp. Your Login Details are; username: {$users->phone}, password: shule2025. Visit {$url} to Login.",
                    'reference' => uniqid(),
                ];

                $response = $nextSmsService->sendSmsByNext($payload['from'], $payload['to'], $payload['text'], $payload['reference']);

                Alert()->toast('Teacher records saved successfully', 'success');
                return redirect()->back();

        } catch (\Exception $e) {
            DB::rollBack();
            Alert()->toast($e->getMessage(), 'error');
            return back();
        }
    }

    // format number according to Beem API documentation ***********************************************************

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

    // prepare unique member id for teacher ***********************************************************
    protected function getMemberId ()
    {

        $user = Auth::user();
        $schoolData = school::find($user->school_id);
        do {
            // Generate a random 4-digit number between 1000 and 9999
            $memberIdNumber = mt_rand(100, 999);

            // Check if this admission number already exists
        } while (Teacher::where('member_id', $memberIdNumber)
                        ->where('school_id', $user->school_id)
                        ->where('status', 1)
                        ->exists());

        return $schoolData->abbriv_code.'-'.$memberIdNumber; // Return the unique admission number
    }

    /**
     * Display the resource.
     */

    //  show teacher profile in the school ***********************************************************
    public function showProfile($teacher)
        {
            $decoded = Hashids::decode($teacher);
            // Find the teacher by ID
            $teacherId = Teacher::findOrFail($decoded[0]);
            $user = Auth::user();

            if($teacher->school_id != $user->school_id) {
                Alert()->toast('You are not authorized to perform this action', 'error');
                return back();
            }
            // Join the teacher with the users table and get the necessary fields
            $teachers = Teacher::query()
                ->join('users', 'users.id', '=', 'teachers.user_id')
                ->join('schools', 'schools.id', '=', 'teachers.school_id')
                ->join('roles', 'roles.id', '=', 'teachers.role_id')
                ->select(
                    'teachers.*', 'users.first_name', 'users.last_name', 'users.gender',
                    'users.phone', 'users.usertype', 'users.image', 'schools.school_reg_no',
                    'schools.school_name', 'roles.role_name')
                ->where('teachers.id', '=', $teacherId->id)
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

    //  update teacher profile in the school ***********************************************************
     public function updateTeachers(Request $request, $teachers)
     {
         // Find teacher and user
         $decoded = Hashids::decode($teachers);
         $teacher = Teacher::findOrFail($decoded[0]);
         $user = User::findOrFail($teacher->user_id);

         $loggedUser = Auth::user();

         if($teacher->school_id != $loggedUser->school_id) {
             Alert()->toast('You are not authorized to perform this action', 'error');
             return back();
         }

         $validated = $request->validate([
            'fname' => 'required|string|max:255',
            'lname' => 'required|string|max:255',
            'dob' => 'required|date|date_format:Y-m-d',
            'phone' => 'required|regex:/^[0-9]{10}$/|unique:users,phone,'.$user->id,
            'qualification' => 'required|integer|max:20',
            'street' => 'required|string|max:255',
            'gender' => 'required|max:20',
            'joined_at' => 'required|date_format:Y',
            'image' => 'nullable|image|mimes:jpg,png,jpeg|max:512',
        ]);


        try {
            // Update user
            $user->first_name = $request->fname;
            $user->last_name = $request->lname;
            $user->phone = $request->phone;
            $user->gender = $request->gender;

            // Handle image upload
            if ($request->hasFile('image')) {
                // Log::info('Image upload detected');
                $image = $request->file('image');
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $imagePath = public_path('assets/img/profile');

                //check for existing file before update new
                if(!empty($user->image)) {
                    $existingImagePath = public_path('assets/img/profile/' . $user->image);
                    if (file_exists($existingImagePath)) {
                        unlink($existingImagePath);
                    }
                }

                $image->move($imagePath, $imageName);
                $user->image = $imageName;
            }

            // Save user
            if ($user->save()) {
                // Log::info('User updated successfully');

                // Update teacher
                $teacher->dob = $request->dob;
                $teacher->address = $request->street;
                $teacher->joined = $request->joined_at;
                $teacher->qualification = $request->qualification;

                if ($teacher->save()) {
                    // Log::info('Teacher updated successfully');
                    Alert()->toast('Teacher records updated successfully', 'success');
                    return back();
                } else {
                    // Log::error('Failed to update teacher');
                    Alert()->toast('Failed to update teacher records', 'error');
                    return back();
                }
            } else {
                // Log::error('Failed to update user');
                Alert()->toast('Failed to update teacher records', 'error');
                return back();
            }
        } catch (\Exception $e) {
            // Log::error('An error occurred', ['message' => $e->getMessage()]);
            Alert()->toast($e->getMessage(), 'error');
            return back();
        }
     }


    //  update teacher status to inactive in the school ***********************************************************
    public function updateStatus(Request $request, $teacher)
    {
        // Find the teacher record
        $decoded = Hashids::decode($teacher);
        $userLogged = Auth::user();
        $teachers = Teacher::findOrFail($decoded[0]);

        if($teachers->school_id != $userLogged->school_id) {
            Alert()->toast('You are not authorized to perform this action', 'error');
            return back();
        }
        // return $teacher; user user_id

        // Find the associated user record
        $user = User::findOrFail($teachers->user_id);
        // return $user;

        if(Auth::user()->id == $user->id) {
            Alert()->toast('You cannot block your own account', 'error');
            return back();
        }

        if($teachers->role_id == 2) {
            Alert()->toast('You do not have permission to block this teacher.', 'error');
            return back();
        }

        // Retrieve the status from the request, default to 0 if not provided
        $status = $request->input('status', 0);

        // Wrap the updates in a transaction
        DB::transaction(function () use ($user, $teachers, $status) {
            // Update the user status
            $user->status = $status;
            $user->save();

            // Update the teacher status
            $teachers->status = $status;
            $teachers->save();
        });
        event(new PasswordResetEvent($user->id));

        // Show success message
        Alert()->toast('Teacher has been blocked successfully', 'success');

        // Redirect back
        return back();
    }

    // update teacher status to active in the school ***********************************************************
    public function restoreStatus(Request $request, $teacher) {
        // Find the teacher record
        $decoded = Hashids::decode($teacher);
        $teachers = Teacher::findOrFail($decoded[0]);

        $userLogged = Auth::user();

        if ($teachers->school_id != $userLogged->school_id) {
            Alert()->toast('You are not authorized to perform this action', 'error');
            return back();
        }

        // Find the associated user record
        $user = User::findOrFail($teachers->user_id);

        // Retrieve the status from the request, default to 0 if not provided
        $status = $request->input('status', 1);

        // Wrap the updates in a transaction
        DB::transaction(function () use ($user, $teachers, $status) {
            // Update the user status
            $user->status = $status;
            $user->save();

            // Update the teacher status
            $teachers->status = $status;
            $teachers->save();
        });

        // Show success message
        Alert()->toast('Teacher has been unblocked successfully', 'success');

        // Redirect back
        return back();
    }

    // delete and move to trash teacher in the school ***********************************************************
    public function deleteTeacher($teacher)
    {
        // Find the teacher record or fail
        $decoded = Hashids::decode($teacher);
        $teachers = Teacher::findOrFail($decoded[0]);

        $userLogged = Auth::user();

        if($teachers->school_id != $userLogged->school_id) {
            Alert()->toast('You are not authorized to perform this action', 'error');
            return back();
        }

        // Find the associated user or fail
        $user = User::where('id', $teachers->user_id)->first();
        // return $user;

        //user id logged in
        $userId = Auth::user()->id;

        // Check if the selected teacher is logged in and trying to update their status themselves
        if ($teachers->user_id == $userId) {
            Alert()->toast('You cannot delete your own account', 'error');
            return back();
        }

         //check role of logged user compare with role of deleted user=======
         if($teachers->role_id == 2) {
            Alert()->toast('You do not have permission to delete this teacher.', 'error');
            return back();
         }

        // Check if the user is assigned as a class teacher to any class
        $assignedClassTeacher = Class_teacher::where('teacher_id', $teachers->id)->first();
        if ($assignedClassTeacher) {
            // Delete the class teacher record
            $assignedClassTeacher->delete();
        }

        // Begin a database transaction
        DB::beginTransaction();

        try {
            // Update the status column and role_id in the teachers table
            $teachers->status = 2;
            $teachers->role_id = 1; // Set to NULL
            $teachers->save();

            // Update the status column in the users table
            $user->status = 2;
            $user->save();

            // Commit the transaction
            DB::commit();

            Alert()->toast('Teacher deleted successfully', 'success');
        } catch (\Exception $e) {
            // Rollback the transaction if there's an error
            DB::rollBack();

            Alert()->toast()('Failed to delete teacher', 'error');
        }

        return back();
    }

    // display trashed teachers in the school ***********************************************************
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
