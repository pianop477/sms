<?php

namespace App\Http\Controllers;

use App\Models\Grade;
use App\Models\school;
use App\Models\Student;
use App\Services\BeemSmsService;
use App\Services\NextSmsService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use RealRashid\SweetAlert\Facades\Alert;

use function Laravel\Prompts\text;

class SmsController extends Controller
{
    //

    protected $beemSmsService;
    protected $nextSmsService;

    public function __construct(BeemSmsService $beemSmsService, NextSmsService $nextSmsService)
    {
        $this->beemSmsService = $beemSmsService;
        $this->nextSmsService = $nextSmsService;
    }

    public function smsForm ()
    {
        $user = auth()->user();

        $classes = Grade::where('status', 1)
            ->where('school_id', $user->school_id)
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('students')
                    ->whereRaw('students.class_id = grades.id')
                    ->where('graduated', 0);
            })
            ->orderBy('class_code')
            ->get();

        return view('profile.sms', compact('classes'));
    }


    //send sms using beem api service***************************************************************************
    public function sendSms(Request $request)
    {
        $user = Auth::user();

        //fetch school
        $school = school::findOrFail($user->school_id);

        // Log the entire request payload for debugging
        // Log::info('Request Data:', $request->all());

        // Validate the request
        $this->validate($request, [
            'class' => 'nullable|required_if:send_to_all,0|integer|exists:grades,id',
            'message_content' => 'required|string',
        ]);

        // Check if "Send to All" is selected
        $sendToAll = $request->has('send_to_all');
        // Log::info('is_checked: ' . ($sendToAll ? 'true' : 'false'));

        // Fetch students based on the selection
        if ($sendToAll) {
            // Fetch students and parents for all classes
            $students = Student::query()
                ->join('parents', 'parents.id', '=', 'students.parent_id')
                ->leftJoin('users', 'users.id', '=', 'parents.user_id')
                ->select('students.*', 'users.phone')
                ->where('students.school_id', $user->school_id)
                ->where('students.graduated', 0) // Only include students who are not graduated
                ->where('students.status', 1)
                ->get();
        } else {
            // Fetch students and parents for the selected class
            $students = Student::query()
                ->join('parents', 'parents.id', '=', 'students.parent_id')
                ->leftJoin('users', 'users.id', '=', 'parents.user_id')
                ->select('users.phone', 'students.*')
                ->where('students.class_id', $request->class)
                ->where('students.graduated', 0)
                ->where('students.status', 1)
                ->where('students.school_id', $user->school_id)
                ->get();
        }

        // Check if any students were found
        if ($students->isEmpty()) {
            // return response()->json(['error' => 'No phone numbers found for this class'], 400);
            Alert()->toast('No phone numbers found for this class', 'error');
            return back();
        }

        // Prepare recipients array for Beem API
        $beemSmsService = new BeemSmsService();
        $sourceAddr = $school->sender_id ?? 'shuleApp'; // Correctly set the source address
        $recipients = [];
        $recipient_id = 1;

        // Ondoa namba zinazojirudia kwa kutumia unique()
        $uniquePhones = $students->pluck('phone')->map(function ($phone) {
            return $this->formatPhoneNumber($phone); // Hakikisha namba zina format sahihi
        })->unique();

        // Kujaza list ya wapokeaji
        foreach ($uniquePhones as $phone) {
            $recipients[] = [
                'recipient_id' => $recipient_id++,
                'dest_addr' => $phone,
            ];
        }

        try {
            // Send SMS
            $response = $beemSmsService->sendSms($sourceAddr, $request->message_content, $recipients);

            // Session::flash('success', 'Message sent successfully');
            Alert()->toast('Message sent successfully', 'success');
            return redirect()->back();

        } catch (Exception $e) {
            // Session::flash('error', $e->getMessage());
            Alert()->toast($e->getMessage(), 'error');
            return redirect()->back();
        }
    }



    //send sms using nextSms api service***************************************************************************
    public function sendSmsUsingNextSms(Request $request)
    {
        $user = Auth::user();

        //fetch school details
        $school = school::findOrFail($user->school_id);

        $this->validate($request, [
            'class' => 'nullable|required_if:send_to_all,0|integer|exists:grades,id',
            'message_content' => 'required|string',
        ]);

        $sendToAll = $request->has('send_to_all');

        if ($sendToAll) {
            // Fetch students and parents for all classes
            $students = Student::query()
                ->join('parents', 'parents.id', '=', 'students.parent_id')
                ->leftJoin('users', 'users.id', '=', 'parents.user_id')
                ->select('students.*', 'users.phone')
                ->where('students.status', 1)
                ->where('students.school_id', $user->school_id)
                ->where('students.graduated', 0)
                ->get();
        } else {
            // Fetch students and parents for the selected class
            $students = Student::query()
                ->join('parents', 'parents.id', '=', 'students.parent_id')
                ->leftJoin('users', 'users.id', '=', 'parents.user_id')
                ->select('users.phone', 'students.*')
                ->where('students.graduated', 0)
                ->where('students.status', 1)
                ->where('students.class_id', $request->class)
                ->where('students.school_id', $user->school_id)
                ->get();
        }

        // Check if any students were found
        if ($students->isEmpty()) {
            // return response()->json(['error' => 'No phone numbers found for this class'], 400);
            Alert()->toast('No phone numbers found for this class', 'error');
            return back();
        }

        //prepare payload
        $nextSmsService = new NextSmsService();
        $sender = 'SHULE APP';
        $dest = [];
        $reference = uniqid();

        // Ondoa namba zinazojirudia kwa kutumia unique()
        $uniquePhones = $students->pluck('phone')->map(function ($phone) {
            return $this->formatPhoneNumber($phone); // Hakikisha namba zina format sahihi
        })->unique()->values()->all(); // Hakikisha ni array safi

        // Tengeneza payload katika format inayofaa
        $payload = [
            "from" => $sender,
            "to" => $uniquePhones, // Array ya strings badala ya array ya arrays
            "text" => $request->message_content, // Ujumbe wa SMS
            "reference" => $reference
        ];

        try {

           // Tuma SMS
            $response = $nextSmsService->sendSmsByNext($payload['from'], $payload['to'], $payload['text'], $payload['reference']);

            Alert()->toast('Message Sent Successfully', 'success');
            return redirect()->back();
        }
        catch(Exception $e) {
            Alert()->toast($e->getMessage(), 'error');
            return back();
        }


    }

    /**
     * Format phone number to match Beem API requirements.
     */
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

    //send sms to all parents
}
