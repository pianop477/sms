<?php

namespace App\Http\Controllers;

use App\Models\Class_teacher;
use App\Models\Grade;
use App\Models\Parents;
use App\Models\school;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Transport;
use App\Models\User;
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

            if ($user->usertype == 1 && $user->status == 1) {
                $schools = School::where('status', '=', 1)->get();
                $teachers = Teacher::where('status', '=', 1)->get();
                $students = Student::where('status', '=', 1)->get();
                $parents = Parents::where('status', '=', 1)->get();
                $classes = Grade::where('status', '=', 1)->get();
                $subjects = Subject::where('status', '=', 1)->get();
                $buses = Transport::where('status', '=', 1)->get();
                $school_details = school::orderBy('school_name')->get();
                // $school_details = User::query()->join('schools', 'schools.id', '=', 'users.school_id')
                //                                 ->select('users.*', 'schools.school_name', 'schools.school_reg_no')
                //                                 ->where('users.usertype', '=', 2)
                //                                 ->orderBy('users.first_name')
                //                                 ->get();
                return view('home', compact('teachers', 'students', 'parents', 'classes', 'subjects', 'buses', 'schools', 'school_details'));
            } elseif ($user->usertype == 2 && $user->status == 1) {
                $teachers = Teacher::where('school_id', '=', $user->school_id)->where('status', '=', 1)->get();
                $parents = Parents::where('school_id', '=', $user->school_id)->where('status', '=', 1)->get();
                $students = Student::where('school_id', '=', $user->school_id)->where('status', '=', 1)->get();
                $classes = Grade::where('school_id', '=', $user->school_id)->where('status', '=', 1)->get();
                $subjects = Subject::where('school_id', '=', $user->school_id)->where('status', '=', 1)->get();
                $buses = Transport::where('school_id', '=', $user->school_id)->where('status', '=', 1)->get();
                return view('home', compact('teachers', 'parents', 'students', 'classes', 'subjects', 'buses'));
            }
            elseif ($user->usertype == 4 && $user->status == 1) {
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
                                        ->orderBy('students.first_name', 'ASC')
                                        ->get();
                $classes = Grade::where('status', '=', 1)->get();
                $buses = Transport::where('status', '=', 1)->orderBy('driver_name', 'ASC')->get();
                return view('home', ['students' => $students, 'classes' => $classes, 'buses' => $buses]);
            }

            if ($user->usertype == 3 && $user->status == 1) {
                $teachers = Teacher::where('user_id', $user->id)->first();

                $courses = Subject::query()
                    ->join('grades', 'grades.id', 'subjects.class_id')
                    ->join('teachers', 'teachers.id', 'subjects.teacher_id')
                    ->leftJoin('users', 'users.id', 'teachers.user_id')
                    ->select(
                        'subjects.*',
                        'grades.id as class_id',
                        'grades.class_name',
                        'grades.class_code',
                        'users.first_name',
                        'users.last_name',
                        'users.gender',
                        'users.phone'
                    )
                    ->where('subjects.teacher_id', $teachers->id)
                    ->where('subjects.school_id', $teachers->school_id)
                    ->where('subjects.status', '=', 1)
                    ->get();

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
                $teachers = Teacher::where('school_id', '=', $user->school_id)->where('status', '=', 1)->get();
                $parents = Parents::where('school_id', '=', $user->school_id)->where('status', '=', 1)->get();
                $students = Student::where('school_id', '=', $user->school_id)->where('status', '=', 1)->get();
                $classes = Grade::where('school_id', '=', $user->school_id)->where('status', '=', 1)->get();
                $subjects = Subject::where('school_id', '=', $user->school_id)->where('status', '=', 1)->get();
                $buses = Transport::where('school_id', '=', $user->school_id)->where('status', '=', 1)->get();

                $classes = Grade::where('status', '=', 1)
                    ->where('school_id', $teachers->school_id)
                    ->get();

                return view('home', [
                    'courses' => $courses,
                    'myClass' => $myClass,
                    'classes' => $classes,
                    'teachers' => $teachers,
                    'students' => $students,
                    'classes' => $classes,
                    'subjects' => $subjects,
                    'parents' => $parents,
                    'buses' => $buses
                ]);
            }

            else {
                Auth::logout(); // You can use the Auth facade to logout
                return redirect()->route('login')->with('error', 'Account suspended, contact system administrator');
            }

        }

        public function changepassword() {
            return view('profile.change-password');
        }

    public function storePassword(Request $request) {
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:6',
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

        }

        public function showProfile()
        {
            $user = Auth::user();
            return view('profile.index', compact('user'));
        }


        public function updateProfile(Request $request, $user)
        {
            $request->validate([
                'fname' => 'required|string',
                'lname' => 'required|string',
                'phone' => 'required|string',
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
