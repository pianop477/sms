<?php

namespace App\Http\Controllers;

use App\Models\Parents;
use App\Models\school;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
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
            'fname' => 'required|string|max:255',
            'lname' => 'required|string|max:255',
            'email' => 'required|string|unique:users,email',
            'phone' => 'required|string|min:10|max:255',
            'gender' => 'required|string|max:255',
            'school' => 'required|integer|exists:schools,id',
            'password' => 'required|min:8',
            'password_confirmation' => 'same:password',
            'image' => 'nullable|image|max:2048',
            'street' => 'required|string|max:255',
        ]);

        $parentExists = User::where('phone', $req->phone)->exists();
        if($parentExists) {
            Alert::error('Error', 'This accounts already exists');
            return back();
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

        Alert::success('Hongera', 'Taarifa zako zimehifadhiwa kikamilifu, unaweza kuingia sasa!');
        return redirect()->route('login');

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

    public function manageAdminAccounts()
    {
        $users = User::where('usertype', 1)->orderBy('first_name')->get();
        return view('Admin.index', compact('users'));
    }

    public function addAdminAccount(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fname' => 'required|string|max:255',
            'lname' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'gender' => 'required|string|max:10',
            'phone' => 'required|string|min:10|max:15',
            'password' => 'string|min:8',
        ]);

        if($validator->fails()) {
            $errors = $validator->errors();
            foreach($errors as $error) {
                Alert::error('Validation Error', $error);
            }
            return back();
        }

        $isExisting = User::where('phone', $request->phone)->exists();
        if($isExisting) {
            Alert::error('Duplicate Data', 'User information already exist');
            return back();
        }

        $user = User::create([
            'first_name' => $request->input('fname'),
            'last_name' => $request->input('lname'),
            'email' => $request->input('email'),
            'gender' => $request->input('gender'),
            'phone' => $request->input('phone'),
            'usertype' => $request->input('usertype', 1),
            'password' => Hash::make($request->input('password', 'shule@2024')),
        ]);
        Alert::success('Success!', 'User admin saved successfully');
        return back();

    }

    public function blockAdminAccount(Request $request, $id)
    {
        $user = User::find($id);
        $status = 0;

        if(! $user) {
            ALert::error('Error', 'No such user was found');
            return back();
        }

        $user->update([
            'status' => $status
        ]);

        Alert::success('Success', 'Admin Account has been blocked successufully');
        return back();

    }

    public function unblockAdminAccount(Request $request, $id)
    {
        $user = User::find($id);
        $status = 1;

        if(! $user) {
            ALert::error('Error', 'No such user was found');
            return back();
        }

        $user->update([
            'status' => $status
        ]);

        Alert::success('Success', 'Admin Account has been unblocked successufully');
        return back();

    }

    public function deleteAdminAccount(Request $request, $id)
    {
        $user = User::find($id);

        if(! $user) {
            ALert::error('Error', 'No such user was found');
            return back();
        }

        $user->delete();

        Alert::success('Success', 'Admin Account has been deleted successufully');
        return back();

    }
}
