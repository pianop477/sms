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
use App\Services\NextSmsService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use RealRashid\SweetAlert\Facades\Alert;
use Vinkla\Hashids\Facades\Hashids;

class ManagerController extends Controller
{

    public function index() {
        $managers = User::where('usertype', '=', 2, 'AND', 'status', '=', 1)->get();
        $schools = school::where('status', '=', 1)->get()->orderBy('school_name', 'ASC');
        return view('Schools.create', ['managers' => $managers, 'schools' => $schools]);
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
        try {
            // Validate without password (since it's auto-generated)
            $request->validate([
                'fname' => 'required|string|max:255',
                'lname' => 'required|string|max:255',
                'email' => 'nullable|email|unique:users,email',
                'phone' => 'required|regex:/^[0-9]{10}$/|unique:users,phone',
                'gender' => 'required|string|in:male,female',
                'school' => 'required|exists:schools,id',
            ], [
                'phone.regex' => 'Phone number must be exactly 10 digits',
                'phone.unique' => 'This phone number is already registered',
                'email.unique' => 'This email is already registered',
                'gender.in' => 'Gender must be either male or female',
            ]);

            // Generate random password
            $password = 'shule2025';
            $usertype = 2;

            // Create user
            $user = User::create([
                'first_name' => $request->input('fname'),
                'last_name' => $request->input('lname'),
                'email' => $request->input('email'),
                'phone' => $request->input('phone'),
                'gender' => $request->input('gender'),
                'usertype' => $usertype,
                'school_id' => $request->input('school'),
                'password' => Hash::make($password),
            ]);

            // Generate login link
            $link = config('app.url') ?? 'https://shuleapp.tech';

            // Create SMS message
            $message = "Hello ". strtoupper($user->first_name) .", Welcome to ShuleApp. Your Username is: {$user->phone} Password: shule2025. Use link {$link} to Login and change password Thank you.";

            // Get school for SMS sender ID
            $school = School::find($user->school_id);

            // Format phone number for SMS
            $formattedPhone = $this->formatPhoneNumber($user->phone);

            if ($formattedPhone && $school) {
                // Send SMS synchronously first for debugging
                $sms = new NextSmsService();

                try {
                    $response = $sms->sendSmsByNext(
                        $school->sender_id ?? 'SHULE APP',
                        $formattedPhone,
                        $message,
                        uniqid()
                    );

                    // Log::info('SMS Response:', $response);

                    // If SMS fails, log but don't stop
                    if(!$response['success'] ?? false) {
                        Log::warning('SMS failed: ' . ($response['error'] ?? 'Unknown error'));
                        // Alert()->toast($response['message'], 'error');
                        return back();
                    }

                } catch (\Exception $e) {
                    // Log::error('SMS Exception: ' . $e->getMessage());
                    Alert()->toast($e->getMessage(), 'error');
                    return back();
                }
            }

            Alert()->toast('Manager registered successfully. Password sent via SMS.', 'success');
            return redirect()->back();

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Laravel handles validation errors automatically
            return redirect()->back()->withErrors($e->validator)->withInput();

        } catch (\Exception $e) {
            Log::error('User registration failed: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            Alert()->toast('Registration failed: ' . $e->getMessage(), 'error');
            return redirect()->back()->withInput();
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
        $users->password = Hash::make($request->input('password', 'shule2025'));
        $users->save();
        event(new PasswordResetEvent($user));
        Alert()->toast('Account Password reset successfully', 'success');
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

    public function updateStatus($school, Request $request)
    {
        $school_id = Hashids::decode($school)[0];
        $user_status = User::where('school_id', $school_id)->where('usertype', 2)->get();
        // return $user_status;

        $status = $request->input('status', 0);
        foreach($user_status as $row) {
            // Update the status of the user
            $row->status = $status;
            $row->update();
        }
            // Update the status of the school associated with the user
            $school_info = School::findOrFail($school_id);
            $school_info->status = $status;
            $school_info->update();

                // Update the status of all users associated with the school
                User::where('school_id', $school_info->id)->whereIn('usertype', [3,4,5])->update(['status' => $status]);

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
                Alert()->toast('School has been deactivated Successfully', 'success');
                return back();
            } else {
                Alert()->toast('Something went wrong, try again', 'error');
                return back();
            }

    }

    public function activateStatus($school, Request $request)
    {
        $school_id = Hashids::decode($school)[0];
        $user_status = User::where('school_id', $school_id)->where('usertype', 2)->get();
        // $school_id = $user_status->school_id;
        $status = $request->input('status', 1);

        foreach($user_status as $row) {
            // Update the status of the user
            $row->status = $status;
            $row->update();
        }

        // Update the status of the school associated with the user
        $school_info = School::findOrFail($school_id);
        $school_info->status = $status;
        $school_info->update();

            // Update the status of all users associated with the school
            User::where('school_id', $school_info->id)->whereIn('usertype', [3,4,5])->update(['status' => $status]);

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
            Alert()->toast('School has been activated successfully', 'success');
            return back();
        }
    }

    public function show ($id)
    {
        $decoded = Hashids::decode($id);
        $user = User::query()
                ->join('schools', 'schools.id', '=', 'users.school_id')
                ->select('users.*', 'schools.school_name', 'schools.school_reg_no', 'schools.postal_address', 'schools.postal_name', 'schools.country',
                    'schools.id as school_id')
                ->findOrFail($decoded[0]);
        return view('Managers.manager_profile', compact('user'));
    }

    public function updateProfile (Request $request, $id)
    {
        $decoded = Hashids::decode($id);
        $user = User::findOrFail($decoded[0]);

        if(!$user) {
            Alert()->toast('User not found', 'error');
            return back();
        }

        $this->validate($request, [
            'fname' => 'required|string|max:255',
            'lname' => 'required|string|max:255',
            'email' => 'nullable|string|unique:users,email,'.$decoded[0],
            'phone' => 'required|regex:/^[0-9]{10}$/|unique:users,phone,'.$decoded[0],
            'gender' => 'required|string|max:255',
            'school' => 'required|exists:schools,id',
        ]);

        $user->update([
            'first_name' => $request->input('fname'),
            'last_name' => $request->input('lname'),
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
            'gender' => $request->input('gender'),
            'school_id' => $request->input('school'),
        ]);

        Alert()->toast('Profile updated successfully', 'success');
        return back();
    }

    public function destroyManager($id)
    {
        $decoded = Hashids::decode($id);
        $user = User::findOrFail($decoded[0]);


        if(!$user) {
            Alert()->toast('User not found', 'error');
            return back();
        }

        //check if user has image in directory storage/app/public/profile and delete it
        if($user->image) {
            $image_path = storage_path('app/public/profile/'.$user->image);
            if(file_exists($image_path)) {
                unlink($image_path);
            }
        }

        $user->delete();

        Alert()->toast('User deleted successfully', 'success');
        return back();
    }

}
