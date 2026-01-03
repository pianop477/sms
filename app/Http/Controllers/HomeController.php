<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\class_learning_courses;
use App\Models\Class_teacher;
use App\Models\Contract;
use App\Models\Grade;
use App\Models\Parents;
use App\Models\school;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Transport;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Ui\Presets\React;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class HomeController extends Controller
{

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
        {
            $user = Auth::user();
                //system adminstrator dashboard redirection --------------------------------------------
            if ($user->usertype == 1 ) {
                $schools = School::orderBy('school_name')->get();
                $teachers = Teacher::where('status', '=', 1)->where('status', 1)->get();
                $students = Student::where('status', '=', 1)->where('status', 1)->get();
                $parents = Parents::where('status', '=', 1)->where('status', 1)->get();
                $classes = Grade::where('status', '=', 1)->where('status', 1)->get();
                $subjects = Subject::where('status', '=', 1)->where('status', 1)->get();
                $buses = Transport::where('status', '=', 1)->where('status', 1)->get();
                // $school_details = school::orderBy('school_name')->get();
                return view('home', compact('teachers', 'students', 'parents', 'classes', 'subjects', 'buses', 'schools'));
            }
                 //manager dashboard redirection --------------------------------------
            elseif ($user->usertype == 2 ) {
                $teachers = Teacher::where('school_id', '=', $user->school_id)->where('status', '=', 1)->get();
                $parents = Parents::where('school_id', '=', $user->school_id)->where('status', '=', 1)->get();
                $students = Student::where('school_id', '=', $user->school_id)->where('status', '=', 1)->get();
                $classes = Grade::where('school_id', '=', $user->school_id)->where('status', '=', 1)->get();
                $subjects = Subject::where('school_id', '=', $user->school_id)->where('status', '=', 1)->get();
                $buses = Transport::where('school_id', '=', $user->school_id)->where('status', '=', 1)->get();
                $teacherByGender = Teacher::query()
                                ->join('users', 'users.id', '=', 'teachers.user_id')
                                ->select(
                                    'users.gender',
                                    DB::raw("COUNT(*) as teacher_count")
                                )
                                ->where('teachers.school_id', $user->school_id)
                                ->where('teachers.status', 1)
                                ->groupBy('users.gender')
                                ->get();

                $today = Carbon::today();
                $attendanceData = DB::table('attendances')
                                                    ->select('attendance_status', DB::raw('count(*) as count'))
                                                    ->where('school_id', $user->school_id)
                                                    ->whereDate('attendance_date', $today)
                                                    ->groupBy('attendance_status')
                                                    ->get();

                $attendanceCounts = [
                                    'present' => $attendanceData->where('attendance_status', 'present')->pluck('count')->first() ?? 0,
                                    'absent' => $attendanceData->where('attendance_status', 'absent')->pluck('count')->first() ?? 0,
                                    'permission' => $attendanceData->where('attendance_status', 'permission')->pluck('count')->first() ?? 0,
                                ];
                $studentsByClass = Student::query()
                                ->join('grades', 'grades.id', '=', 'students.class_id')
                                ->select('grades.class_code', DB::raw('COUNT(students.id) as student_count'))
                                ->where('students.school_id', '=', $user->school_id)
                                ->where('students.status', 1)
                                ->groupBy('grades.class_code')
                                ->orderBy('grades.class_code', 'ASC')
                                ->get();

                $totalMaleStudents = Student::where('gender', '=', 'male')->where('status', 1)->where('school_id', '=', $user->school_id)->count();
                $totalFemaleStudents = Student::where('gender', '=', 'female')->where('status', 1)->where('school_id', '=', $user->school_id)->count();

                $teacherQualifications = Teacher::where('school_id', '=', $user->school_id)
                                ->where('status', '=', 1)
                                ->select('qualification', \DB::raw('COUNT(*) as count'))
                                ->groupBy('qualification')
                                ->get();
                $studentsByClassAndGender = Student::query()
                                    ->join('grades', 'grades.id', '=', 'students.class_id')
                                    ->select(
                                        'grades.class_code',
                                        DB::raw("SUM(CASE WHEN students.gender = 'male' THEN 1 ELSE 0 END) as male_count"),
                                        DB::raw("SUM(CASE WHEN students.gender = 'female' THEN 1 ELSE 0 END) as female_count")
                                    )
                                    ->where('students.school_id', '=', $user->school_id)
                                    ->where('students.status', 1)
                                    ->groupBy('grades.class_code')
                                    ->orderBy('grades.class_code', 'ASC')
                                    ->get();
                            // Map the results to labels for easier use in the view
                $qualificationData = [
                                'masters' => $teacherQualifications->firstWhere('qualification', 1)?->count ?? 0,
                                'bachelor' => $teacherQualifications->firstWhere('qualification', 2)?->count ?? 0,
                                'diploma' => $teacherQualifications->firstWhere('qualification', 3)?->count ?? 0,
                                'certificate' => $teacherQualifications->firstWhere('qualification', 4)?->count ?? 0,
                            ];

                $chartData = [];
                    foreach ($studentsByClassAndGender as $row) {
                        $chartData[] = [
                            'category' => $row->class_code . ' (Male)',
                            'value' => $row->male_count,
                        ];
                    $chartData[] = [
                        'category' => $row->class_code . ' (Female)',
                        'value' => $row->female_count,
                        ];
                }
                return view('home', compact('teachers', 'attendanceCounts', 'today', 'parents', 'totalMaleStudents', 'totalFemaleStudents', 'teacherByGender', 'students', 'studentsByClass', 'classes', 'subjects', 'buses', 'chartData', 'qualificationData'));
            }
            //parents dashboard redirection ----------------------
            elseif ($user->usertype == 4 ) {
                $students = Student::query()->join('parents', 'parents.id', '=', 'students.parent_id')
                                        ->join('users', 'users.id', '=', 'parents.user_id')
                                        ->join('grades', 'grades.id', '=', 'students.class_id')
                                        ->leftJoin('transports', 'transports.id', '=', 'students.transport_id') // Use leftJoin here
                                        ->select(
                                        'students.*',
                                        'parents.address as parent_address',
                                        'users.phone as parent_phone',
                                        'grades.class_name',
                                        'grades.class_code',
                                        'transports.driver_name', 'transports.gender as driver_gender', 'transports.phone as driver_phone', 'transports.bus_no as bus_number',
                                        'transports.routine as bus_routine' // Select transport fields, if needed
                                        )
                                        ->where('parents.user_id', '=', $user->id)
                                        ->where('students.school_id', $user->school_id)
                                        ->whereIn('students.status', [0, 1, 2])
                                        ->orderBy('students.first_name', 'ASC')
                                        ->get();
                $classes = Grade::where('status', '=', 1)->where('school_id', $user->school_id)->get();
                $buses = Transport::where('status', '=', 1)->where('school_id', $user->school_id)->orderBy('bus_no', 'ASC')->get();
                return view('home', ['students' => $students, 'classes' => $classes, 'buses' => $buses]);
            }

            //teachers dashboard redirection ---------------------------------------------------------
            elseif ($user->usertype == 3) {
                $today = Carbon::today();
                $attendanceData = DB::table('attendances')
                                    ->select('attendance_status', DB::raw('count(*) as count'))
                                    ->where('school_id', $user->school_id)
                                    ->whereDate('attendance_date', $today)
                                    ->groupBy('attendance_status')
                                    ->get();

                $attendanceCounts = [
                    'present' => $attendanceData->where('attendance_status', 'present')->pluck('count')->first() ?? 0,
                    'absent' => $attendanceData->where('attendance_status', 'absent')->pluck('count')->first() ?? 0,
                    'permission' => $attendanceData->where('attendance_status', 'permission')->pluck('count')->first() ?? 0,
                ];

                // return $attendanceCounts;

                $teachers = Teacher::where('user_id', $user->id)->first();

                //check for contract status
                $contract = Contract::where('teacher_id', $teachers->id)->first();

                //return class teachers course assigned-------------------
                $courses = class_learning_courses::query()
                                                ->join('grades', 'grades.id', '=', 'class_learning_courses.class_id')
                                                ->join('subjects', 'subjects.id', '=', 'class_learning_courses.course_id')
                                                ->join('teachers', 'teachers.id', '=', 'class_learning_courses.teacher_id')
                                                ->select(
                                                    'class_learning_courses.*', 'subjects.course_name', 'grades.class_code',
                                                )
                                                ->where('class_learning_courses.teacher_id', $teachers->id)
                                                ->where('class_learning_courses.school_id', $user->school_id)
                                                ->get();
                // return $courses;
                //return myclass assigned as class teachers ------------------------
                $myClass = Class_teacher::query()
                            ->join('grades', 'grades.id', 'class_teachers.class_id')
                            ->join('teachers', 'teachers.id', 'class_teachers.teacher_id')
                            ->leftJoin('users', 'users.id', 'teachers.user_id')
                            ->select(
                                'class_teachers.*',
                                'grades.id as class_id',
                                'grades.class_name',
                                'grades.class_code',
                                'users.first_name',
                                'users.last_name',
                                'users.gender',
                                'users.phone'
                            )
                            ->where('class_teachers.teacher_id', '=', $teachers->id)
                            ->where('class_teachers.school_id', '=', $teachers->school_id)
                            ->get();

                //class teacher attendance daily report chart ************************
                    $today = Carbon::today()->format('Y-m-d'); // Get today's date
                    $teacher = Teacher::where('user_id', $user->id)->first();
                    $classTeacher = Class_teacher::where('teacher_id', $teacher->id)->first();
                    // Query to get the attendance counts
                    $attendanceStats = Attendance::query()
                                                ->join('teachers', 'teachers.id', '=', 'attendances.teacher_id')
                                                ->join('students', 'students.id', '=', 'attendances.student_id')
                                                ->select(
                                                    'attendances.*', 'teachers.id as teachers_id',
                                                    'students.gender', 'students.id as student_id',
                                                )
                                                ->where('attendances.school_id', $user->school_id)
                                                ->where('attendances.class_id', $classTeacher->class_id ?? '')
                                                ->where('attendances.class_group', $classTeacher->group ?? '')
                                                // ->where('attendances.teacher_id', $teacher->id)
                                                ->whereDate('attendances.attendance_date', $today)
                                                ->get();

                    // return $attendanceStats;
                    // Initialize an array to hold the attendance counts for each gender and status
                    $attendanceCount = [
                        'male' => [
                            'present' => 0,
                            'absent' => 0,
                            'permission' => 0,
                        ],
                        'female' => [
                            'present' => 0,
                            'absent' => 0,
                            'permission' => 0,
                        ]
                    ];

                    // Loop through the results and count the statuses based on gender
                    foreach ($attendanceStats as $attendance) {
                        $gender = $attendance->gender;
                        $status = $attendance->attendance_status;

                        if ($gender == 'male') {
                            $attendanceCount['male'][$status]++;
                        } elseif ($gender == 'female') {
                            $attendanceCount['female'][$status]++;
                        }
                    }

                    // Now we have the count of male and female students based on status (present, absent, permission)
                    // return $attendanceCount;


                $teacherByGender = Teacher::query()
                            ->join('users', 'users.id', '=', 'teachers.user_id')
                            ->select(
                                'users.gender',
                                DB::raw("COUNT(*) as teacher_count")
                            )
                            ->where('teachers.school_id', $user->school_id)
                            ->where('teachers.status', 1)
                            ->groupBy('users.gender')
                            ->get();
                    $classData = [];
                    foreach ($myClass as $class) {
                        $maleCount = Student::where('class_id', $class->class_id)
                                            ->where('group', $class->group)
                                            ->where('gender', 'male')
                                            ->where('school_id', $teachers->school_id)
                                            ->where('status', 1)
                                            ->count();
                        $femaleCount = Student::where('class_id', $class->class_id)
                                              ->where('group', $class->group)
                                              ->where('gender', 'female')
                                              ->where('school_id', $teachers->school_id)
                                              ->where('status', 1)
                                              ->count();

                        $classData[] = [
                            'class' => $class,
                            'maleCount' => $maleCount,
                            'femaleCount' => $femaleCount
                        ];
                    }

                $teachers = Teacher::where('school_id', '=', $user->school_id)->where('status', '=', 1)->get();
                $parents = Parents::where('school_id', '=', $user->school_id)->where('status', '=', 1)->get();
                $students = Student::where('school_id', '=', $user->school_id)->where('status', '=', 1)->get();
                $classes = Grade::where('school_id', '=', $user->school_id)->where('status', '=', 1)->get();
                $subjects = Subject::where('school_id', '=', $user->school_id)->where('status', '=', 1)->get();
                $buses = Transport::where('school_id', '=', $user->school_id)->where('status', '=', 1)->get();

                //summary counts ------------------
                $totalMaleStudents = Student::where('gender', '=', 'male')->where('status', 1)->where('school_id', '=', $user->school_id)->count();
                $totalFemaleStudents = Student::where('gender', '=', 'female')->where('status', 1)->where('school_id', '=', $user->school_id)->count();
                $studentsByClassAndGender = Student::query()
                                    ->join('grades', 'grades.id', '=', 'students.class_id')
                                    ->select(
                                        'grades.class_code',
                                        DB::raw("SUM(CASE WHEN students.gender = 'male' THEN 1 ELSE 0 END) as male_count"),
                                        DB::raw("SUM(CASE WHEN students.gender = 'female' THEN 1 ELSE 0 END) as female_count")
                                    )
                                    ->where('students.school_id', '=', $user->school_id)
                                    ->where('students.status', 1)
                                    ->groupBy('grades.class_code')
                                    ->orderBy('grades.class_code', 'ASC')
                                    ->get();
                $teacherQualifications = Teacher::where('school_id', '=', $user->school_id)
                                ->where('status', '=', 1)
                                ->select('qualification', DB::raw('COUNT(*) as count'))
                                ->groupBy('qualification')
                                ->get();

                $studentsByClass = Student::query()
                                ->join('grades', 'grades.id', '=', 'students.class_id')
                                ->select('grades.class_code', DB::raw('COUNT(students.id) as student_count'))
                                ->where('students.school_id', '=', $user->school_id)
                                ->where('students.status', 1)
                                ->groupBy('grades.class_code')
                                ->orderBy('grades.class_code', 'ASC')
                                ->get();

                            // Map the results to labels for easier use in the view
                $qualificationData = [
                                'masters' => $teacherQualifications->firstWhere('qualification', 1)?->count ?? 0,
                                'bachelor' => $teacherQualifications->firstWhere('qualification', 2)?->count ?? 0,
                                'diploma' => $teacherQualifications->firstWhere('qualification', 3)?->count ?? 0,
                                'certificate' => $teacherQualifications->firstWhere('qualification', 4)?->count ?? 0,
                            ];
                $chartData = [];
                    foreach ($studentsByClassAndGender as $row) {
                        $chartData[] = [
                            'category' => $row->class_code . ' (Male)',
                            'value' => $row->male_count,
                        ];
                    $chartData[] = [
                        'category' => $row->class_code . ' (Female)',
                        'value' => $row->female_count,
                        ];
                }
                //end of summary -----------------
                return view('home', compact('courses', 'contract', 'attendanceCounts', 'attendanceCount', 'today', 'myClass', 'teacherByGender', 'classes', 'teachers', 'students', 'classes', 'subjects', 'studentsByClass',
                            'parents', 'buses', 'totalMaleStudents', 'chartData', 'totalFemaleStudents', 'classData', 'qualificationData'));
            }

            elseif ($user->usertype == 5) {

                $categories = [];
                $daily = 0;
                $monthly = 0;
                $yearly = 0;
                $recent = [];
                $last7DaysExpenses = [];

                $token = session('finance_api_token');

                // dd($token);
                try {
                    $response = Http::withToken(session('finance_api_token'))
                                    ->get(config('app.finance_api_base_url'). '/accountant-dashboard', [
                                    'school_id' => $user->school_id,
                                ]);

                    if ($response->successful()) {
                        $data = $response->json();
                        $categories = $data['categories'] ?? [];
                        $daily = $data['daily_expenses'] ?? 0;
                        $monthly = $data['monthly_expenses'] ?? 0;
                        $yearly = $data['yearly_expenses'] ?? 0;
                        $recent = $data['recent_expenses'] ?? [];
                        $last7DaysExpenses = $data['last_7_days_expenses'] ?? [];

                        // Continue with your logic here...

                    } else {
                        $errorData = $response->json();
                        Alert()->toast($errorData['message'] ?? 'Request failed', 'error');
                        return back();
                    }
                } catch (\Throwable $e) {
                    // Log::error("Dashboard API error: " . $e->getMessage());
                    Alert()->toast('Unable to connect to finance service', 'error');
                    return to_route('home');
                }

                $students = Student::where('school_id', $user->school_id)
                                    ->where('status', 1)
                                    ->get();

                return view('home', compact('categories', 'daily', 'monthly', 'yearly', 'recent', 'students', 'last7DaysExpenses'));
            }

            else {
                Auth::logout();
                return redirect()->route('login')->with('error', 'You do not have access to the system. Contact System Administrator');
            }

        }

        public function changepassword() {
            return view('profile.change-password');
        }

    public function storePassword(Request $request)
    {

        $this->validate($request, [
            'current_password' => ['required', 'string'],
            'new_password' => ['required', 'string', Password::min(8)->letters()->numbers()],
            'confirm_password' => ['required', 'same:new_password'],
        ], [
            'current_password.required' => 'Current password is required',
            'new_password.required' => 'New password is required',
            'new_password.min' => 'New password must be at least 8 characters long',
            'confirm_password.required' => 'Confirm password is required',
            'confirm_password.same' => 'Confirm password must match the new password',
        ]);

        try {
                if(!(Hash::check($request->get('current_password'), Auth::user()->password))) {
                    // return back()->with('error', 'Current password does not match');
                    Alert()->toast('Current password does not match', 'error');
                    return back();
                }

                if(strcmp($request->get('current_password'), $request->get('new_password')) == 0) {
                    // return back()->with('error', 'This password you have already used, choose new one');
                    Alert()->toast('This password you have already used, choose new one', 'error');
                    return back();
                }
                $user = Auth::user();
                $user->password = Hash::make($request->new_password);
                $new_password = $user->save();

                if($new_password) {
                    Alert()->toast('Password Updated successfully', 'success');
                    // Auth::logout();
                    return to_route('home');
                } else {
                    Alert()->toast('Failed to change password', 'error');
                    return back();
                }
        } catch (\Exception $e) {
            Alert::error('Errors', $e->getMessage());
            return back();
        }

    }

    public function showProfile()
    {
        $user = Auth::user();

        $userAccount = User::query()
                        ->leftJoin('teachers', 'users.id', '=', 'teachers.user_id')
                        ->leftJoin('parents', 'users.id', '=', 'parents.user_id')
                        ->leftJoin('roles', 'roles.id', '=', 'teachers.role_id')
                        ->select(
                            'users.*',
                            'teachers.user_id as teacher_id',
                            'teachers.address as teacher_address',
                            'teachers.member_id', 'teachers.qualification',
                            'parents.address as parent_address',
                            'roles.role_name'
                        )
                        ->findOrFail($user->id);

        return view('profile.index', ['user' => $userAccount]);
    }


    public function updateProfile(Request $request, $user)
    {
        $userData = User::findOrFail($user);

        // Only get teacher/parent if the user is actually that type
        $teacher = null;
        $parent = null;

        if($userData->usertype == 3) {
            $teacher = Teacher::where('user_id', $userData->id)->first();
        }
        elseif($userData->usertype == 4) {
            $parent = Parents::where('user_id', $userData->id)->first();
        }

        $validated = $request->validate([
            'fname' => 'required|string|max:255',
            'lname' => 'required|string|max:255',
            'phone' => 'required|regex:/^[0-9]{10}$/|unique:users,phone,' . $userData->id,
            'email' => 'nullable|unique:users,email,' . $userData->id,
            'gender' => 'required|in:female,male',
            'image' => [
                'nullable',
                'file',
                'mimetypes:image/jpeg,image/png,image/jpg',
                'max:1024'
            ],
            'address' => 'nullable|string|max:255',
        ],
        [
            'fname.required' => 'First name is required',
            'lname.required' => 'Last name is required',
            'phone.required' => 'Phone number is required',
            'phone.regex' => 'Phone number must be 10 digits',
            'email.unique' => 'Email already exists',
            'image.mimetypes' => 'Image must be a file of type: jpg, png, jpeg',
            'image.max' => 'Image size must not exceed 1MB',
        ]);

        // scan image file for virus
        if($request->hasFile('image')) {
            $scanResult = $this->scanFileForViruses($request->file('image'));
            if (!$scanResult['clean']) {
                Alert()->toast('Uploaded file is infected with a virus: ' . $scanResult['message'], 'error');
                return back();
            }
        }

        // Update user data
        $userData->first_name = $request->fname;
        $userData->last_name = $request->lname;
        $userData->phone = $request->phone;
        $userData->email = $request->email;
        $userData->gender = $request->gender;

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old logo if it exists
            if ($userData->image && Storage::disk('public')->exists('profile/'.$userData->image)) {
                Storage::disk('public')->delete('profile/'.$userData->image);
            }

            // Create unique logo name
            $imageFile = time() . '_' . uniqid() . '.' . $request->image->getClientOriginalExtension();

            // Store in storage/app/public/logo
            $request->image->storeAs('profile', $imageFile, 'public');
            $userData->image = $imageFile;
        }

        // Save user data
        $userData->save();

        // Update address based on user type
        if($request->address) {
            if($userData->usertype == 3 && $teacher) {
                $teacher->address = $request->address;
                $teacher->save();
            }
            elseif($userData->usertype == 4 && $parent) {
                $parent->address = $request->address;
                $parent->save();
            }
        }

        Alert()->toast('Profile Updated successfully', 'success');
        return back();
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
