<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Transport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class TransportController extends Controller
{

    public function index() {
        $transport = Transport::get();
        return view('Transport.index', ['transport' => $transport]);
    }
    /**
     * Show the form for creating the resource.
     */
    public function create()
    {
        // abort(404);

        return view('Transport.create');

    }

    /**
     * Store the newly created resource in storage.
     */
    public function store(Request $request)
    {
        // abort(404);
        $request->validate([
            'fullname' => 'required|string|max:255',
            'gender' => 'string|required',
            'phone' => 'required|string|max:10|min:10',
            'bus' => 'required|string',
            'routine' => 'required|string',
        ]);

        $existingRecord = Transport::where('driver_name', $request->fullname)
                            ->where('gender', $request->gender)
                            ->where('phone', $request->phone)
                            ->where('bus_no', $request->bus)
                            ->where('routine', $request->routine)
                            ->where('school_id', Auth::user()->school_id)
                            ->exists();

            // If a record with the same combination exists, return validation error
            if ($existingRecord) {
                Alert::error('Error', 'A record with the same data already exists.');
                return back();

            }

        $new_routine = new Transport();
        $new_routine->driver_name = $request->fullname;
        $new_routine->gender = $request->gender;
        $new_routine->phone = $request->phone;
        $new_routine->bus_no = $request->bus;
        $new_routine->routine = $request->routine;
        $new_routine->school_id = Auth::user()->school_id;
        $new_routine->save();

        Alert::success('Success', 'School bus routine saved successfully');
        return back();
    }

    /**
     * Display the resource.
     */
    public function show()
    {
        //
    }

    public function update(Request $request, $trans)
    {
        //
        $transport = Transport::findOrFail($trans);
        $transport->status = $request->input('status', 0);
        $transport->save();
        if($transport) {
            Alert::success('Success', 'Bus routine Blocked successfully');
            return back();
        }
    }

    public function restore(Request $request, $trans)
    {
        $transport = Transport::findOrFail($trans);
        $transport->status = $request->input('status', 1);
        $transport->save();
        if($transport) {
            Alert::success('Success', 'Bus routine Unblocked successfully');
            return back();
        }

    }

    /**
     * Remove the resource from storage.
     */
    public function destroy($trans)
    {
        // abort(404);
        $transport = Transport::findOrFail($trans);
        $transport->delete();
       if($transport) {
            Alert::success('Success', 'School bus routine deleted successfully');
            return back();
       }
    }

    public function Edit($trans)
    {
        $transport = Transport::findOrFail($trans);
        return view('Transport.Edit', ['transport' => $transport]);
    }

    public function UpdateRecords(Request $request, $transport)
    {
        $request->validate([
            'fullname' => 'required|string',
            'gender' => 'required|string',
            'phone' => 'required|string',
            'bus_no' => 'required|string',
            'routine' => 'required|string'
        ]);
        $trans = Transport::findOrFail($transport);
        $trans->driver_name = $request->fullname;
        $trans->gender = $request->gender;
        $trans->phone = $request->phone;
        $trans->bus_no = $request->bus_no;
        $trans->routine = $request->routine;
        $trans->save();
        Alert::success('Success!', 'School bus information updated successfully');
        return redirect()->route('Transportation.index');
    }

    public function showStudents(Transport $trans)
    {
        $students = Student::query()->join('grades', 'grades.id', '=', 'students.class_id')
                                    ->join('parents', 'parents.id', '=', 'students.parent_id')
                                    ->leftJoin('users', 'users.id', '=', 'parents.user_id')
                                    ->select(
                                        'students.*',
                                        'grades.id as class_id', 'grades.class_name', 'grades.class_code',
                                        'parents.address', 'users.phone'

                                    )
                                    ->where('students.transport_id', $trans->id)
                                    ->orderBy('students.first_name')
                                    ->get();
        return view('Transport.students', compact('students', 'trans'));
    }
}
