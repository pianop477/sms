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
use Illuminate\Support\Facades\Http;
use RealRashid\SweetAlert\Facades\Alert;

class SchoolsController extends Controller
{

    public function index() {
        $response = Http::get('https://restcountries.com/v3.1/all');
        $countries = $response->json();

         // Optionally, filter by continent
         $africanCountries = array_filter($countries, function ($country) {
            return isset($country['region']) && $country['region'] == 'Africa';
        });
         // Sort countries by name in ascending order
         usort($africanCountries, function ($a, $b) {
            return strcmp($a['name']['common'], $b['name']['common']);
        });

        $schools = school::orderBy('school_name', 'ASC')->orderBy('school_name', 'ASC')->get();
        return view('Schools.index', ['schools' => $schools, 'countries' => $africanCountries]);
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
            'logo' => 'image|max:4096',
            'postal' => 'required|string',
            'postal_name' => 'required|string',
            'country' => 'required|string'
        ]);

        $school = new school();
        $school->school_name = $request->name;
        $school->school_reg_no = $request->reg_no;
        $school->postal_address = $request->postal;
        $school->postal_name = $request->postal_name;
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
     * Edit the resource in storage
    */

    public function edit (school $school)
    {
        $response = Http::get('https://restcountries.com/v3.1/all');
        $countries = $response->json();

         // Optionally, filter by continent
         $africanCountries = array_filter($countries, function ($country) {
            return isset($country['region']) && $country['region'] == 'Africa';
        });
         // Sort countries by name in ascending order
         usort($africanCountries, function ($a, $b) {
            return strcmp($a['name']['common'], $b['name']['common']);
        });

        return view('Schools.edit', ['countries' => $africanCountries, 'school' => $school]);
    }

     /** Update the resource in storage.
     */

    public function updateSchool($school, Request $request)
    {
        //
        $request->validate([
            'name' => 'required|string|max:255',
            'reg_no' => 'required|string|max:255',
            'logo' => 'image|max:4096',
            'postal' => 'required|string',
            'postal_name' => 'required|string',
            'country' => 'required|string'
        ]);

        $schools = school::findOrFail($school);
        $schools->school_name = $request->name;
        $schools->school_reg_no = $request->reg_no;
        $schools->postal_address = $request->postal;
        $schools->postal_name = $request->postal_name;
        $schools->country = $request->country;
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
        $schools->save();
        Alert::success('Success!', 'School information updated successfully');
        return redirect()->route('home');
    }

    /**
     * Remove the resource from storage.
     */
    public function destroy(): never
    {
        abort(404);
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
}
