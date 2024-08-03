<?php

namespace App\Http\Controllers;

use App\Models\Parents;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\DB;

class ParentsController extends Controller
{

    public function index() {
        $user = Auth::user();
        $parents = Parents::query()
                            ->join('users', 'users.id', '=', 'parents.user_id')
                            ->join('schools', 'schools.id', '=', 'parents.school_id')
                            ->select('parents.*', 'users.first_name', 'users.last_name', 'users.gender', 'users.phone', 'users.email')
                            ->where('parents.school_id', '=', $user->school_id)
                            ->where(function ($query) {
                                $query->where('parents.status', 1)
                                        ->orWhere('parents.status', 0);
                            })
                            ->orderBy('users.first_name', 'ASC')
                            ->get();
        return view('Parents.index', ['parents' => $parents]);
    }
    /**
     * Show the form for creating the resource.
     */
    public function create()
    {
        // abort(404);
        return view('Parents.create');
    }

    /**
     * Store the newly created resource in storage.
     */
    public function store(Request $request)
    {
        // abort(404);
        $request->validate([
            'fname' => 'required|string|max:25',
            'lname' => 'required|string|max:25',
            'email' => 'required|string|unique:users,email',
            'gender' => 'required|string|max:6',
            'phone' => 'required|string|max:10|min:10',
            'street' => 'required|string|max:15',
        ]);

        $users = new User();
        $users->first_name = $request->fname;
        $users->last_name = $request->lname;
        $users->email = $request->email;
        $users->phone = $request->phone;
        $users->gender = $request->gender;
        $users->usertype = $request->usertype;
        $users->password = Hash::make($request->password);
        $users->school_id = $request->school_id;
        $users->save();

        $parents = new Parents();
        $parents->user_id = $users->id;
        $parents->school_id = $users->school_id;
        $parents->address = $request->street;
        $new_parent = $parents->save();

        // return back()->with('success', 'Parent records saved successfully');
        if($new_parent) {
            Alert::success('Success', 'Parent records saved successfully');
            return back();
        }
    }

    /**
     * Display the resource.
     */
    public function show()
    {
        //
    }

    /**
     * Show the form for editing the resource.
     */
    public function edit($parent)
    {
        //
        $parents = Parents::query()->join('users', 'users.id', '=', 'parents.user_id')
                                    ->select('parents.*', 'users.first_name', 'users.last_name', 'users.gender', 'users.phone', 'users.image')
                                    ->where('parents.id', '=', $parent)
                                    ->first();
        return view('Parents.edit', ['parents' => $parents]);
    }

    /**
     * Update the resource in storage.
     */
    public function updateStatus(Request $request, $parent)
    {
        //
        $parents = Parents::findOrFail($parent);
        $user = User::findOrFail($parents->user_id);
        $user->status = $request->input('status', 0);
        if($user->save()) {
            $parents->status = $request->input('status', 0);

            if($parents->save()) {
                Alert::success('Success', 'Parent blocked successfully');
                return back();
            }
        }
    }

    public function restoreStatus(Request $request, $parent) {
        $parents = Parents::findOrFail($parent);
        $user = User::findOrFail($parents->user_id);
        $user->status = $request->input('status', 1);
        if($user->save()) {
            $parents->status = $request->input('status', 1);

            if($parents->save()) {
                Alert::success('Success', 'Parent unblocked successfully');
                return back();
            }
        }
    }

    /**
     * Remove the resource from storage.
     */
    public function destroy($parent)
    {
        // Find the teacher record or fail
        $parents = Parents::findOrFail($parent);

        // Find the associated user or fail
        $user = User::findOrFail($parents->user_id);

        // Check the image path
        $userImgPath = public_path('assets/img/profile/' . $user->image);

        // Begin a database transaction
        DB::beginTransaction();

        try {

            //update parents status ------------
            $parents->status = 2;
            $parents->save();

            //update users status references to parents
            $user->status = 2;
            $user->save();

            // Commit the transaction
            DB::commit();

            Alert::success('Success', 'Parent records deleted successfully');
        } catch (\Exception $e) {
            // Rollback the transaction if there's an error
            DB::rollBack();

            Alert::error('Error', 'Failed to delete parent records');
        }

        return back();
    }


    public function update (Request $request, $parents)
    {
        $request->validate([
            'fname' => 'required|max:25|string',
            'lname' => 'required|max:25|string',
            'gender' => 'required|string|max:6',
            'phone' => 'required|min:10|max:10',
            'street' => 'required|string|max:15',
            'image' => 'nullable|image|max:20148',
        ]);

        try {
            $parent = Parents::findOrFail($parents);
            // Log::info('Teacher found', ['teacher' => $teacher]);

            $user = User::findOrFail($parent->user_id);
            // Log::info('User found', ['user' => $user]);

            $user->first_name = $request->fname;
            $user->last_name = $request->lname;
            $user->phone = $request->phone;
            $user->gender = $request->gender;

            if($request->hasFile('image')) {
                // Log::info('Image upload detected');
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
                $user->image = $imageName;
            }

            if ($user->save()) {
                // Log::info('User updated successfully');
                $parent->address = $request->street;

                if ($parent->save()) {
                    // Log::info('Teacher updated successfully');
                    Alert::success('Success', 'Parent records updated successfully');
                    return back();
                } else {
                    // Log::error('Failed to update teacher information');
                    Alert::error('Error', 'Failed to updated Parent records');
                    return back();
                }
            } else {
                // Log::error('Failed to update user information');
                Alert::error('Error', 'Failed to update parent records');
                return back();
            }
        } catch (\Exception $e) {
            // Log::error('An error occurred: ' . $e->getMessage());
            Alert::error('Error', 'An error occured: ' . $e->getMessage());
            return back();
        }
    }
}
