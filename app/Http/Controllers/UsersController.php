<?php

namespace App\Http\Controllers;

use App\Models\Parents;
use App\Models\school;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use RealRashid\SweetAlert\Facades\Alert;

class UsersController extends Controller
{
    //
    public function index() {
        $schools = school::where('status', '=', 1)->orderBy('school_name', 'ASC')->get();
        return view('auth.register', ['schools' => $schools]);
    }

    public function create(Request $req) {
        $req->validate([
            'fname' => 'required|string|max:25',
            'lname' => 'required|string|max:25',
            'email' => 'required|string|unique:users,email',
            'phone' => 'required|string|min:10|max:10',
            'gender' => 'required|string|max:6',
            'usertype' => 'required|max:6|integer',
            'school' => 'required|integer|exists:schools,id',
            'password' => 'required|min:8',
            'password_confirmation' => 'same:password',
            'image' => 'nullable|image|max:2048',
            'street' => 'required|string|max:15',
        ]);


        $users = new User();
        $users->first_name = $req->fname;
        $users->last_name = $req->lname;
        $users->email = $req->email;
        $users->phone = $req->phone;
        $users->gender = $req->gender;
        $users->usertype = $req->usertype;
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

        Alert::success('Success', 'User registered successfully');
        return back();

    }

    public function managerForm() {
        $schools = school::where('status', '=', 1)->orderBy('school_name', 'ASC')->get();
        $managers = User::query()
                        ->join('schools', 'schools.id', '=', 'users.school_id')
                        ->select('users.*', 'schools.school_name', 'schools.school_reg_no')
                        ->where('users.usertype', '=', 2)
                        ->orderBy('users.first_name', 'ASC')
                        ->get();

        return view('Managers.index', ['managers' => $managers], ['schools' => $schools]);
    }

    public function errorPage()
    {
        return view('Error.403');
    }
}
