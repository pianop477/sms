<?php

namespace App\Http\Controllers;

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
                                        ->where('students.status', 1)
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
                                ->select('qualification', \DB::raw('COUNT(*) as count'))
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
                return view('home', compact('courses', 'contract', 'attendanceCounts', 'today', 'myClass', 'teacherByGender', 'classes', 'teachers', 'students', 'classes', 'subjects', 'studentsByClass',
                            'parents', 'buses', 'totalMaleStudents', 'chartData', 'totalFemaleStudents', 'classData', 'qualificationData'));
            }

        }

        public function changepassword() {
            return view('profile.change-password');
        }

    public function storePassword(Request $request) {
        try {
            $request->validate([
                'current_password' => 'required|string',
                'new_password' => 'required|string|min:8',
                'confirm_password' => 'required|same:new_password'
            ]);

                if(!(Hash::check($request->get('current_password'), Auth::user()->password))) {
                    // return back()->with('error', 'Current password does not match');
                    Alert::error('Error', 'Current password does not match');
                    return back();
                }

                if(strcmp($request->get('current_password'), $request->get('new_password')) == 0) {
                    // return back()->with('error', 'This password you have already used, choose new one');
                    Alert::error('Error', 'This password you have already used, choose new one');
                    return back();
                }
                $user = Auth::user();
                $user->password = Hash::make($request->new_password);
                $new_password = $user->save();

                if($new_password) {
                    Alert::success('Password Updated successfully');
                    Auth::logout();
                    return redirect()->route('login');
                }
        } catch (\Exception $e) {
            Alert::error('Errors', $e->getMessage());
            return back();
        }

    }

        public function showProfile()
        {
            $user = Auth::user();
            return view('profile.index', compact('user'));
        }


        public function updateProfile(Request $request, $user)
        {
            $request->validate([
                'fname' => 'required|string|max:255',
                'lname' => 'required|string|max:255',
                'phone' => 'required|string|min:10|max:15',
                'image' => 'nullable|image|max:2048'
            ]);

            $userData = User::findOrFail($user);
            $userData->first_name = $request->fname;
            $userData->last_name = $request->lname;
            $userData->phone = $request->phone;

            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $imageDestinationPath = public_path('assets/img/profile');

                // Ensure the directory exists
                if (!file_exists($imageDestinationPath)) {
                    mkdir($imageDestinationPath, 0775, true);
                }

                //check for existing image file
                if(! empty($userData->image)) {
                    $existingFile = $imageDestinationPath . '/' . $userData->image;
                    if(file_exists($existingFile)) {
                        unlink($existingFile);
                    }
                }

                // Move the file
                $image->move($imageDestinationPath, $imageName);

                // Save the file name to the database
                $userData->image = $imageName;
            }

            // Update user data
            $saveData = $userData->save();
            if($saveData) {
                Alert::success('Success', 'Profile Updated successfully');
                return back();
            }
        }
}
