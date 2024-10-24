<?php

namespace App\Http\Controllers;

use App\Models\Grade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class ClassesController extends Controller
{

    public function index() {
        $classes = Grade::where('school_id', '=', Auth::user()->school_id)->get();
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
    public function store(Request $request)
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
                Alert::error('Error', 'A record with the same data already exists.');
                return back();

            }

            $New_class = new Grade();
            $New_class->class_name = $request->name;
            $New_class->class_code = $request->code;
            $New_class->school_id = Auth::user()->school_id;
            $New_class->save();

        Alert::success('Success', 'Class saved successfully');
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
    public function edit()
    {
        //
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
