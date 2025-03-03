<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Transport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;
use Spatie\Ignition\Contracts\HasSolutionsForThrowable;
use Vinkla\Hashids\Facades\Hashids;

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
        $request->validate([
            'fullname' => 'required|string|max:255',
            'gender' => 'string|required|max:255',
            'phone' => 'required|regex:/^[0-9]{10}$/|unique:transports,phone',
            'bus' => 'required|string|max:255',
            'routine' => 'required|string|max:255',
        ]);

        try {

            $existingRecord = Transport::where('phone', $request->phone)
                                        ->where('school_id', Auth::user()->school_id)
                                        ->exists();

            // If a record with the same combination exists, return validation error
            if ($existingRecord) {
                Alert()->toast('A record with the same data already exists.', 'error');
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

            Alert()->toast('School bus routine saved successfully', 'success');
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
        $id = Hashids::decode($trans);
        $user = Auth::user();
        $transport = Transport::findOrFail($id[0]);

        if($transport->school_id != $user->school_id) {
            Alert()->toast('You are not authorized to perform this action', 'error');
            return back();
        }
        //check for existing students in this bus routine
        $hasStudents = Student::where('transport_id', $transport->id)->where('status', 1)->where('graduated', 0)->exists();

        if($hasStudents) {
            Alert()->toast('This school bus already have active students, cannot be blocked', 'info');
            return back();
        }
        $transport->status = $request->input('status', 0);
        $transport->save();
        if($transport) {
            Alert()->toast('Bus routine Blocked successfully', 'success');
            return back();
        }
    }

    public function restore(Request $request, $trans)
    {
        $id = Hashids::decode($trans);
        $user = Auth::user();
        $transport = Transport::findOrFail($id[0]);

        if($transport->school_id != $user->school_id) {
            Alert()->toast('You are not authorized to perform this action', 'error');
            return back();
        }
        $transport->status = $request->input('status', 1);
        $transport->save();
        if($transport) {
            Alert()->toast('Bus routine Unblocked successfully', 'success');
            return back();
        }

    }

    /**
     * Remove the resource from storage.
     */
    public function destroy($trans)
    {
        // abort(404);
        $id = Hashids::decode($trans);
        $user = Auth::user();
        $transport = Transport::findOrFail($id[0]);

        if($transport->school_id != $user->school_id) {
            Alert()->toast('You are not authorized to perform this action', 'error');
            return back();
        }
        $hasStudentTake = Student::where('transport_id', $transport->id)->where('status', 1)->where('graduated', 0)->count();

        if($hasStudentTake > 0) {
            Alert()->toast('Cannot delete this route because they have active students', 'info');
            return back();
        }

        $transport->delete();
       if($transport) {
            Alert()->toast('School bus routine deleted successfully', 'success');
            return back();
       }
    }

    public function Edit($trans)
    {
        $id = Hashids::decode($trans);
        $user = Auth::user();
        $transport = Transport::findOrFail($id[0]);

        if($transport->school_id != $user->school_id) {
            Alert()->toast('You are not authorized to perform this action', 'error');
            return back();
        }
        return view('Transport.Edit', ['transport' => $transport]);
    }

    public function UpdateRecords(Request $request, $transport)
    {
        $id = Hashids::decode($transport);
        $trans = Transport::findOrFail($id[0]);
        $request->validate([
            'fullname' => 'required|string|max:255',
            'gender' => 'required|string|max:255',
            'phone' => 'required|regex:/^[0-9]{10}$/|unique:transports,phone,'.$trans->phone,
            'bus_no' => 'required|string|max|255',
            'routine' => 'required|string|max:255'
        ]);

        try {
            $user = Auth::user();
            if($trans->school_id != $user->school_id) {
                Alert()->toast('You are not authorized to perform this action', 'error');
                return back();
            }

            $trans->driver_name = $request->fullname;
            $trans->gender = $request->gender;
            $trans->phone = $request->phone;
            $trans->bus_no = $request->bus_no;
            $trans->routine = $request->routine;
            $trans->save();
            Alert()->toast('School bus information updated successfully', 'success');
            return redirect()->route('Transportation.index');
        } catch (\Exception $e) {
            Alert()->toast($e->getMessage(), 'error');
            return back();
        }
    }

    public function showStudents($trans)
    {
        $id = Hashids::decode($trans);
        $transport = Transport::find($id[0]);
        $user = Auth::user();
        $students = Student::query()->join('grades', 'grades.id', '=', 'students.class_id')
                                    ->join('parents', 'parents.id', '=', 'students.parent_id')
                                    ->leftJoin('users', 'users.id', '=', 'parents.user_id')
                                    ->join('transports', 'transports.id', '=', 'students.transport_id')
                                    ->select(
                                        'students.*',
                                        'grades.id as class_id', 'grades.class_name', 'grades.class_code',
                                        'parents.address', 'users.phone', 'transports.driver_name',
                                        'transports.phone', 'transports.bus_no'

                                    )
                                    ->where('students.transport_id', $transport->id)
                                    ->where('students.status', 1)
                                    ->where('students.school_id', $user->school_id)
                                    ->orderBy('students.first_name')
                                    ->get();
        return view('Transport.students', compact('students', 'transport'));
    }

    public function export ($trans)
    {
        // return response()->json($trans);
        $id = Hashids::decode($trans);
        $transport = Transport::find($id[0]);
        $user = Auth::user();
        $students = Student::query()
                            ->join('grades', 'grades.id', '=', 'students.class_id')
                            ->join('parents', 'parents.id', '=', 'students.parent_id')
                            ->leftJoin('users', 'users.id', '=', 'parents.user_id')
                            ->join('transports', 'transports.id', '=', 'students.transport_id')
                            ->select(
                                'students.*',
                                'parents.address', 'users.phone',
                                'grades.class_name', 'grades.class_code', 'transports.driver_name',
                                'transports.phone', 'transports.bus_no', 'transports.routine'
                            )
                            ->where('students.transport_id', $transport->id)
                            ->where('students.status', 1)
                            ->where('students.school_id', $user->school_id)
                            ->orderBy('first_name')
                            ->get();
        $pdf = \PDF::loadView('Transport.export', compact('students', 'transport'));
        return $pdf->stream($trans->driver_name. ' students.pdf');
    }
}
