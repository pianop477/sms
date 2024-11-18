<?php

namespace App\Http\Controllers;

use App\Models\Parents;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ParentsController extends Controller
{

    public function showAllParents() {
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
        return view('Parents.index', compact('parents'));
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
    public function registerParents(Request $request)
    {
        $user = Auth::user();

        try {
            $dataValidation = $request->validate([
                'fname' => 'required|string|max:255',
                'lname' => 'required|string|max:255',
                'email' => 'required|string|unique:users,email',
                'gender' => 'required|string|max:255',
                'phone' => 'required|string|min:10|max:15',
                'street' => 'required|string|max:255',
            ]);

            $userExists = User::where('phone', $request->phone)
                                ->where('school_id', $user->school_id)
                                ->exists();
            if($userExists) {
                Alert::info('Info', 'Parents information already exists in our records');
                return back();
            }

            $users = User::create([
                'first_name' => $request->fname,
                'last_name' => $request->lname,
                'email' => $request->email,
                'phone' => $request->phone,
                'gender' => $request->gender,
                'usertype' => $request->usertype,
                'password' => Hash::make($request->password),
                'school_id' => $user->school_id
            ]);

            $parents = Parents::create([
                'user_id' => $users->id,
                'school_id' => $user->school_id,
                'address' => $request->street,
            ]);

            Alert::success('Success', 'Parent has registered successfully');
            return redirect()->route('Parents.index');

        } catch (\Exception $e) {
            Alert::error('Error', $e->getMessage());
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
    public function editParent($parent)
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
    public function deleteParent($parentId)
    {
        try {
            // Find the parent record
            $parent = Parents::find($parentId);

            if (!$parent) {
                Alert::error('Error', 'No such parent was found');
                return back();
            }

            // Find the associated user
            $user = User::find($parent->user_id);
            if (!$user) {
                Alert::error('Error', 'No associated user was found');
                return back();
            }

            // Check if the parent has active students
            $activeStudents = Student::where('parent_id', $parent->id)->where('status', 1)->count();

            if ($activeStudents > 0) {
                Alert::info('Info', 'Cannot delete this parent because they have active children.');
                return back();
            }

            // Delete any related inactive students (if needed)
            Student::where('parent_id', $parent->id)->where('status', '!=', 1)->delete();

            // Check and delete the user's profile image if it exists
            if (!empty($user->image)) {
                $userImagePath = public_path('assets/img/profile/' . $user->image);
                if (file_exists($userImagePath)) {
                    unlink($userImagePath);
                }
            }

            // Delete the user and parent records
            $user->delete();
            $parent->delete();

            Alert::success('Success', 'Parent data has been deleted successfully');
            return back();
        } catch (\Exception $e) {
            Alert::error('Error', 'An error occurred: ' . $e->getMessage());
            return back();
        }
    }


    public function updateParent (Request $request, $parents)
    {
        $request->validate([
            'fname' => 'required|max:25|string',
            'lname' => 'required|max:25|string',
            'gender' => 'required|string|max:255',
            'phone' => 'required|min:10|max:255',
            'street' => 'required|string|max:255',
            'image' => 'nullable|image|max:2048',
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
