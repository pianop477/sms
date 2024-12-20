<?php

namespace App\Http\Controllers;

use App\Models\Grade;
use App\Models\Parents;
use App\Models\Student;
use App\Models\Transport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Psy\Command\WhereamiCommand;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Storage;

class StudentsController extends Controller
{

    public function index()
    {
        // Get all classes with their respective student counts
        $classes = Grade::where('status', 1)
                        ->withCount(['students' => function($query) {
                            $query->where('status', 1);
                        }])
                        ->where('school_id', '=', Auth::user()->school_id)
                        ->orderBy('id', 'ASC')
                        ->get();

        return view('Students.classes', ['classes' => $classes]);
    }

    /**
     * Show the form for creating the resource.
     */
    public function create($classId)
    {
        // abort(404);
        $class = Grade::findOrFail($classId);
        $buses = Transport::where('school_id', '=', Auth::user()->school_id, 'AND', 'status', '=', 1)->orderBy('driver_name', 'ASC')->get();
        $parents = Parents::query()
                            ->join('users', 'users.id', '=', 'parents.user_id')
                            ->join('schools', 'schools.id', '=', 'parents.school_id')
                            ->select('parents.id', 'users.first_name', 'users.last_name')
                            ->where('parents.school_id', '=', Auth::user()->school_id)
                            ->where('parents.status', '=', 1)
                            ->get();
        return view('Students.create', compact('class', 'buses', 'parents'));

    }

    /**
     * Store the newly created resource in storage.
     */
    public function createNew(Request $request, $class)
        {
            // Validate the incoming request data
            $request->validate([
                'fname' => 'required|string|max:255',
                'middle' => 'required|string|max:255',
                'lname' => 'required|string|max:255',
                'gender' => 'required|string|max:255',
                'parent' => 'required|exists:parents,id',
                'dob' => 'required|date|date_format:Y-m-d',
                'driver' => 'nullable|exists:transports,id',
                'group' => 'required|string|max:255',
                'image' => 'nullable|image|max:2048',
            ]);

            // Check for existing student records
            $existingRecords = Student::where('first_name', '=', $request->fname)
                                    ->where('middle_name', '=', $request->middle)
                                    ->where('last_name', '=', $request->lname)
                                    ->where('school_id', '=', Auth::user()->school_id)
                                    ->exists();

            if ($existingRecords) {
                Alert::error('Error','Student with the same records already exists in our records');
                return back();
            }

            // Retrieve the class
            $class = Grade::findOrFail($class);

            // Create a new student record
            $new_student = new Student();
            $new_student->first_name = $request->fname;
            $new_student->middle_name = $request->middle;
            $new_student->last_name = $request->lname;
            $new_student->gender = $request->gender;
            $new_student->dob = $request->dob;
            $new_student->group = $request->group;
            $new_student->parent_id = $request->parent;
            $new_student->class_id = $class->id;
            $new_student->transport_id = $request->driver;
            $new_student->admission_number = $this->getAdmissionNumber();
            $new_student->school_id = Auth::user()->school_id;

            // Handle file upload if present
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageFile = time() . '.' . $image->getClientOriginalExtension();
                $imagePath = public_path('assets/img/students');

                // Ensure the directory exists
                if (!file_exists($imagePath)) {
                    mkdir($imagePath, 0775, true);
                }

                // Move the file
                $image->move($imagePath, $imageFile);

                // Set the image file name on the student record
                $new_student->image = $imageFile;
            }

            // Save the new student record
           $student =  $new_student->save();
           if($student) {
            Alert::success('Success', 'Student records saved successfully');
            return redirect()->route('create.selected.class', $class->id);
           }
    }
        //generate uniqe student admission number randomly
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
    public function edit()
    {
        //
    }

    /**
     * Update the resource in storage.
     */
    public function updateRecords(Request $request, $students)
    {
        $request->validate([
            'fname' => 'required|string|max:255',
            'middle' => 'required|string|max:255',
            'lname' => 'required|string|max:255',
            'class' => 'required|integer|exists:grades,id',
            'group' => 'required|max:255',
            'gender' => 'required|max:255',
            'dob' => 'required|date|date_format:Y-m-d',
            'driver' => 'integer|nullable|exists:transports,id',
            'image' => 'nullable|max:2048|image',
        ]);
        $student = Student::findOrFail($students);
        // return $student->first_name . ' '. $student->school_id. ' '. $student->parent_id;
        $student->first_name = $request->fname;
        $student->middle_name = $request->middle;
        $student->last_name = $request->lname;
        $student->class_id = $request->class;
        $student->group = $request->group;
        $student->gender = $request->gender;
        $student->dob = $request->dob;
        $student->transport_id = $request->driver;
        if($request->hasFile('image')){
            $image = $request->file('image');
            $imageFile = time() . '.' . $image->getClientOriginalExtension();
            $imagePath = public_path('assets/img/students');

            // Ensure the directory exists
            if (!file_exists($imagePath)) {
                mkdir($imagePath, 0775, true);
            }

            // Move the file
            $image->move($imagePath, $imageFile);
            $student->image = $imageFile;
        }
        $student->save();
        Alert::success('Success', 'Student records updated successfully');
        return back();

    }


    /**
     * Remove the resource from storage.
     */
     public function destroy($student)
     {
         // Find the student record
         $student = Student::findOrFail($student);

         //update status ------------
         $student->status = 2;
         $student->save();
         // Show success message
         Alert::success('Success', 'Student records deleted successfully');
         return back();
     }

    public function showStudent($class)
    {
        $classId = Grade::findOrFail($class);
        $parents = Parents::query()
                            ->join('users', 'users.id', '=', 'parents.user_id')
                            ->join('schools', 'schools.id', '=', 'parents.school_id')
                            ->select('parents.id', 'users.first_name', 'users.last_name')
                            ->where('parents.school_id', '=', Auth::user()->school_id)
                            ->where('parents.status', '=', 1)
                            ->orderBy('users.first_name', 'ASC')
                            ->get();
        $buses = Transport::where('school_id', '=', Auth::user()->school_id)->where('status', '=', 1)->orderBy('driver_name', 'ASC')->get();
        $students = Student::query()
                            ->join('parents', 'parents.id', '=', 'students.parent_id')
                            ->join('users', 'users.id', '=', 'parents.user_id')
                            ->join('grades', 'grades.id', '=', 'students.class_id')
                            ->join('schools', 'schools.id', '=', 'students.school_id')
                            ->leftJoin('transports', 'transports.id', '=', 'students.transport_id')
                            ->select('students.id', 'students.first_name', 'students.middle_name', 'students.admission_number', 'students.last_name', 'students.gender', 'students.dob',
                                'transports.driver_name', 'transports.gender as driver_gender', 'transports.phone as driver_phone', 'transports.bus_no',
                                'transports.routine', 'schools.school_reg_no')
                            ->where('students.class_id', '=', $classId->id)
                            ->where('students.school_id', '=', Auth::user()->school_id)
                            ->where('students.status', 1)
                            ->orderBy('students.first_name', 'ASC')
                            ->get();
        $classes = Grade::where('id', '!=', $classId->id)->get();
        return view('Students.index', ['students' => $students, 'classId' => $classId, 'parents' => $parents, 'buses' => $buses, 'classes' => $classes]);
    }

    //promote students to the next class
    public function promoteClass($id, Request $request)
    {
        $class = Grade::find($id);
        if(! $class) {
            Alert::error('Error!', 'No such class was found');
            return back();
        }

        $this->validate($request, [
            'class_id' => 'required|exists:grades,id',
        ]);

        // $student = Student::where('class_id', $class->id)->get();
        Student::where('class_id', $class->id)->update(['class_id' => $request->class_id]);
        Alert::success('Success!', 'Students have been upgraded to the next class');
        return back();
        // return $student;

    }

    //export to pdf ======================
    public function exportPdf($classId)
    {
        // return $classId;
        $students = Student::query()->join('grades', 'grades.id', '=', 'students.class_id')
                                    ->join('schools', 'schools.id', '=', 'students.school_id')
                                    ->join('parents', 'parents.id', 'students.parent_id')
                                    ->leftJoin('users', 'users.id', '=', 'parents.user_id')
                                    ->select(
                                        'students.*',
                                        'grades.class_name', 'grades.class_code',
                                        'schools.school_name', 'schools.school_reg_no', 'schools.logo', 'schools.postal_address',
                                        'schools.postal_name', 'schools.country',
                                        'parents.address', 'users.phone'
                                    )
                                    ->where('students.class_id', $classId)
                                    ->orderBy('students.first_name')
                                    ->get();
        $pdf = \PDF::loadView('Students.student_export', compact('students'));
        // return $pdf->download($students->first()->class_name.' students.pdf');

        if ($students->isNotEmpty()) {
            $className = $students->first()->class_name;
            $fileName = "{$className} Attendance Report.pdf";
        } else {
            $fileName = "Students List.pdf";
        }
        return $pdf->download($fileName);
    }

    public function parentByStudent()
    {
        $buses = Transport::where('school_id', '=', Auth::user()->school_id)->where('status', '=', 1)->orderBy('driver_name', 'ASC')->get();
        $classes = Grade::where('school_id', '=', Auth::user()->school_id)->where('status', '=', 1)->orderBy('class_name', 'ASC')->get();
        return view('Students.register', ['buses' => $buses, 'classes' => $classes ]);
    }

    public function registerStudent (Request $request)
    {
        $user = Auth::user();
        $parent = Parents::where('user_id', $user->id)->first();
        $request->validate([
            'fname' => 'required|string|max:255',
            'middle' => 'required|string|max:255',
            'lname' => 'required|string|max:255',
            'gender' => 'required|string|max:255',
            'grade' => 'required|integer|exists:grades,id',
            'dob' => 'required|date|date_format:Y-m-d',
            'driver' => 'nullable|integer|exists:transports,id',
            'group' => 'required|string|max:255',
            'image' => 'nullable|image|max:2048',
        ]);

         // Check for existing student records
         $existingRecords = Student::where('first_name', '=', $request->fname)
                                    ->where('middle_name', '=', $request->middle)
                                    ->where('last_name', '=', $request->lname)
                                    ->where('school_id', '=', Auth::user()->school_id)
                                    ->exists();

        if ($existingRecords) {
        Alert::error('Error', 'Student with the same records already exists in our records');
        return back();
        }

        $students = new Student();
        $students->admission_number = $this->getAdmissionNumber();
        $students->first_name = $request->fname;
        $students->middle_name = $request->middle;
        $students->last_name = $request->lname;
        $students->gender = $request->gender;
        $students->dob = $request->dob;
        $students->class_id = $request->grade;
        $students->group = $request->group;
        $students->transport_id = $request->driver;
        $parents = Parents::where('user_id', '=', Auth::user()->id)->first();
        $students->parent_id = $parents->id;
        $students->school_id = $parent->school_id;

        // Handle file upload if present
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageFile = time() . '.' . $image->getClientOriginalExtension();
            $imagePath = public_path('assets/img/students');

            // Ensure the directory exists
            if (!file_exists($imagePath)) {
                mkdir($imagePath, 0775, true);
            }

            // Move the file
            $image->move($imagePath, $imageFile);

            // Set the image file name on the student record
            $students->image = $imageFile;
        }

        // Save the new student record
        $students->save();

        // Return success message
        Alert::success('Success', 'Student records saved successfully');
        return redirect()->route('home');
    }

    public function showRecords($student)
    {
        $data = Student::query()
                        ->join('parents', 'parents.id', '=', 'students.parent_id')
                        ->join('grades', 'grades.id', '=', 'students.class_id')
                        ->join('users', 'users.id', '=', 'parents.user_id')
                        ->join('schools', 'schools.id', '=', 'students.school_id')
                        ->leftJoin('transports', 'transports.id', '=', 'students.transport_id')
                        ->select(
                            'students.*',
                            'parents.user_id as parent_user_id',
                            'parents.address as parent_address',
                            'users.first_name as user_first_name',
                            'users.last_name as user_last_name',
                            'users.id as user_id',
                            'parents.id as parent_id',
                            'users.phone',
                            'users.gender as user_gender',
                            'grades.class_name as grade_class_name',
                            'grades.class_code as grade_class_code',
                            'transports.driver_name as driver', 'transports.gender as driver_gender', 'transports.phone as driver_phone', 'transports.bus_no as bus_number',
                            'transports.routine as bus_routine',
                            'grades.id as grade_class_id',
                            'schools.school_reg_no',
                        )
                        ->where('students.id', '=', $student)
                        ->first();

        // Check if the data is found
        if (!$data) {
            // Handle the case where no data is found, e.g., return a 404 response
            return abort(404, 'Student not found.');
        }

        // The student gender can be accessed directly from $data
        $studentGender = $data->gender;

        return view('Students.show', [
            'data' => $data,
            'studentGender' => $studentGender
        ]);
    }

    public function modify($student)
    {
        $students = Student::query()->join('parents', 'parents.id', '=', 'students.parent_id')
                                    ->join('users', 'users.id', '=', 'parents.user_id')
                                    ->join('grades', 'grades.id', '=', 'students.class_id')
                                    ->leftJoin('transports', 'transports.id', '=', 'students.transport_id')
                                    ->select('students.*', 'grades.class_name', 'grades.class_code', 'transports.driver_name')
                                    ->findOrFail($student);

        $buses = Transport::where('status', '=', 1)->orderBy('driver_name', 'ASC')->get();
        $classes = Grade::where('status', '=', 1)->get();
        return view('Students.edit', ['buses' => $buses, 'students' => $students, 'classes' => $classes ]);
    }

    public function studentTrashList()
    {
        $user = Auth::user();

        $students = Student::query()
                            ->join('grades', 'grades.id', '=', 'students.class_id')
                            ->select(
                                'students.*', 'grades.class_name'
                            )
                            ->where('students.status', 2)
                            ->where('students.school_id', $user->school_id)
                            ->get();
        return view('Students.trash', compact('students'));
    }

    public function restoreTrashList ($id, Request $request)
    {
        $student = Student::find($id);

        if(! $student ) {
            Alert::error('Error!', 'No such student was found');
            return back();
        }

        $status = 1;
        $student->update(['status' => $request->input('status', $status)]);
        Alert::success('Success!', 'Student has been restored successfully');
        return back();
    }

    public function deletePerStudent($id)
    {
        $student = Student::find($id);

        if(! $student) {
            Alert::error('Error!', 'No such student was found');
            return back();
        }

        $student->delete();
        Alert::success('Done!', 'Student has been deleted permanently');
        return back();
    }

}
