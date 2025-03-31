<?php

namespace App\Http\Controllers;

use App\Models\Grade;
use App\Models\message;
use App\Models\Parents;
use App\Models\school;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Http\Request;
use App\Models\User;
use App\Services\BeemSmsService;
use App\Services\NextSmsService;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RealRashid\SweetAlert\Facades\Alert;
use Vinkla\Hashids\Facades\Hashids;

class SchoolsController extends Controller
{
    protected $beemSmsService;
    protected $nextSmsService;

    public function __construct(BeemSmsService $beemSmsService, NextSmsService $nextSmsService)
    {
        $this->beemSmsService = $beemSmsService;
        $this->nextSmsService = $nextSmsService;
    }

    public function index() {
        $schools = school::orderBy('school_name', 'ASC')->orderBy('school_name', 'ASC')->get();
        return view('Schools.index', ['schools' => $schools]);
    }
    /**
     * Show the form for creating the resource.
     */
    public function create()
    {
        // abort(404)
        return view('Schools.create');
    }

    /**
     * Store the newly created resource in storage.
     */
    public function store(Request $request)
    {
        // abort(404);
        $request->validate([
            'name' => 'required|string|max:255',
            'reg_no' => 'required|string|max:255',
            'abbriv' => 'required|string|max:4',
            'logo' => 'image|max:1024|mimes:png,jpg,jpeg',
            'postal' => 'required|string|max:255',
            'postal_name' => 'required|string|max:255',
            'country' => 'required|string',
            'fname' => 'required|string|max:255',
            'lname' => 'required|string|max:255',
            'email' => 'nullable|string|unique:users,email',
            'phone' => 'required|regex:/^[0-9]{10}$/|unique:users,phone',
            'gender' => 'required|string|max:255',
            'sender_name' => 'nullable|string|max:11'
        ]);
        try {

            //check if exists
            $schoolExists = school::where('school_reg_no', $request->reg_no)->exists();

            if($schoolExists) {
                Alert()->toast('This school already exists', 'error');
                return back();
            }
            //store schools information
            $school = new school();
            $school->school_name = $request->name;
            $school->school_reg_no = $request->reg_no;
            $school->abbriv_code = $request->abbriv;
            $school->sender_id = $request->sender_name;
            $school->reg_date = Carbon::now()->format('Y-m-d');
            $school->postal_address = $request->postal;
            $school->postal_name = $request->postal_name;
            $school->status = 2;
            $school->country = $request->country;
            if ($request->hasFile('logo')) {
                $image = $request->file('logo');
                $imageFile = time() . '.' . $image->getClientOriginalExtension();
                $imagePath = public_path('assets/img/logo');

                // Ensure the directory exists
                if (!file_exists($imagePath)) {
                    mkdir($imagePath, 0775, true);
                }

                // Move the file
                $image->move($imagePath, $imageFile);

                // Set the image file name on the student record
                $school->logo = $imageFile;
            }
            $school->save();

            //store managers information
            $users = new User();
            $users->first_name = $request->fname;
            $users->last_name = $request->lname;
            $users->email = $request->email;
            $users->phone = $request->phone;
            $users->gender = $request->gender;
            $users->usertype = $request->input('usertype', 2);
            $users->school_id = $request->school;
            $users->password = Hash::make($request->input('password', 'shule2025'));
            $users->school_id = $school->id;
            $saveData = $users->save();

            //notify manager using nextSms API
            $nextSmsService = new NextSmsService();
            $url = "https://shuleapp.tech";
            $sender = 'SHULE APP';
            $message = "Welcome to Shule App. Your login details are: Username: $users->phone, Password: shule2025, Thanks for choosing us. Visit; $url";
            $destinations = $this->formatPhoneNumber($users->phone);
            $reference = uniqid();

            $payload = [
                'from' => $sender,
                'to' => $destinations,
                'text' => $message,
                'reference' => $reference
            ];
            // $response = $nextSmsService->sendSmsByNext($payload['from'], $payload['to'], $payload['text'], $payload['reference']);

            // send sms  by Beem API
            //prepare send sms payload to send via Beem API *************************************
            $beemSmsService = new BeemSmsService();
            $sourceAddr = 'shuleApp';
            $recipient_id = 1;
            $phone = $this->formatPhoneNumber($users->phone);
            $recipients = [
                [
                    'recipient_id' => $recipient_id++,
                    'dest_addr' => $phone
                ],
            ];

            //send sms by Beem API
            $response = $beemSmsService->sendSms($sourceAddr, $message, $recipients);

            Alert()->toast('School information saved successfully', 'success');
            return redirect()->back();
        }
        catch(\Exception $e){
            Alert::error('Error', $e->getMessage());
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
     * Display the resource.
     */
    public function show($school)
    {
        $decoded = Hashids::decode($school);
        $schools = school::find($decoded[0]);
        $managers = User::query()->join('schools', 'schools.id', '=', 'users.school_id')
                            ->select(
                                'users.*', 'schools.school_name', 'schools.school_reg_no', 'schools.logo'
                            )
                            ->where('users.usertype', 2)
                            ->where('users.school_id', $schools->id)
                            ->get();
        $parents = Parents::query()->join('users', 'users.id', '=', 'parents.user_id')
                                    ->select(
                                        'parents.*', 'users.first_name', 'users.last_name', 'users.usertype'
                                    )
                                    ->where('users.usertype', 4)
                                    ->where('parents.school_id', $schools->id)
                                    ->get();
        $teachers = Teacher::query()->join('users', 'users.id', '=', 'teachers.user_id')
                                    ->select('teachers.*', 'users.first_name', 'users.last_name', 'users.usertype')
                                    ->where('users.usertype', 3)
                                    ->where('teachers.school_id', $schools->id)
                                    ->get();
        $students = Student::where('school_id', $schools->id)->where('status', 1)->get();
        $courses = Subject::where('school_id', $schools->id)->get();
        $classes = Grade::where('school_id', $schools->id)->get();
        return view('Schools.show', compact('managers', 'parents', 'teachers', 'students', 'courses', 'classes', 'schools'));
    }

    /**
     * Show the form for editing the resource.
     */
    public function invoceCreate($school)
    {
        //
        $decoded = Hashids::decode($school);
        $schools = school::find($decoded[0]);
        $managers = User::query()->join('schools', 'schools.id', '=', 'users.school_id')
                            ->select(
                                'users.*', 'schools.school_name', 'schools.school_reg_no', 'schools.logo'
                            )
                            ->where('users.usertype', 2)
                            ->where('users.school_id', $schools->id)
                            ->get();
        $students = Student::where('school_id', $schools->id)->where('status', 1)->get();
        return view('invoice.invoice', compact('schools', 'managers', 'students'));
    }

    /**
     * Edit the resource in storage
    */

    public function edit ($school)
    {
        $decoded = Hashids::decode($school);
        $schools = school::find($decoded[0]);
        return view('Schools.edit', compact('schools'));
    }

     /** Update the resource in storage.
     */

    public function updateSchool($school, Request $request)
    {
        //
        $decoded = Hashids::decode($school);
        $request->validate([
            'name' => 'required|string|max:255',
            'reg_no' => 'required|string|max:255',
            'logo' => 'image|max:1024|mimes:png,jpg,jpeg',
            'abbriv' => 'required|string|max:4',
            'postal' => 'required|string|max:255',
            'postal_name' => 'required|string|max:255',
            'country' => 'required|string',
            'sender_name' => 'nullable|string|max:11'
        ]);

        $schools = school::findOrFail($decoded[0]);
        $schools->school_name = $request->name;
        $schools->abbriv_code = $request->abbriv;
        $schools->school_reg_no = $request->reg_no;
        $schools->postal_address = $request->postal;
        $schools->postal_name = $request->postal_name;
        $schools->sender_id = $request->sender_name;
        $schools->country = $request->country;
        if ($request->hasFile('logo')) {
            $image = $request->file('logo');
            $imageFile = time() . '.' . $image->getClientOriginalExtension();
            $imagePath = public_path('assets/img/logo');

            // Ensure the directory exists
            if (!file_exists($imagePath)) {
                mkdir($imagePath, 0775, true);
            }

            // Check if the existing file exists and delete it
            $existingFile = $imagePath . '/' . $schools->logo;
            if (file_exists($existingFile)) {
                unlink($existingFile);
            }

            // Move the new file
            $image->move($imagePath, $imageFile);

            // Set the image file name on the school record
            $schools->logo = $imageFile;
        }
        $schools->save();
        Alert()->toast('School information updated successfully', 'success');
        return redirect()->route('home');
    }

    /**
     * Remove the resource from storage.
     */
    public function destroy($school)
    {
        // abort(404);
        $decoded = Hashids::decode($school);
        $schools = school::find($decoded[0]);

        if(! $schools) {
            Alert()->toast('This school is not found', 'error');
            return back();
        }
        $logoPath = public_path('assets/img/logo');
        $existingFile = $logoPath . '/' . $schools->logo;
        if(file_exists($existingFile)) {
            unlink($existingFile);
        }
        //delete school
        $schools->delete();
        Alert()->toast('School has been deleted successfully', 'success');
        return redirect()->back();

    }

    public function showFeedback ()
    {
        $message = message::latest()->paginate(15);
        return view('Schools.feedback', compact('message'));
    }

    public function deletePost ($sms)
    {
        $decoded = Hashids::decode($sms);

        $message = message::findOrFail($decoded[0]);
        $message->delete();
        Alert()->toast('Post has been moved to trash', 'success');
        return back();
    }

    public function addActiveTime(Request $request, $school)
    {
        $decoded = Hashids::decode($school);
        $request->validate([
            'school' => 'exists:schools,id',
            'service_duration' => 'required|integer',
        ]);
        try {

            $startDate = Carbon::now();
            $endDate = $startDate->copy()->addMonth($request->service_duration);

            $schools = school::findOrFail($decoded[0]);
            $schools->service_start_date = $startDate;
            $schools->service_end_date = $endDate;
            $schools->service_duration = $request->service_duration;
            $schools->status = 1;
            $schools->save();

            //update users who disabled
            User::where('school_id', $schools->id)->where('status', 0)->update(['status' => 1]);

            Alert()->toast('Active time has been updated successfully', 'success');
            return back();
        }
        catch(Exception $e) {
            Alert()->toast($e->getMessage(), 'error');
            return back();
        }

    }

    public function replyFeedback($sms)
    {
        $id = Hashids::decode($sms);
        $sender = message::find($id[0]);
        $schools = school::orderBy('school_name')->get();
        return view('Schools.reply_box', compact('sender', 'schools'));
    }

    public function sendFeebackReply (Request $request)
    {
        // dd($request->all());
        try {
            $this->validate($request, [
                'message_content' => 'required|string|max:160',
            ]);

            $sender_id = '';
            if($request->sender_id == NULL) {
                $sender_id = 'SHULE APP';
            }
            else {
                $sender_id = $request->sender_id;
            }

            $nextSmsService = new NextSmsService();
            $payload = [
                'from' => $sender_id,
                'to' => $this->formatPhoneNumber($request->phone),
                'text' => $request->message_content,
                'reference' => uniqid()
            ];

            $response = $nextSmsService->sendSmsByNext($payload['from'], $payload['to'], $payload['text'], $payload['reference']);

            //delete feedback after reply;
            $textId = $request->text_id;
            // return $textId;
            $sms = message::where('id', $textId)->delete();

            Alert()->toast('Message sent', 'success');
            return redirect()->route('feedback');

        }
        catch(\Exception $e) {
            Alert()->toast($e->getMessage(), 'error');
            return back();
        }
    }

}
