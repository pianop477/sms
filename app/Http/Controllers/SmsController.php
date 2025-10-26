<?php

namespace App\Http\Controllers;

use App\Models\Grade;
use App\Models\notifications;
use App\Models\school;
use App\Models\Student;
use App\Models\Teacher;
use App\Services\BeemSmsService;
use App\Services\NextSmsService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
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
        ], [
            'class.required_if' => 'Please select a class or check "Send to All"',
            'message_content.required' => 'Message content is required',
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

        // Fetch school details
        $school = school::findOrFail($user->school_id);

        // Validation rules for multiple classes
        $validator = Validator::make($request->all(), [
            'classes' => 'nullable|array',
            'classes.*' => 'integer|exists:grades,id',
            'message_content' => 'required|string|max:306',
            'send_to_all' => 'sometimes|boolean',
            'send_with_transport' => 'sometimes|boolean',
            'send_without_transport' => 'sometimes|boolean',
            'send_to_teachers' => 'sometimes|boolean',
        ], [
            'classes.array' => 'Classes must be an array',
            'classes.*.integer' => 'Each class must be a valid integer',
            'classes.*.exists' => 'Selected class does not exist',
            'message_content.required' => 'Message content is required',
            'message_content.max' => 'Message cannot exceed 306 characters',
        ]);

        // Custom validation - at least one recipient option must be selected
        $validator->after(function ($validator) use ($request) {
            $classesSelected = !empty($request->classes);
            $sendToAll = $request->has('send_to_all');
            $withTransport = $request->has('send_with_transport');
            $withoutTransport = $request->has('send_without_transport');
            $sendToTeachers = $request->has('send_to_teachers');

            if (!$classesSelected && !$sendToAll && !$withTransport && !$withoutTransport && !$sendToTeachers) {
                $validator->errors()->add(
                    'recipients',
                    'Please select at least one recipient group or class.'
                );
            }
        });

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $sendToAllClasses = $request->has('send_to_all');
        $sendwithTransport = $request->has('send_with_transport');
        $sendWithoutTransport = $request->has('send_without_transport');
        $sendToTeachers = $request->has('send_to_teachers');
        $selectedClasses = $request->input('classes', []);

        // Initialize variables for notification
        $recipientType = null;
        $recipientId = null;
        $data = collect();

        // Build query based on selection and determine recipient type
        if ($sendToAllClasses) {
            // Fetch students and parents for all classes
            $data = Student::query()
                ->join('parents', 'parents.id', '=', 'students.parent_id')
                ->leftJoin('users', 'users.id', '=', 'parents.user_id')
                ->select('students.*', 'users.phone')
                ->where('students.status', 1)
                ->where('students.school_id', $user->school_id)
                ->where('students.graduated', 0)
                ->get();

            $recipientType = 'parents';
            $recipientId = 4; // User type ID for parents
        }
        elseif ($sendwithTransport) {
            $data = Student::query()
                ->join('parents', 'parents.id', '=', 'students.parent_id')
                ->leftJoin('users', 'users.id', '=', 'parents.user_id')
                ->select('users.phone', 'students.*')
                ->where('students.graduated', 0)
                ->where('students.status', 1)
                ->where('students.transport_id', '!=', null)
                ->where('students.school_id', $user->school_id)
                ->get();

            $recipientType = 'parents';
            $recipientId = 4; // User type ID for parents
        }
        elseif ($sendWithoutTransport) {
            $data = Student::query()
                ->join('parents', 'parents.id', '=', 'students.parent_id')
                ->leftJoin('users', 'users.id', '=', 'parents.user_id')
                ->select('users.phone', 'students.*')
                ->where('students.graduated', 0)
                ->where('students.status', 1)
                ->where('students.transport_id', null)
                ->where('students.school_id', $user->school_id)
                ->get();

            $recipientType = 'parents';
            $recipientId = 4; // User type ID for parents
        }
        elseif ($sendToTeachers) {
            $data = Teacher::query()
                ->join('users', 'users.id', '=', 'teachers.user_id')
                ->select('users.phone', 'teachers.*')
                ->where('teachers.status', 1)
                ->where('teachers.school_id', $user->school_id)
                ->get();

            $recipientType = 'teachers';
            $recipientId = 3; // User type ID for teachers
        }
        elseif (!empty($selectedClasses)) {
            // Fetch students and parents for the selected multiple classes
            $data = Student::query()
                ->join('parents', 'parents.id', '=', 'students.parent_id')
                ->leftJoin('users', 'users.id', '=', 'parents.user_id')
                ->select('users.phone', 'students.*')
                ->where('students.graduated', 0)
                ->where('students.status', 1)
                ->whereIn('students.class_id', $selectedClasses)
                ->where('students.school_id', $user->school_id)
                ->get();

            $recipientType = 'parents';
            $recipientId = 4; // User type ID for parents
        }
        else {
            Alert()->toast('No recipient selection made', 'error');
            return back()->withInput();
        }

        // Check if any recipients were found
        if ($data->isEmpty()) {
            Alert()->toast('No phone numbers found for the selected criteria', 'error');
            return back()->withInput();
        }

        // Prepare payload
        $nextSmsService = new NextSmsService();
        $sender = $school->sender_id ?? 'SHULE APP';
        $reference = uniqid();

        // Get unique phone numbers and count
        $uniquePhones = $data->pluck('phone')
            ->filter() // Remove empty values
            ->map(function ($phone) {
                return $this->formatPhoneNumber($phone);
            })
            ->unique()
            ->values()
            ->all();

        $recipientCount = count($uniquePhones);

        // Check if we have phone numbers
        if ($recipientCount === 0) {
            Alert()->toast('No valid phone numbers found', 'error');
            return back()->withInput();
        }

        $payload = [
            "from" => $sender,
            "to" => $uniquePhones,
            "text" => $request->message_content,
            "reference" => $reference
        ];

        try {
            // Send SMS

            $response = $nextSmsService->sendSmsByNext($payload['from'], $payload['to'], $payload['text'], $payload['reference']);

            // Log the SMS sending for audit
            // \Log::info('SMS sent successfully', [
            //     'school_id' => $user->school_id,
            //     'recipient_count' => $recipientCount,
            //     'classes_selected' => $selectedClasses,
            //     'send_to_all' => $sendToAllClasses,
            //     'notification_id' => $notification->id,
            //     'message_length' => strlen($request->message_content)
            // ]);

            Alert()->toast('Message Sent Successfully to ' . $recipientCount . ' recipients', 'success');
            return redirect()->back();

        } catch(Exception $e) {
            // \Log::error('SMS sending failed', [
            //     'error' => $e->getMessage(),
            //     'school_id' => $user->school_id,
            //     'recipient_count' => $recipientCount
            // ]);

            Alert()->toast('Failed to send SMS: ' . $e->getMessage(), 'error');
            return back()->withInput();
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
