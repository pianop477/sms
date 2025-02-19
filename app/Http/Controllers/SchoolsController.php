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
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RealRashid\SweetAlert\Facades\Alert;

class SchoolsController extends Controller
{

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
        try {
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

            //check if exists
            $schoolExists = school::where('school_reg_no', $request->reg_no)->exists();

            if($schoolExists) {
                Alert::error('Error', 'This school already exists');
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
            $users->password = Hash::make($request->input('password', 'shule@2024'));
            $users->school_id = $school->id;
            $saveData = $users->save();

            Alert::success('Success!', 'School information saved successfully');
            return redirect()->back();
        }
        catch(\Exception $e){
            Alert::error('Error', $e->getMessage());
            return back();
        }
    }

    /**
     * Display the resource.
     */
    public function show(school $school)
    {
        $managers = User::query()->join('schools', 'schools.id', '=', 'users.school_id')
                            ->select(
                                'users.*', 'schools.school_name', 'schools.school_reg_no', 'schools.logo'
                            )
                            ->where('users.usertype', 2)
                            ->where('users.school_id', $school->id)
                            ->get();
        $parents = Parents::query()->join('users', 'users.id', '=', 'parents.user_id')
                                    ->select(
                                        'parents.*', 'users.first_name', 'users.last_name', 'users.usertype'
                                    )
                                    ->where('users.usertype', 4)
                                    ->where('parents.school_id', $school->id)
                                    ->get();
        $teachers = Teacher::query()->join('users', 'users.id', '=', 'teachers.user_id')
                                    ->select('teachers.*', 'users.first_name', 'users.last_name', 'users.usertype')
                                    ->where('users.usertype', 3)
                                    ->where('teachers.school_id', $school->id)
                                    ->get();
        $students = Student::where('school_id', $school->id)->where('status', 1)->get();
        $courses = Subject::where('school_id', $school->id)->get();
        $classes = Grade::where('school_id', $school->id)->get();
        return view('Schools.show', compact('managers', 'parents', 'teachers', 'students', 'courses', 'classes', 'school'));
    }

    /**
     * Show the form for editing the resource.
     */
    public function invoceCreate(school $school)
    {
        //
        $managers = User::query()->join('schools', 'schools.id', '=', 'users.school_id')
                            ->select(
                                'users.*', 'schools.school_name', 'schools.school_reg_no', 'schools.logo'
                            )
                            ->where('users.usertype', 2)
                            ->where('users.school_id', $school->id)
                            ->get();
        $students = Student::where('school_id', $school->id)->where('status', 1)->get();
        return view('invoice.invoice', compact('school', 'managers', 'students'));
    }

    /**
     * Edit the resource in storage
    */

    public function edit (school $school)
    {
        return view('Schools.edit', ['school' => $school]);
    }

     /** Update the resource in storage.
     */

    public function updateSchool($school, Request $request)
    {
        //
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

        $schools = school::findOrFail($school);
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
        Alert::success('Success!', 'School information updated successfully');
        return redirect()->route('home');
    }

    /**
     * Remove the resource from storage.
     */
    public function destroy($id)
    {
        // abort(404);
        $school = school::find($id);

        if(! $school) {
            Alert::error('Error', 'This school is not found');
            return back();
        }
        $logoPath = public_path('assets/img/logo');
        $existingFile = $logoPath . '/' . $school->logo;
        if(file_exists($existingFile)) {
            unlink($existingFile);
        }
        //delete school
        $school->delete();
        Alert::success('School has been deleted successfully');
        return redirect()->back();

    }

    public function showFeedback ()
    {
        $message = message::latest()->paginate(15);
        return view('Schools.feedback', compact('message'));
    }

    public function deletePost (message $sms)
    {
        $message = message::findOrFail($sms->id);
        $message->delete();
        Alert::success('Success!', 'Post has been moved to trash');
        return back();
    }

    public function addActiveTime(Request $request, $id)
    {
        $request->validate([
            'school_id' => 'exists:schools,id',
            'service_duration' => 'required|integer',
        ]);

        $startDate = Carbon::now();
        $endDate = $startDate->copy()->addMonth($request->service_duration);

        $school = school::findOrFail($id);
        $school->service_start_date = $startDate;
        $school->service_end_date = $endDate;
        $school->service_duration = $request->service_duration;
        $school->status = 1;
        $school->save();

        //update users who disabled
        User::where('school_id', $id)->where('status', 0)->update(['status', 1]);

        Alert::success('Success!', 'Active time has been updated successfully');
        return back();
    }

    public function approveSchool ($id)
    {
        $school = school::find($id);

        return view('Schools.approval', compact('school'));
    }
}
