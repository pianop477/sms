<?php

namespace App\Http\Controllers;

use App\Models\Examination_result;
use App\Models\Grade;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RealRashid\SweetAlert\Facades\Alert;
use Vinkla\Hashids\Facades\Hashids;

class ClassesController extends Controller
{

    public function showAllClasses()
    {
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
        ], [
            'name.required' => 'Class name is required',
            'code.required' => 'Class code is required',
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
        if (! $class) {
            Alert::error('Error', 'No class found');
            return back();
        }
        $request->validate([
            'cname' => 'required|string|max:255',
            'ccode' => 'required|string|max:255',
        ], [
            'cname.required' => 'Class name is required',
            'ccode.required' => 'Class code is required',
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
        $decoded = Hashids::decode($id);

        if (empty($decoded)) {
            abort(404);
        }

        $class = Grade::findOrFail($decoded[0]);

        /**
         * 1. Class status lazima iwe 0
         */
        if ($class->status == 1) {
            Alert()->toast('This class is active and cannot be deleted.', 'info');
            return back();
        }

        /**
         * 2. Usifute kama kuna active students
         */
        $hasActiveStudents = Student::where('class_id', $class->id)
                        ->where('status', 1)
                        ->exists();

        if ($hasActiveStudents) {
            Alert()->toast('Cannot delete this class because it has active students.', 'info');
            return back();
        }

        /**
         * 3. Usifute kama class imetumika kwenye examination_results
         */
        $usedInResults = Examination_result::where('class_id', $class->id)->exists();

        if ($usedInResults) {
            Alert()->toast(
                'Cannot delete this class because examination results already exist for it.',
                'info'
            );
            return back();
        }

        try {
            DB::transaction(function () use ($class) {

                // futa students wasio active wala graduate
                Student::where('class_id', $class->id)
                    ->where('status', '!=', 1)
                    ->where('graduated', 0)
                    ->delete();

                // futa class
                $class->delete();
            });

            Alert()->toast('Class has been deleted successfully.', 'success');
            return back();
        } catch (\Exception $e) {
            Alert()->toast('Something went wrong. Please try again.', 'error');
            return back();
        }
    }

    public function blockClass($id)
    {
        $decoded = Hashids::decode($id);
        $class = Grade::findOrFail($decoded[0]);
        if (! $class) {
            Alert::error('Error', 'No class found');
            return back();
        }
        $class->status = 0;
        $class->save();
        Alert()->toast('Class has been blocked successfully', 'success');
        return redirect()->back();
    }

    public function unblockClass($id)
    {
        $decoded = Hashids::decode($id);
        $class = Grade::findOrFail($decoded[0]);
        if (! $class) {
            Alert::error('Error', 'No class found');
            return back();
        }
        $class->status = 1;
        $class->save();
        Alert()->toast('Class has been unblocked successfully', 'success');
        return redirect()->back();
    }
}
