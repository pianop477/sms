<?php

namespace App\Http\Controllers;

use App\Models\Grade;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;
use Vinkla\Hashids\Facades\Hashids;

class ClassesController extends Controller
{

    public function showAllClasses() {
        $classes = Grade::where('school_id', '=', Auth::user()->school_id)->orderBy('class_code')->get();
        return view('Classes.index', ['classes' => $classes]);
    }
    /**
     * Show the form for creating the resource.
     */
    public function create()
    {
        // abort(404);
        return view('Classes.create');
    }

    /**
     * Store the newly created resource in storage.
     */
    public function registerClass(Request $request)
    {
        // abort(404);
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255',
        ]);
        // Check if a record with the same combination already exists
            $existingRecord = Grade::where('class_name', $request->name)
                            ->where('class_code', $request->code)
                            ->where('school_id', Auth::user()->school_id)
                            ->exists();

            // If a record with the same combination exists, return validation error
            if ($existingRecord) {
                // Alert::error('Error', 'A record with the same data already exists.');
                Alert()->toast('A record with the same data already exists.', 'error');
                return back();

            }

            $New_class = new Grade();
            $New_class->class_name = $request->name;
            $New_class->class_code = $request->code;
            $New_class->school_id = Auth::user()->school_id;
            $New_class->save();

        // Alert::success('Success', 'Class saved successfully');
        Alert()->toast('Class saved successfully', 'success');
        return redirect()->route('Classes.index');
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
    public function editClass($id)
    {
        //
        $decoded = Hashids::decode($id);
        $class = Grade::findOrFail($decoded[0]);
        return view('Classes.edit', compact('class'));
    }

    /**
     * Update the resource in storage.
     */
    public function updateClass(Request $request, $id)
    {
        //
        $decoded = Hashids::decode($id);
        $class = Grade::findOrFail($decoded[0]);
        if(! $class) {
            Alert::error('Error', 'No class found');
            return back();
        }
        $request->validate([
            'cname' => 'required|string|max:255',
            'ccode' => 'required|string|max:255',
        ]);

        $class->update([
            'class_name' => $request->cname,
            'class_code' => $request->ccode,
        ]);
        // Alert::success('success', 'Class details updated successfully');
        Alert()->toast('Class details updated successfully', 'success');
        return redirect()->route('Classes.index');
    }

    /**
     * Remove the resource from storage.
     */
    public function deleteClass($id)
    {
        // abort(404);
        $decoded = Hashids::decode($id);
        $class = Grade::find($decoded[0]);
        // return $class;
        $students = Student::where('class_id', $class->id)->where('status', 1)->exists();
        if($students) {
            // Alert::info('Info', 'Cannot delete this class because has active students');
            Alert()->toast('Cannot delete this class because has active students', 'info');
            return back();
        }

        try {
            //delete students not active to the class id
            $notActiveStudents = Student::where('class_id', $class->id)->where('graduated', 0)->where('status', '!=', 1)->get();

            if($notActiveStudents->isEmpty()) {
                //delete class
                $class->delete();
                // Alert::success('Success', 'Class has been deleted successfully');
                Alert()->toast('Class has been deleted successfully', 'success');
                return back();
            } else {
                $notActiveStudents->delete();
                //delete class
                $class->delete();
                // Alert::success('Success', 'Class has been deleted successfully');
                Alert()->toast('Class has been deleted successfully', 'success');
                return back();
            }

        } catch(\Exception $e) {
            Alert::error('Error', $e->getMessage());
            return back();
        }

    }
}
