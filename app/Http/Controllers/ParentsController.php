<?php

namespace App\Http\Controllers;

use App\Events\PasswordResetEvent;
use App\Imports\ParentStudentImport;
use App\Models\Grade;
use App\Models\Parents;
use App\Models\school;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Transport;
use App\Models\User;
use App\Services\BeemSmsService;
use App\Services\NextSmsService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
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
    public function showAllParents()
    {
        $user = Auth::user();
        $classes = Grade::where('school_id', '=', $user->school_id, 'AND', 'status', '=', 1)->orderBy('class_code')->get();
        $buses = Transport::where('school_id', '=', $user->school_id, 'AND', 'status', '=', 1)->orderBy('bus_no', 'ASC')->get();
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
        return view('Parents.index', compact('parents', 'classes', 'buses'));
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
            'passport' => 'nullable|mimes:jpg,png,jpeg,giff,tiff,bmp|max:1024',
        ], [
            'phone.regex' => 'The phone number must be 10 digits long.',
            'phone.unique' => 'The phone number has already been taken.',
            'email.unique' => 'The email has already been taken.',
            'passport.mimes' => 'The image must be a file of type: jpg, png, jpeg,giff,tiff,bmp.',
            'passport.max' => 'The image may not be greater than 1MB.',
        ]);

        if ($request->hasFile('passport')) {
            // Scan the uploaded file for viruses
            $scanResult = $this->scanFileForViruses($request->file('passport'));
            if (!$scanResult['clean']) {
                Alert()->toast('File security check failed: ' . $scanResult['message'], 'error');
                return redirect()->back();
            }
        }

        DB::beginTransaction();
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
                // $image = $request->file('passport');
                $imageFile = time() . '_' . uniqid() . '.' . $request->passport->getClientOriginalExtension();

                $request->passport->storeAs('students', $imageFile, 'public');
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
                'transport_id' => $request->bus_no,
                'group' => $request->group,
                'image' => $studentImage,
                'school_id' => $parents->school_id
            ]);

            DB::commit();

            $url = "https://shuleapp.tech";

            $nextSmsService = new NextSmsService();
            $senderId = $school->sender_id ?? "SHULE APP";
            $message = "Hello {$users->first_name} {$users->last_name},\n";
            $message .= "Welcome to ShuleApp, Your Login details are:\n";
            $message .= " Username: {$users->phone}\n";
            $message .= " Password: shule2025.\n"; // Default password
            $message .= " Click here {$url} to Login";

            $reference = uniqid();
            $formattedPhone = $this->formatPhoneNumber($users->phone);

            $payload = [
                'from' => $senderId,
                'to' => $formattedPhone,
                'text' => $message,
                'reference' => $reference
            ];

            $response = $nextSmsService->sendSmsByNext($payload['from'], $payload['to'], $payload['text'], $payload['reference']);

            if (!$response['success']) {
                Alert()->toast('SMS failed: ' . $response['error'], 'error');
                return back();
            }

            $beemSmsService = new BeemSmsService();
            $senderId = $school->sender_id ?? 'shuleApp';
            $Code_id = 1;
            $recipients = [
                [
                    'recipient_id' => 1,
                    'dest_addr' => $formattedPhone, // Use validated phone number
                ],
            ];

            // $response = $beemSmsService->sendSms($senderId, $message, $recipients);
            Alert()->toast('Parent and student information saved successfully', 'success');
            return redirect()->route('Parents.index');
        } catch (\Exception $e) {
            DB::rollBack();
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

        // Pata ID ya mwisho ya mwanafunzi na uongeze 1
        $lastStudent = Student::where('school_id', $user->school_id)
            ->orderBy('id', 'desc')
            ->first();

        $lastId = $lastStudent ? $lastStudent->id + 1 : 1;

        // Hakikisha kuwa ID ni ya kipekee
        $admissionNumber = str_pad($lastId, 4, '0', STR_PAD_LEFT);

        // Hakikisha admission number ni ya kipekee
        while (Student::where('admission_number', $schoolData->abbriv_code . '-' . $admissionNumber)->exists()) {
            $lastId++;
            $admissionNumber = str_pad($lastId, 4, '0', STR_PAD_LEFT);
        }

        // Rudisha nambari ya kujiunga kwa kutumia kifupi cha shule na ID
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
            ->select('parents.*', 'users.first_name', 'users.last_name', 'users.created_at as user_created_at', 'users.email', 'users.gender', 'users.phone', 'users.image')
            ->where('parents.id', '=', $decoded[0])
            ->first();
        if ($parents->school_id != $loggedUser->school_id) {
            Alert()->toast('You are not authorized to edit this parent', 'error');
            return back();
        }
        $students = Student::query()
            ->join('grades', 'grades.id', '=', 'students.class_id')
            ->select('students.*', 'grades.class_name', 'grades.class_code')
            ->where('students.parent_id', $parents->id)
            ->where('students.status', 1)
            ->get();

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

        if ($parents->school_id != $loggedUser->school_id) {
            Alert()->toast('You are not authorized to block this parent', 'error');
            return back();
        }

        $user = User::findOrFail($parents->user_id);
        $user->status = $request->input('status', 0);
        if ($user->save()) {
            $parents->status = $request->input('status', 0);

            if ($parents->save()) {
                event(new PasswordResetEvent($user->id));
                Alert()->toast('Parent blocked successfully', 'success');
                return back();
            }
        }
    }

    // update parents records set to active mode **************************************************
    public function restoreStatus(Request $request, $parent)
    {
        $decoded = Hashids::decode($parent);
        $loggedUser = Auth::user();
        $parents = Parents::findOrFail($decoded[0]);

        if ($parents->school_id != $loggedUser->school_id) {
            Alert()->toast('You are not authorized to block this parent', 'error');
            return back();
        }

        $user = User::findOrFail($parents->user_id);
        $user->status = $request->input('status', 1);
        if ($user->save()) {
            $parents->status = $request->input('status', 1);

            if ($parents->save()) {
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

            if ($parent->school_id != $loggedUser->school_id) {
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
            $student = Student::where('parent_id', $parent->id)->where('status', '!=', 1)->get();

            foreach ($student as $s) {
                try {
                    $sixMonthsAgo = now()->subMonths(6);

                    if ($s->updated_at > $sixMonthsAgo) {
                        Alert()->toast('Cannot delete this parent because student is still in grace period', 'info');
                        return back();
                    }
                    // Check and delete the student's profile image if it exists
                    if (!empty($student->image)) {
                        $studentImagePath = storage_path('app/public/students/' . $student->image);
                        if (file_exists($studentImagePath)) {
                            unlink($studentImagePath);
                        }
                    }
                    // Delete the student record
                    $student->delete();
                } catch (Exception $e) {
                    Alert()->toast($e->getMessage(), 'error');
                    return back();
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
    public function updateParent(Request $request, $parents)
    {
        $decoded = Hashids::decode($parents);
        $parent = Parents::findOrFail($decoded[0]);
        $loggedUser = Auth::user();

        if ($parent->school_id != $loggedUser->school_id) {
            Alert()->toast('You are not authorized to block this parent', 'error');
            return back();
        }
        $user = User::findOrFail($parent->user_id);

        // run validation
        $this->validate(
            $request,
            [
                'fname' => 'required|max:255|string',
                'lname' => 'required|max:255|string',
                'gender' => 'required|string|max:255',
                'phone' => 'required|regex:/^[0-9]{10}$/|unique:users,phone,' . $user->id,
                'email' => 'nullable|unique:users,email,' . $user->id,
                'street' => 'required|string|max:255',
                'image' => 'nullable|mimes:jpg,png,jpeg,tiff,bmp,giff|max:1024',
            ],
            [
                'phone.regex' => 'The phone number must be 10 digits long.',
                'phone.unique' => 'The phone number has already been taken.',
                'email.unique' => 'The email has already been taken.',
                'image.mimes' => 'The image must be a file of type: jpg, png, jpeg.',
                'image.max' => 'The image may not be greater than 1MB.',
            ]
        );

        // scan image file for virus
        if ($request->hasFile('image')) {
            $scanResult = $this->scanFileForViruses($request->file('image'));
            if (!$scanResult['clean']) {
                Alert()->toast('File security check failed: ' . $scanResult['message'], 'error');
                return redirect()->back();
            }
        }

        $user->first_name = $request->fname;
        $user->last_name = $request->lname;
        $user->phone = $request->phone;
        $user->gender = $request->gender;
        $user->email = $request->email;

        if ($request->hasFile('image')) {
            // Log::info('Image upload detected');
            if ($user->image && Storage::disk('public')->exists('profile/' . $user->image)) {
                Storage::disk('public')->delete('profile/' . $user->image);
            }

            // Create unique logo name
            $imageFile = time() . '_' . uniqid() . '.' . $request->image->getClientOriginalExtension();

            // Store in storage/app/public/logo
            $request->image->storeAs('profile', $imageFile, 'public');

            // Save the file name to the database
            $user->image = $imageFile;
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

    private function sendSmsToParents($schoolId)
    {
        $parents = Parents::where('school_id', $schoolId)->get();
        $school = $user = Auth::user();
        $url = "https://shuleapp.tech";

        foreach ($parents as $parent) {
            // Fetch user linked to the parent
            $users = User::find($parent->user_id);

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

            // Send SMS
            $beemSmsService = new BeemSmsService();
            $Code_id = 1;
            $recipients = [
                [
                    'recipient_id' => 1,
                    'dest_addr' => $formattedPhone, // Use validated phone number
                ],
            ];

            $response = $beemSmsService->sendSms($senderId, $message, $recipients);
        }
    }

    //import file
    public function import(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|file|mimes:xlsx,csv,xls|max:2048',
            ], [
                'file.mimes' => 'The file must be a file of type: xlsx, csv.',
                'file.max' => 'The file may not be greater than 2MB.',
                'file.required' => 'The field must be filled.',
                'file.file' => 'The file must be a file.',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
        try {
            $scanResult = $this->scanFileForViruses($request->file('file'));
            if (!$scanResult['clean']) {
                return response()->json([
                    'success' => false,
                    'message' => 'File security check failed: ' . $scanResult['message']
                ], 422);
            }
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }

        try {
            // Step 1: Validate and extract data for preview
            $file = $request->file('file');

            // Create a new import instance
            $import = new ParentStudentImport();

            // Use Laravel Excel's Reader to read the file
            $data = Excel::toArray($import, $file);

            if (empty($data) || empty($data[0])) {
                return response()->json([
                    'success' => false,
                    'message' => 'No data found in the file'
                ], 422);
            }

            // Validate each row
            $rows = $data[0];
            $previewData = [];
            $errors = [];
            $rules = $import->rules();

            foreach ($rows as $index => $row) {
                $rowNumber = $index + 2;

                try {
                    // Validate the row
                    $validator = Validator::make($row, $rules);

                    if ($validator->fails()) {
                        foreach ($validator->errors()->all() as $error) {
                            $errors[] = "Row {$rowNumber}: {$error}";
                        }
                    } else {
                        // Check for duplicate phones in the SAME FILE
                        $phone = $this->formatPhoneForPreview($row['parent_phone']);

                        // // Track phones we've seen in this file
                        // static $seenPhones = [];
                        // if (in_array($phone, $seenPhones)) {
                        //     $errors[] = "Row {$rowNumber}: Duplicate parent phone number '{$phone}' found in this file. Each parent must have a unique phone.";
                        //     continue;
                        // }
                        // $seenPhones[] = $phone;

                        // Prepare row for preview
                        $previewRow = [
                            'row_number' => $rowNumber,
                            'parent_name' => ucwords(strtolower($row['parent_first_name'] . ' ' . $row['parent_last_name'])),
                            'parent_gender' => ucwords(strtolower($row['parent_gender'])),
                            'parent_phone' => $phone,
                            'parent_email' => $row['parent_email'] ?? 'N/A',
                            'student_name' => ucwords(strtolower(
                                $row['student_first_name'] . ' ' .
                                    ($row['student_middle_name'] ?? '') . ' ' .
                                    $row['student_last_name']
                            )),
                            'student_gender' => ucwords(strtolower($row['student_gender'])),
                            'class_name' => $row['class_name'],
                            'student_group' => strtoupper($row['student_group']),
                            'status' => 'pending',
                            'original_data' => $row
                        ];
                        $previewData[] = $previewRow;
                    }
                } catch (Exception $e) {
                    $errors[] = "Row {$rowNumber}: " . $e->getMessage();
                }
            }

            // Store the file and data in session for actual import
            $fileName = 'import_' . time() . '_' . $file->getClientOriginalName();
            $file->storeAs('temp_imports', $fileName);

            session([
                'import_file' => $fileName,
                'import_preview_data' => $previewData,
                'import_errors' => $errors
            ]);

            return response()->json([
                'success' => true,
                'preview_data' => $previewData,
                'errors' => $errors,
                'total_rows' => count($rows),
                'valid_rows' => count($previewData),
                'invalid_rows' => count($errors),
                'file_name' => $fileName
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error reading file: ' . $e->getMessage()
            ], 422);
        }
    }

    // New function for actual import
    public function processImport(Request $request)
    {
        try {
            if (!session()->has('import_file') || !session()->has('import_preview_data')) {
                return response()->json([
                    'success' => false,
                    'message' => 'No import session found. Please upload the file again.'
                ], 422);
            }

            $fileName = session('import_file');
            $previewData = session('import_preview_data');

            // Get the file from storage
            $filePath = storage_path('app/temp_imports/' . $fileName);

            if (!file_exists($filePath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'File not found. Please upload again.'
                ], 422);
            }

            // Import the file
            Excel::import(new ParentStudentImport, $filePath);

            // Clean up
            Storage::delete('temp_imports/' . $fileName);
            session()->forget(['import_file', 'import_preview_data', 'import_errors']);

            return response()->json([
                'success' => true,
                'message' => 'Successfully imported ' . count($previewData) . ' records',
                'count' => count($previewData)
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Import failed: ' . $e->getMessage()
            ], 422);
        }
    }

    private function formatPhoneForPreview($phone)
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);

        if (strlen($phone) === 12 && str_starts_with($phone, '255')) {
            return '0' . substr($phone, 3);
        } elseif (strlen($phone) === 9) {
            return '0' . $phone;
        }

        return $phone;
    }

    public function parentExportFile()
    {
        if (!Storage::exists('templates/SampleFile.xlsx')) {
            // abort(404, 'File not found.');
            Alert()->toast('No file found, please try again', 'error');
            return back();
        }
        return Storage::download('templates/SampleFile.xlsx');
    }

    private function scanFileForViruses($file): array
    {
        // For production, use actual API
        if (app()->environment('production')) {
            $apiKey = config('services.virustotal.key');
            try {
                $response = Http::withHeaders(['x-apikey' => $apiKey])
                    ->attach('file', fopen($file->path(), 'r'))
                    ->post('https://www.virustotal.com/api/v3/files');

                if ($response->successful()) {
                    $scanId = $response->json()['data']['id'];
                    $analysis = Http::withHeaders(['x-apikey' => $apiKey])
                        ->get("https://www.virustotal.com/api/v3/analyses/{$scanId}");

                    return [
                        'clean' => $analysis->json()['data']['attributes']['stats']['malicious'] === 0,
                        'message' => $analysis->json()['data']['attributes']['status']
                    ];
                }
            } catch (\Exception $e) {
                return [
                    'clean' => false,
                    'message' => 'Scan failed: ' . $e->getMessage()
                ];
            }
        }

        // For local development, just mock a successful scan
        return ['clean' => true, 'message' => 'Development mode - scan bypassed'];
    }
}
