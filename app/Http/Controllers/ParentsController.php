<?php

namespace App\Http\Controllers;

use App\Events\PasswordResetEvent;
use App\Models\Grade;
use App\Models\Parents;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Transport;
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
        $classes = Grade::where('school_id', '=', $user->school_id, 'AND', 'status', '=', 1)->orderBy('class_code')->get();
        $buses = Transport::where('school_id', '=', $user->school_id, 'AND', 'status', '=', 1)->orderBy('driver_name', 'ASC')->get();
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
        return view('Parents.index', compact('parents','classes', 'buses'));
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
                'email' => 'string|unique:users,email|nullable',
                'gender' => 'required|string|max:255',
                'phone' => 'required|regex:/^[0-9]{10}$/|unique:users,phone',
                'street' => 'required|string|max:255',
                'school_id' => 'exists:schools,id',
                'student_first_name' => 'required|string|max:255',
                'student_middle_name' => 'required|string|max:255',
                'student_last_name' => 'required|string|max:255',
                'student_gender' => 'required|string|in:male,female',
                'dob' => 'required|date|date_format:Y-m-d',
                'class' => 'required|integer|exists:grades,id',
                'group' => 'required|string|in:a,b,c,d,e',
                'bus_no' => 'nullable|integer|exists:transports,id',
                'passport' => 'nullable|image|mimes:jpg,png,jpeg|max:512',
            ]);

            $userExists = User::where('phone', $request->phone)
                                ->where('school_id', $user->school_id)
                                ->exists();
            $studentExists = Student::where('first_name', $request->student_first_name)
                                    ->where('middle_name', $request->student_middle_name)
                                    ->where('last_name', $request->student_last_name)
                                    ->where('school_id', $request->school_id)
                                    ->exists();

            if($userExists || $studentExists) {
                Alert::info('Info', 'Parents or Student information already exists in our records');
                return back();
            }

            $users = User::create([
                'first_name' => $request->fname,
                'last_name' => $request->lname,
                'email' => $request->email,
                'phone' => $request->phone,
                'gender' => $request->gender,
                'usertype' => $request->input('usertype', 4),
                'password' => Hash::make($request->input('password', 'shule@2024')),
                'school_id' => $user->school_id,
            ]);

            $parents = Parents::create([
                'user_id' => $users->id,
                'school_id' => $user->school_id,
                'address' => $request->street,
            ]);

            $studentImage = '';

            // Handle file upload if present
            if ($request->hasFile('passport')) {
                $image = $request->file('passport');
                $imageFile = time() . '.' . $image->getClientOriginalExtension();
                $imagePath = public_path('assets/img/students');

                // Ensure the directory exists
                if (!file_exists($imagePath)) {
                    mkdir($imagePath, 0775, true);
                }

                // Move the file
                $image->move($imagePath, $imageFile);

                // Set the image file name on the student record
                $studentImage = $imageFile;
            }

            $students = Student::create([
                'admission_number' => $this->getAdmissionNumber(),
                'first_name' => $request->student_first_name,
                'middle_name' => $request->student_middle_name,
                'last_name' => $request->student_last_name,
                'parent_id' => $parents->id,
                'gender' => $request->student_gender,
                'dob' => $request->dob,
                'class_id' => $request->class,
                'group' => $request->group,
                'image' => $studentImage,
                'school_id' => $parents->school_id
            ]);

            Alert::success('Success', 'Parent and student information saved successfully');
            return redirect()->route('Parents.index');

        } catch (\Exception $e) {
            Alert::error('Error', $e->getMessage());
            return back();
        }
    }

    protected function getAdmissionNumber ()
    {
        do {
            // Generate a random 4-digit number between 1000 and 9999
            $admissionNumber = mt_rand(1000, 9999);

            // Check if this admission number already exists
        } while (Student::where('admission_number', $admissionNumber)->exists());

        return $admissionNumber; // Return the unique admission number
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
                event(new PasswordResetEvent($user->id));
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
                Alert::info('Info', 'Cannot delete this parent because has active children.');
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
        $parent = Parents::findOrFail($parents);
        $user = User::findOrFail($parent->user_id);

        try {

            // run validation
            $this->validate($request, [
                'fname' => 'required|max:255|string',
                'lname' => 'required|max:255|string',
                'gender' => 'required|string|max:255',
                'phone' => 'required|regex:/^[0-9]{10}$/|unique:users,phone,'.$user->id,
                'street' => 'required|string|max:255',
                'image' => 'nullable|image|max:2048',
            ]);

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

                //check for existing image file
                if(!empty($user->image)) {
                    $existingImagePath = public_path('assets/img/profile/' . $user->image);
                    if (file_exists($existingImagePath)) {
                        unlink($existingImagePath);
                    }
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
