<?php

namespace App\Http\Controllers;

use App\Events\PasswordResetEvent;
use App\Models\Grade;
use App\Models\Parents;
use App\Models\school;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Transport;
use App\Models\User;
use App\Services\BeemSmsService;
use App\Services\NextSmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Vinkla\Hashids\Facades\Hashids;

class ParentsController extends Controller
{

    protected $beemSmsService;
    protected $nextSmsService;

    public function __construct(BeemSmsService $beemSmsService, NextSmsService $nextSmsService)
    {
        $this->beemSmsService = $beemSmsService;
        $this->nextSmsService = $nextSmsService;
    }

    // Display a listing of the resource *******************PARENTS ***************************************.
    public function showAllParents() {
        $user = Auth::user();
        $classes = Grade::where('school_id', '=', $user->school_id, 'AND', 'status', '=', 1)->orderBy('class_code')->get();
        $buses = Transport::where('school_id', '=', $user->school_id, 'AND', 'status', '=', 1)->orderBy('driver_name', 'ASC')->get();
        $parents = Parents::query()
                            ->join('users', 'users.id', '=', 'parents.user_id')
                            ->join('schools', 'schools.id', '=', 'parents.school_id')
                            ->select('parents.*', 'users.first_name', 'users.last_name', 'users.gender', 'users.phone', 'users.email')
                            ->where('parents.school_id', '=', $user->school_id)
                            ->where(function ($query) {
                                $query->where('parents.status', 1)
                                        ->orWhere('parents.status', 0);
                            })
                            ->orderBy('users.first_name', 'ASC')
                            ->get();
        return view('Parents.index', compact('parents','classes', 'buses'));
    }
    /**
     * Show the form for creating the resource.
     */
    public function create()
    {
        // abort(404);
        return view('Parents.create');
    }

    /**
     * Store the newly created resource in storage.
     */

    //  register new parents with new student and send sms via Beem API **************************************************
    public function registerParents(Request $request)
    {
        $user = Auth::user();
        $school = school::findOrFail($user->school_id);

        $dataValidation = $request->validate([
            'fname' => 'required|string|max:255',
            'lname' => 'required|string|max:255',
            'email' => 'string|unique:users,email|nullable',
            'gender' => 'required|string|max:255',
            'phone' => 'required|regex:/^[0-9]{10}$/|unique:users,phone',
            'street' => 'required|string|max:255',
            'school_id' => 'exists:schools,id',
            'student_first_name' => 'required|string|max:255',
            'student_middle_name' => 'required|string|max:255',
            'student_last_name' => 'required|string|max:255',
            'student_gender' => 'required|string|in:male,female',
            'dob' => 'required|date|date_format:Y-m-d',
            'class' => 'required|integer|exists:grades,id',
            'group' => 'required|string|in:a,b,c,d,e',
            'bus_no' => 'nullable|integer|exists:transports,id',
            'passport' => 'nullable|image|mimes:jpg,png,jpeg|max:512',
        ]);

        try {
            // Check if user (parent) exists
            $userExists = User::where('phone', $request->phone)
                        ->where('school_id', $user->school_id)
                        ->exists();

            // Check if student exists
            $studentExists = Student::whereRaw('LOWER(first_name) = ?', [strtolower($request->student_first_name)])
                    ->whereRaw('LOWER(middle_name) = ?', [strtolower($request->student_middle_name)])
                    ->whereRaw('LOWER(last_name) = ?', [strtolower($request->student_last_name)])
                    ->where('dob', $request->dob) // Angalia tarehe ya kuzaliwa ili kuwa na uhakika zaidi
                    ->where('school_id', $request->school_id)
                    ->first();

            if ($userExists || $studentExists) {
                Alert()->toast('Parent or Student information already exists in our records', 'error');
                return back();
            }


            $users = User::create([
                'first_name' => $request->fname,
                'last_name' => $request->lname,
                'email' => $request->email,
                'phone' => $request->phone,
                'gender' => $request->gender,
                'usertype' => $request->input('usertype', 4),
                'password' => Hash::make($request->input('password', 'shule2025')),
                'school_id' => $user->school_id,
            ]);

            $parents = Parents::create([
                'user_id' => $users->id,
                'school_id' => $user->school_id,
                'address' => $request->street,
            ]);

            $studentImage = '';

            // Handle file upload if present
            if ($request->hasFile('passport')) {
                $image = $request->file('passport');
                $imageFile = time() . '.' . $image->getClientOriginalExtension();
                $imagePath = public_path('assets/img/students');

                // Ensure the directory exists
                if (!file_exists($imagePath)) {
                    mkdir($imagePath, 0775, true);
                }

                // Move the file
                $image->move($imagePath, $imageFile);

                // Set the image file name on the student record
                $studentImage = $imageFile;
            }

            $students = Student::create([
                'admission_number' => $this->getAdmissionNumber(),
                'first_name' => $request->student_first_name,
                'middle_name' => $request->student_middle_name,
                'last_name' => $request->student_last_name,
                'parent_id' => $parents->id,
                'gender' => $request->student_gender,
                'dob' => $request->dob,
                'class_id' => $request->class,
                'group' => $request->group,
                'image' => $studentImage,
                'school_id' => $parents->school_id
            ]);

                $url = "https://shuleapp.tech";

                $nextSmsService = new NextSmsService();
                $senderId = $school->sender_id ?? "SHULE APP";
                $message = "Welcome to ShuleApp, Your Login details are: ";
                $message .= " Username: {$users->phone}";
                $message .= " Password: shule2025."; // Default password
                $message .= " Visit {$url} to Login";

                $reference = uniqid();
                $formattedPhone = $this->formatPhoneNumber($users->phone);

                $payload = [
                    'from' => $senderId,
                    'to' => $formattedPhone,
                    'text' => $message,
                    'reference' => $reference
                ];

                // $response = $nextSmsService->sendSmsByNext($payload['from'], $payload['to'], $payload['text'], $payload['reference']);

                $beemSmsService = new BeemSmsService();
                $senderId = $school->sender_id ?? 'shuleApp';
                $Code_id = 1;
                $recipients = [
                    [
                        'recipient_id' => 1,
                        'dest_addr' => $formattedPhone, // Use validated phone number
                    ],
                ];

                $response = $beemSmsService->sendSms($senderId, $message, $recipients);
                Alert()->toast('Parent and student information saved successfully', 'success');
                return redirect()->route('Parents.index');

        } catch (\Exception $e) {
            Alert()->toast($e->getMessage(), 'error');
            return back();
        }
    }

    //  phone number format according to Beem API **************************************************
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

    // format for registration number **************************************************
    protected function getAdmissionNumber()
    {
        $user = Auth::user();
        $schoolData = School::findOrFail($user->school_id);

        // Count existing unique admission numbers
        if (Student::count() >= 9000) { // Keeping some buffer
            throw new \Exception("All admission numbers are taken. Please use a larger range.");
        }

        do {
            $admissionNumber = str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
        } while (Student::where('admission_number', $admissionNumber)
                        ->where('status', 1)
                        ->where('school_id', $user->school_id)
                        ->exists());

        return $schoolData->abbriv_code . '-' . $admissionNumber;
    }

    /**
     * Display the resource.
     */
    public function show()
    {
        //
    }

    /**
     * Show the form for editing the resource.
     */
    // edit parents get form **************************************************
    public function editParent($parent)
    {
        //
        $decoded = Hashids::decode($parent);
        $loggedUser = Auth::user();
        $parents = Parents::query()->join('users', 'users.id', '=', 'parents.user_id')
                                    ->select('parents.*', 'users.first_name', 'users.last_name', 'users.gender', 'users.phone', 'users.image')
                                    ->where('parents.id', '=', $decoded[0])
                                    ->first();
        if($parents->school_id != $loggedUser->school_id) {
            Alert()->toast('You are not authorized to edit this parent', 'error');
            return back();
        }
        $students = Student::query()
                        ->join('grades', 'grades.id', '=', 'students.class_id')
                        ->select('students.*', 'grades.class_name', 'grades.class_code')
                        ->where('students.parent_id', $parents->id)->get();

        return view('Parents.edit', ['parents' => $parents, 'students' => $students]);
    }

    /**
     * Update the resource in storage.
     */

    //  update parents records set to inactive mode **************************************************
    public function updateStatus(Request $request, $parent)
    {
        //
        $decoded = Hashids::decode($parent);
        $loggedUser = Auth::user();
        $parents = Parents::findOrFail($decoded[0]);

        if($parents->school_id != $loggedUser->school_id) {
            Alert()->toast('You are not authorized to block this parent', 'error');
            return back();
        }

        $user = User::findOrFail($parents->user_id);
        $user->status = $request->input('status', 0);
        if($user->save()) {
            $parents->status = $request->input('status', 0);

            if($parents->save()) {
                event(new PasswordResetEvent($user->id));
                Alert()->toast('Parent blocked successfully', 'success');
                return back();
            }
        }
    }

    // update parents records set to active mode **************************************************
    public function restoreStatus(Request $request, $parent) {
        $decoded = Hashids::decode($parent);
        $loggedUser = Auth::user();
        $parents = Parents::findOrFail($decoded[0]);

        if($parents->school_id != $loggedUser->school_id){
            Alert()->toast('You are not authorized to block this parent', 'error');
            return back();
        }

        $user = User::findOrFail($parents->user_id);
        $user->status = $request->input('status', 1);
        if($user->save()) {
            $parents->status = $request->input('status', 1);

            if($parents->save()) {
                Alert()->toast('Parent unblocked successfully', 'success');
                return back();
            }
        }
    }

    /**
     * Remove the resource from storage.
     */

    //  delete parents records **************************************************
    public function deleteParent($parentId)
    {
        $decoded = Hashids::decode($parentId);
        try {
            $loggedUser = Auth::user();
            // Find the parent record
            $parent = Parents::find($decoded[0]);

            if($parent->school_id != $loggedUser->school_id) {
                Alert()->toast('You are not authorized to delete this parent', 'error');
                return back();
            }

            if (!$parent) {
                Alert()->toast('No such parent was found', 'error');
                return back();
            }

            // Find the associated user
            $user = User::find($parent->user_id);
            if (!$user) {
                Alert()->toast('No associated user was found', 'error');
                return back();
            }


            // Check if the parent has active students
            $activeStudents = Student::where('parent_id', $parent->id)->where('status', 1)->count();

            if ($activeStudents > 0) {
                Alert()->toast('Cannot delete this parent because has active children', 'info');
                return back();
            }

            // Delete any related inactive students (if needed)
            Student::where('parent_id', $parent->id)->where('status', '!=', 1)->delete();

            // Check and delete the user's profile image if it exists
            if (!empty($user->image)) {
                $userImagePath = public_path('assets/img/profile/' . $user->image);
                if (file_exists($userImagePath)) {
                    unlink($userImagePath);
                }
            }

            // Delete the user and parent records
            $user->delete();
            $parent->delete();

            Alert()->toast('Parent data has been deleted successfully', 'success');
            return back();
        } catch (\Exception $e) {
            Alert()->toast($e->getMessage(), 'error');
            return back();
        }
    }

    // update full data for parents **************************************************
    public function updateParent (Request $request, $parents)
    {
        $decoded = Hashids::decode($parents);
        $parent = Parents::findOrFail($decoded[0]);
        $loggedUser = Auth::user();

        if($parent->school_id != $loggedUser->school_id) {
            Alert()->toast('You are not authorized to block this parent', 'error');
            return back();
        }
        $user = User::findOrFail($parent->user_id);

        // run validation
        $this->validate($request, [
            'fname' => 'required|max:255|string',
            'lname' => 'required|max:255|string',
            'gender' => 'required|string|max:255',
            'phone' => 'required|regex:/^[0-9]{10}$/|unique:users,phone,'.$user->id,
            'street' => 'required|string|max:255',
            'nullable|image|mimes:jpg,png,jpeg|max:512',
        ]);

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

            //check for existing image file
            if(!empty($user->image)) {
                $existingImagePath = public_path('assets/img/profile/' . $user->image);
                if (file_exists($existingImagePath)) {
                    unlink($existingImagePath);
                }
            }

            // Move the file
            $image->move($imageDestinationPath, $imageName);

            // Save the file name to the database
            $user->image = $imageName;
        }

        if ($user->save()) {
            // Log::info('User updated successfully');
            $parent->address = $request->street;

            if ($parent->save()) {
                // Log::info('Teacher updated successfully');
                Alert()->toast('Parent records updated successfully', 'success');
                return back();
            } else {
                // Log::error('Failed to update teacher information');
                Alert()->toast('Failed to updated Parent records', 'error');
                return back();
            }
        } else {
            // Log::error('Failed to update user information');
            Alert()->toast('Failed to update parent records', 'error');
            return back();
        }
    }
}
