<?php

namespace App\Http\Controllers;

use App\Models\Grade;
use App\Models\Parents;
use App\Models\school;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Http\Request;
use App\Models\User;
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
        $request->validate([
            'name' => 'required|string|max:255',
            'reg_no' => 'required|string|max:255',
            'logo' => 'image|max:4096'
        ]);

        $school = new school();
        $school->school_name = $request->name;
        $school->school_reg_no = $request->reg_no;
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
        Alert::success('Success!', 'School information saved successfully');
        return back();
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
        $students = Student::where('school_id', $school->id)->get();
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
        $students = Student::where('school_id', $school->id)->get();
        return view('invoice.invoice', compact('school', 'managers', 'students'));
    }

    /**
     * Update the resource in storage.
     */
    public function update(Request $request)
    {
        //
    }

    /**
     * Remove the resource from storage.
     */
    public function destroy(): never
    {
        abort(404);
    }
}
