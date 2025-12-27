<?php

namespace App\Http\Controllers;

use App\Models\school;
use App\Models\User;
use App\Services\NextSmsService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use RealRashid\SweetAlert\Facades\Alert;
use Vinkla\Hashids\Facades\Hashids;

class AccountantsController extends Controller
{
    //

    public function index()
    {
        $accountant = User::where('usertype', 5)
                        ->orderBy('status', 'desc')
                        ->orderBy('first_name')
                        ->get();
        return view('Accountants.index', compact('accountant'));
    }

    public function registerAccountants (Request $request)
    {
        $this->validate($request, [
            'fname' => 'required|string|max:255',
            'lname' => 'required|string|max:255',
            'email' => 'nullable|string|email|max:255|unique:users',
            'phone' => 'required|string|max:15|unique:users|regex:/^[0-9]{10}$/',
            'gender' => 'required|string|in:male,female',
        ],
        [
            'fname.required' => 'First name is required',
            'lname.required' => 'Last name is required',
            'email.email' => 'Please provide a valid email address',
            'email.unique' => 'This email is already registered',
            'phone.required' => 'Phone number is required',
            'phone.unique' => 'This phone number is already registered',
        ]);

        try {
            $accountant = User::create([
                'first_name' => $request->fname,
                'last_name' => $request->lname,
                'email' => $request->email,
                'phone' => $request->phone,
                'gender' => $request->gender,
                'usertype' => $request->input('usertype', 5),
                'school_id' => Auth::user()->school_id,
                'password' => Hash::make($request->input('password', 'shule2025')),
            ]);

            // send SMS using nextSMS API ***********************************************
                $nextSmsService = new NextSmsService();
                $url = "https://shuleapp.tech/home";
                $school = school::findOrFail($accountant->school_id);
                $destination = $this->formatPhoneNumber($accountant->phone);

                $payload = [
                    'from' => $school->sender_id ?? "SHULE APP",
                    'to' => $destination,
                    'text' => "Welcome ". strtoupper($accountant->first_name) .", to ShuleApp. Your Login Details are; username: {$accountant->phone}, Password: shule2025. Visit {$url} to Login.",
                    'reference' => uniqid(),
                ];

                $response = $nextSmsService->sendSmsByNext($payload['from'], $payload['to'], $payload['text'], $payload['reference']);

                if(!$response['success']) {
                    Alert()->toast('SMS failed: '.$response['error'], 'error');
                    return back();
                }
                // Log::info('SMS sent to Accountant: '. $payload['to']);
            // ****************************************************************************

            Alert::toast('Accountant registered successfully', 'success');
            return redirect()->back();

        } catch (Exception $e) {
            Alert::toast($e->getMessage(), 'error');
            return back();
        }
    }

    public function blockAccountants($id, Request $request)
    {
        $decoded = Hashids::decode($id);
        $user = User::findOrFail($decoded[0]);

        if(! $user) {
            Alert()->toast('Invalid user information', 'error');
            return back();
        }

        try {
            $user->update([
                'status' => $request->input('status', 0)
            ]);

            Alert()->toast('Account suspended successfully', 'success');
            return back();

        } catch (Exception $e) {
            Alert()->toast($e->getMessage(), 'error');
            return back();
        }
    }


    public function unBlockAccountants($id, Request $request)
    {
        $decoded = Hashids::decode($id);
        $user = User::findOrFail($decoded[0]);

        if(! $user) {
            Alert()->toast('Invalid user information', 'error');
            return back();
        }

        try {
            $user->update([
                'status' => $request->input('status', 1)
            ]);
            Alert()->toast('Account activated successfully', 'success');
            return back();
        } catch (Exception $e) {
            Alert()->toast($e->getMessage(), 'error');
            return back();
        }
    }

    public function accountantProfile ($id)
    {
        $decoded = Hashids::decode($id);

        $user = User::findOrFail($decoded[0]);

        if(! $user) {
            Alert()->toast('Accountant information does not exists');
            return back();
        }

        return view('Accountants.profile', compact('user'));
    }

    public function updateAccountants ($id, Request $request)
    {
        $decoded = Hashids::decode($id);
        $user = User::findOrFail($decoded[0]);

        if(! $user) {
            Alert()->toast('Invalid user information', 'error');
            return back();
        }

        $this->validate($request, [
            'fname' => 'required|string|max:255',
            'lname' => 'required|string|max:255',
            'email' => 'nullable|string|email|max:255|unique:users,email,'.$user->id,
            'phone' => 'required|string|max:15|unique:users,phone,'.$user->id.'|regex:/^[0-9]{10}$/',
            'gender' => 'required|string|in:male,female',
            'image' => [
                'nullable',
                'file',
                'mimetypes:image/jpeg,image/png,image/jpg',
                'max:1024'
            ],
        ],
        [
            'fname.required' => 'First name is required',
            'lname.required' => 'Last name is required',
            'email.email' => 'Please provide a valid email address',
            'email.unique' => 'This email is already registered',
            'phone.required' => 'Phone number is required',
            'phone.unique' => 'This phone number is already registered',
            'phone.regex' => 'Phone number must be 10 digits',
            'image.mimetypes' => 'Image must be a file of type: jpg, png, jpeg',
            'image.max' => 'Image size must not exceed 1MB',
        ]);

        if($request->hasFile('image')) {
            // Scan the image for viruses
            $scanResult = $this->scanFileForViruses($request->file('image'));
            if (!$scanResult['clean']) {
                Alert()->toast('Image scan failed: ' . $scanResult['message'], 'error');
                return back();
            }
        }

        try {
            $user->update([
                'first_name' => $request->fname,
                'last_name' => $request->lname,
                'email' => $request->email,
                'phone' => $request->phone,
                'gender' => $request->gender,
            ]);

            if ($request->hasFile('image')) {
                // Log::info('Image upload detected');
                // Log::info('Image upload detected');
                if ($user->image && Storage::disk('public')->exists('profile/'.$user->image)) {
                    Storage::disk('public')->delete('profile/'.$user->image);
                }

                // Create unique logo name
                $imageFile = time() . '_' . uniqid() . '.' . $request->image->getClientOriginalExtension();

                // Store in storage/app/public/logo
                $request->image->storeAs('profile', $imageFile, 'public');
                $user->update(['image' => $imageFile]);
            }

            Alert::toast('Accountant information updated successfully', 'success');
            return redirect()->back();
        } catch (Exception $e) {
            Alert::toast($e->getMessage(), 'error');
            return back();
        }
    }

    public function deleteAccountants ($id)
    {
        $decoded = Hashids::decode($id);
        $user = User::findOrFail($decoded[0]);

        if(! $user) {
            Alert()->toast('Invalid user information', 'error');
            return back();
        }

        try {
            //check for existing file before delete
            if(!empty($user->image)) {
                $existingImagePath = public_path('assets/img/profile/' . $user->image);
                if (file_exists($existingImagePath)) {
                    unlink($existingImagePath);
                }
            }

            $user->delete();

            Alert()->toast('Accountant deleted successfully', 'success');
            return back();

        } catch (Exception $e) {
            Alert()->toast($e->getMessage(), 'error');
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
