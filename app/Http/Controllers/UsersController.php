<?php

namespace App\Http\Controllers;

use App\Models\Parents;
use App\Models\school;
use Illuminate\Http\Request;
use App\Models\User;
use App\Services\BeemSmsService;
use App\Services\NextSmsService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rules\Unique;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\FormattedNumber;
use RealRashid\SweetAlert\Facades\Alert;
use Vinkla\Hashids\Facades\Hashids;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;

class UsersController extends Controller
{
    protected $beemSmsService;
    protected $nextSmsService;

    public function __construct(BeemSmsService $beemSmsService, NextSmsService $nextSmsService)
    {
        $this->beemSmsService = $beemSmsService;
        $this->nextSmsService = $nextSmsService;
    }

    // get schools list for user registration and show form ********************************************
    public function index() {
        $schools = school::where('status', '=', 1)->orderBy('school_name')->get();
        return view('auth.register', ['schools' => $schools]);
    }

    // register parents out of the system, in the sign up page *****************************************
    public function create(Request $req)
    {
        $this->validate($req, [
            'fname' => ['required', 'string', 'max:255'],
            'lname' => ['required','string', 'max:255'],
            'email' => ['nullable', 'string', 'unique:users,email'],
            'phone' => ['required', 'regex:/^[0-9]{10}$/', 'unique:users,phone'],
            'gender' => ['required', 'string', 'max:255'],
            'school' => ['required', 'integer', 'exists:schools,id'],
            'password' => ['required', 'string', Password::min(8)
                ->letters()
                ->mixedCase()
                ->numbers()
            ],
            'password_confirmation' => ['same:password'],
            'image' => ['nullable', 'image', 'mimes:jpg,png,jpeg', 'max:1024'],
            'street' => ['required', 'string', 'max:255'],
        ], [
            'fname.required' => 'First name is required',
            'lname.required' => 'Last name is required',
            'email.unique' => 'Email already exists',
            'phone.required' => 'Phone number is required',
            'phone.regex' => 'Phone number must be 10 digits',
            'phone.unique' => 'Phone number already exists',
        ]);

        $parentExists = User::where('phone', $req->phone)->where('school_id', $req->school)->exists();
        if($parentExists) {
            Alert()->toast('This accounts already exists', 'error');
            return back();
        }

        if($req->hasFile('image')) {
            $scanResult = $this->scanFileForViruses($req->file('image'));
            if (!$scanResult['clean']) {
                Alert()->toast('Uploaded file is infected with a virus: ' . $scanResult['message'], 'error');
                return back();
            }
        }

        $users = new User();
        $users->first_name = $req->fname;
        $users->last_name = $req->lname;
        $users->email = $req->email;
        $users->phone = $req->phone;
        $users->gender = $req->gender;
        $users->usertype = $req->input('usertype', 4);
        $users->school_id = $req->school;
        $users->password = Hash::make($req->password);
        $users->school_id = $req->school;

        if ($req->hasFile('image')) {
            $image = $req->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $imageDestinationPath = public_path('assets/img/profile');

            // Ensure the directory exists
            if (!file_exists($imageDestinationPath)) {
                mkdir($imageDestinationPath, 0775, true);
            }

            // Move the file
            $image->move($imageDestinationPath, $imageName);

            // Save the file name to the database
            $users->image = $imageName;
        }

        $users->save();

        $parents = new Parents();
        $parents->user_id = $users->id;
        $parents->school_id = $users->school_id;
        $parents->address = $req->street;
        $parents->save();
        // return redirect()->back()->with('success', 'User registered successfully, Login now');

        $url = "https://shuleapp.tech"; //url for application
        //send sms after registration using Beem API *******************************************************
        $beemSmsService = new BeemSmsService();
        $sourceAddr = $school->sender_id ?? 'shuleApp'; // Get sender ID
            $formattedPhone = $this->formatPhoneNumber($users->phone); // Validate phone before sending

            // Check if phone number is valid after formatting
            if (strlen($formattedPhone) !== 12 || !preg_match('/^255\d{9}$/', $formattedPhone)) {
                // Log('Invalid phone number format', ['phone' => $formattedPhone]);
            } else {
                $recipients = [
                    [
                        'recipient_id' => 1,
                        'dest_addr' => $formattedPhone, // Use validated phone number
                    ]
                ];
            }

            $message = "Welcome to ShuleApp, Your Login details are; Username: {$users->phone}, Password: ". htmlspecialchars($req->password) .". Visit {$url} to Login.";
            $response = $beemSmsService->sendSms($sourceAddr, $message, $recipients);

        // send sms using NextSMS API *****************************************************************************
        $nextSmsService = new NextSmsService();
        $dest = $this->formatPhoneNumber($users->phone);
        $payload = [
            'from' => $school->sender_id ?? 'SHULE APP',
            'to' => $dest,
            'text' => "Welcome to ShuleApp, Your Login details are; Username: {$users->phone}, Password: ". htmlspecialchars($req->password) .". Visit {$url} to Login.",
            'reference' => uniqid(),
        ];

        // $response = $nextSmsService->sendSmsByNext($payload['from'], $payload['to'], $payload['text'], $payload['reference']);


        Alert()->toast('Your Account has been saved successfully', 'success');
        Auth::login($users);
        return redirect()->route('home');

    }

    // managers registration form, but now this is not working any more *************************************
    public function managerForm()
    {
        $schools = school::where('status', '=', 1)->orderBy('school_name', 'ASC')->get();
        $managers = User::query()
                        ->join('schools', 'schools.id', '=', 'users.school_id')
                        ->select('users.*', 'schools.school_name', 'schools.school_reg_no')
                        ->where('users.usertype', '=', 2)
                        ->orderBy('users.first_name', 'ASC')
                        ->get();

        return view('Managers.index', ['managers' => $managers], ['schools' => $schools]);
    }

    // showing error page for restricted areas user to access ****************************************
    public function errorPage()
    {
        return view('Error.403');
    }

    // Admin manage managers list *******************************************************************
    public function manageAdminAccounts()
    {
        $users = User::where('usertype', 1)->orderBy('first_name')->get();
        return view('Admin.index', compact('users'));
    }

    // add super admin account to manage all schools ************************************************
    public function addAdminAccount(Request $request)
    {
        $this->validate($request, [
            'fname' => 'required|string|max:255',
            'lname' => 'required|string|max:255',
            'email' => 'nullable|unique:users,email',
            'gender' => 'required|string|max:10',
            'phone' => 'required|regex:/^[0-9]{10}$/|unique:users,phone',
        ], [
            'fname.required' => 'First name is required',
            'lname.required' => 'Last name is required',
            'email.unique' => 'Email already exists',
            'phone.required' => 'Phone number is required',
            'phone.regex' => 'Phone number must be 10 digits',
            'phone.unique' => 'Phone number already exists',
        ]);

        $isExisting = User::where('phone', $request->phone)->exists();
        if($isExisting) {
            Alert()->toast('User information already exist', 'error');
            return back();
        }

        $user = User::create([
            'first_name' => $request->input('fname'),
            'last_name' => $request->input('lname'),
            'email' => $request->input('email'),
            'gender' => $request->input('gender'),
            'phone' => $request->input('phone'),
            'usertype' => $request->input('usertype', 1),
            'password' => Hash::make($request->input('password', 'shule2025')),
        ]);

        // use nextSMS API to send sms
        $nextSmsService = new NextSmsService();
        $url = "https://shulapp.tech";
        $sender = "SHULE APP";
        $destination = $this->formatPhoneNumber($user->phone);
        $message = "Hello!". strtoupper($user->first_name). " Welcome to ShuleApp System. Your Username: {$user->phone} and Password: shule2025. Click here {$url} to login";
        $reference = uniqid();
        //create payload
        $payload = [
            'from' => $sender,
            'to' => $destination,
            'text' => $message,
            'reference' => $reference
        ];

        $response = $nextSmsService->sendSmsByNext($payload['from'], $payload['to'], $payload['text'], $payload['reference']);

        Alert()->toast('User admin saved successfully', 'success');
        return back();

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

    // block user account ***********************************************************************
    public function blockAdminAccount(Request $request, $user)
    {
        $decoded = Hashids::decode($user);
        $user = User::find($decoded[0]);
        $loggedUser = Auth::user();
        $status = 0;

        if(! $user) {
            ALert()->toast('No such user was found', 'error');
            return back();
        }

        if($loggedUser->id == $user->id) {
            Alert()->toast('You cannot block your own account', 'error');
            return back();
        }

        $user->update([
            'status' => $status
        ]);

        Alert()->toast('Admin Account has been blocked successufully', 'success');
        return back();

    }

    // unblock user account **************************************************************************
    public function unblockAdminAccount(Request $request, $user)
    {
        $decoded = Hashids::decode($user);
        $user = User::find($decoded[0]);
        $status = 1;

        if(! $user) {
            ALert()->toast('No such user was found', 'error');
            return back();
        }

        $user->update([
            'status' => $status
        ]);

        Alert()->toast('Admin Account has been unblocked successufully', 'success');
        return back();

    }

    // delete user account ****************************************************************************
    public function deleteAdminAccount(Request $request, $user)
    {
        $decoded = Hashids::decode($user);
        $user = User::find($decoded[0]);

        $loggedUser = Auth::user();


        if(! $user) {
            ALert()->toast('No such user was found', 'error');
            return back();
        }

        if($loggedUser->id == $user->id) {
            Alert()->toast('You cannot delete your own account', 'error');
            return back();
        }

        $user->delete();

        Alert()->toast('Admin Account has been deleted successufully', 'success');
        return back();

    }

    public function constructionPage ()
    {
        return view('Error.construction');
    }

    public function editAdminAccount($user)
    {
        $id = Hashids::decode($user);
        $user = User::findOrFail($id[0]);
        return view('Admin.edit', compact('user'));
    }


    public function updateAdminAccount(Request $request, $user)
    {
        $userId = Hashids::decode($user);
        $userInfo = User::findOrFail($userId[0]);

        if(!$userInfo) {
            Alert()->toast('User information does not exist', 'error');
            return to_route('admin.accounts');
        }

        $this->validate($request, [
            'fname' => 'required|string|max:255',
            'lname' => 'required|string|max:255',
            'email' => 'nullable|unique:users,email,'.$userInfo->id,
            'gender' => 'required|string|max:10',
            'phone' => 'required|regex:/^[0-9]{10}$/|unique:users,phone,'.$userInfo->id,
            'image' => 'nullable|image|max:1024|mimes:jpg,png,tiff,jpeg',
        ], [
            'fname.required' => 'First name is required',
            'lname.required' => 'Last name is required',
            'email.unique' => 'Email already exists',
            'phone.required' => 'Phone number is required',
            'phone.regex' => 'Phone number must be 10 digits',
            'phone.unique' => 'Phone number already exists',
            'image.image' => 'Your file must be an image file type',
            'image.max' => 'Image file must not exceed 1 Megabyte',
            'image.mimes' => 'Your file must be type of jpg, jpeg, png, tiff'
        ]);

        $updateData = [
            'first_name' => $request->fname,
            'last_name' => $request->lname,
            'phone' => $request->phone,
            'email' => $request->email,
            'gender' => $request->gender,
        ];

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $imageDestinationPath = public_path('assets/img/profile');

            if (!file_exists($imageDestinationPath)) {
                mkdir($imageDestinationPath, 0755, true);
            }

            if ($userInfo->image && file_exists($imageDestinationPath.'/'.$userInfo->image)) {
                unlink($imageDestinationPath.'/'.$userInfo->image);
            }

            $image->move($imageDestinationPath, $imageName);
            $updateData['image'] = $imageName;
        }

        $userInfo->update($updateData);

        Alert()->toast('User account updated successfully', 'success');
        return to_route('admin.account.edit', ['user' => $user]);
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
                    'message' => 'Scan failed: '.$e->getMessage()
                ];
            }
        }

        // For local development, just mock a successful scan
        return ['clean' => true, 'message' => 'Development mode - scan bypassed'];
    }
}
