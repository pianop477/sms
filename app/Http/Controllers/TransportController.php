<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Transport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;
use Spatie\Ignition\Contracts\HasSolutionsForThrowable;

class TransportController extends Controller
{

    public function getSchoolBuses() {
        $user = Auth::user();
        $transport = Transport::where('school_id', $user->school_id)->get();
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
    public function registerDrivers(Request $request)
    {
        // abort(404);
        $user = Auth::user();

        try {

            $request->validate([
                'fullname' => 'required|string|max:255',
                'gender' => 'string|required|max:255',
                'phone' => 'required|regex:/^[0-9]{10}$/|unique:transports,phone',
                'bus' => 'required|string|max:255',
                'routine' => 'required|string|max:255',
            ]);

            $existingRecord = Transport::where('phone', $request->phone)
                                        ->where('school_id', Auth::user()->school_id)
                                        ->exists();

            // If a record with the same combination exists, return validation error
            if ($existingRecord) {
                Alert::error('Error', 'A record with the same data already exists.');
                return back();

            }

            $schoolBus = Transport::create([
                'driver_name' => $request->fullname,
                'gender' => $request->gender,
                'phone' => $request->phone,
                'bus_no' => $request->bus,
                'routine' => $request->routine,
                'school_id' => $user->school_id
            ]);

            Alert::success('Success', 'School bus routine saved successfully');
            return back();

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

    public function update(Request $request, $trans)
    {
        //
        $transport = Transport::findOrFail($trans);

        //check for existing students in this bus routine

        $hasStudents = Student::where('transport_id', $transport->id)->where('status', 1)->where('graduated', 0)->exists();

        if($hasStudents) {
            Alert::info('Info', 'This school bus already have active students, cannot be blocked');
            return back();
        }
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

        $hasStudentTake = Student::where('transport_id', $transport->id)->where('status', 1)->where('graduated', 0)->count();

        if($hasStudentTake > 0) {
            Alert::info('Info', 'Cannot delete this route because they have active students');
            return back();
        }

        $transport->delete();
       if($transport) {
            Alert::success('Success', 'School bus routine deleted successfully');
            return back();
       }
    }

    public function Edit(Transport $trans)
    {
        $transport = Transport::findOrFail($trans);
        return view('Transport.Edit', ['transport' => $transport]);
    }

    public function UpdateRecords(Request $request, $transport)
    {
        $trans = Transport::findOrFail($transport);
        try {
            $request->validate([
                'fullname' => 'required|string|max:255',
                'gender' => 'required|string|max:255',
                'phone' => 'required|regex:/^[0-9]{10}$/|unique:transports,phone,'.$trans->phone,
                'bus_no' => 'required|string|max|255',
                'routine' => 'required|string|max:255'
            ]);
            $trans->driver_name = $request->fullname;
            $trans->gender = $request->gender;
            $trans->phone = $request->phone;
            $trans->bus_no = $request->bus_no;
            $trans->routine = $request->routine;
            $trans->save();
            Alert::success('Success!', 'School bus information updated successfully');
            return redirect()->route('Transportation.index');
        } catch (\Exception $e) {
            Alert::error('Errors', $e->getMessage());
            return back();
        }
    }

    public function showStudents(Transport $trans)
    {
        $user = Auth::user();
        $students = Student::query()->join('grades', 'grades.id', '=', 'students.class_id')
                                    ->join('parents', 'parents.id', '=', 'students.parent_id')
                                    ->leftJoin('users', 'users.id', '=', 'parents.user_id')
                                    ->select(
                                        'students.*',
                                        'grades.id as class_id', 'grades.class_name', 'grades.class_code',
                                        'parents.address', 'users.phone'

                                    )
                                    ->where('students.transport_id', $trans->id)
                                    ->where('students.status', 1)
                                    ->where('students.school_id', $user->school_id)
                                    ->orderBy('students.first_name')
                                    ->get();
        return view('Transport.students', compact('students', 'trans'));
    }

    public function export (Transport $trans)
    {
        // return response()->json($trans);
        $user = Auth::user();
        $students = Student::query()
                            ->join('grades', 'grades.id', '=', 'students.class_id')
                            ->join('parents', 'parents.id', '=', 'students.parent_id')
                            ->leftJoin('users', 'users.id', '=', 'parents.user_id')
                            ->select(
                                'students.*',
                                'parents.address', 'users.phone',
                                'grades.class_name', 'grades.class_code'
                            )
                            ->where('students.transport_id', $trans->id)
                            ->where('students.status', 1)
                            ->where('students.school_id', $user->school_id)
                            ->orderBy('first_name')
                            ->get();
        $pdf = \PDF::loadView('Transport.export', compact('students', 'trans'));
        return $pdf->stream($trans->driver_name. ' students.pdf');
    }
}
