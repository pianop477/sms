<?php

namespace App\Http\Controllers;

use App\Models\Grade;
use App\Models\Parents;
use App\Models\school;
use App\Models\Student;
use App\Models\Transport;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Psy\Command\WhereamiCommand;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Storage;

class StudentsController extends Controller
{

    // show classes list for students to display **************************************************
    public function index()
    {
        // Get all classes with their respective student counts
        $user = Auth::user();
        $classes = Grade::where('status', 1)
                        ->withCount(['students' => function($query) use ($user) {
                            $query->where('status', 1)->where('school_id', $user->school_id);
                        }])
                        ->where('school_id', '=', $user->school_id)
                        ->orderBy('class_code')
                        ->get();

        return view('Students.classes', ['classes' => $classes]);
    }

    /**
     * Show the form for creating the resource.
     */

    //  create or get form new student record ******************************************************************
    public function create($classId)
    {
        // abort(404);
        $user = Auth::user();
        $class = Grade::findOrFail($classId);
        $buses = Transport::where('school_id', '=', $user->school_id, 'AND', 'status', '=', 1)->orderBy('driver_name', 'ASC')->get();
        $parents = Parents::query()
                            ->join('users', 'users.id', '=', 'parents.user_id')
                            ->join('schools', 'schools.id', '=', 'parents.school_id')
                            ->select('parents.id', 'users.first_name', 'users.last_name')
                            ->where('parents.school_id', '=', $user->school_id)
                            ->where('parents.status', '=', 1)
                            ->get();
        return view('Students.create', compact('class', 'buses', 'parents'));

    }

    /**
     * Store the newly created resource in storage.
     */

    //  store and save new student records *****************************************************************
    public function createNew(Request $request, $class)
        {
            // Validate the incoming request data
            $user = Auth::user();
            $request->merge(['group' => strtoupper($request->input('group'))]);

            $request->validate([
                'fname' => 'required|string|max:255',
                'middle' => 'required|string|max:255',
                'lname' => 'required|string|max:255',
                'gender' => 'required|string|max:255',
                'parent' => 'required|exists:parents,id',
                'dob' => 'required|date|date_format:Y-m-d',
                'driver' => 'nullable|exists:transports,id',
                'group' => 'required|string|in:A,B,C,D',
                'image' => 'nullable|image|max:512|mimes:jpg,jpeg,png',
                'school_id' => 'exists:schools,id'
            ]);

            // Check for existing student records
            $existingRecords = Student::where('first_name', '=', $request->fname)
                                    ->where('middle_name', '=', $request->middle)
                                    ->where('last_name', '=', $request->lname)
                                    ->where('school_id', '=', $user->school_id)
                                    ->exists();

            if ($existingRecords) {
                Alert()->toast('Student with the same records already exists in our records', 'error');
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
                Alert()->toast('Student records saved successfully', 'success');
                return redirect()->route('create.selected.class', $class->id);
           }
    }

    //generate uniqe student admission number randomly*******************************************************
    protected function getAdmissionNumber ()
    {
        $user = Auth::user();
        $schoolData = school::where('id', $user->school_id)->first();
        do {
            // Generate a random 4-digit number between 1000 and 9999
            $admissionNumber = mt_rand(1000, 9999);

            // Check if this admission number already exists
        } while (Student::where('admission_number', $admissionNumber)->exists());

        return $schoolData->abbriv_code.'-'.$admissionNumber; // Return the unique admission number
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

    //  update students records *************************************************************************
    public function updateRecords(Request $request, $students)
    {
        try {
            $user = Auth::user();

            $request->merge(['group' => strtoupper($request->input('group'))]);

            $request->validate([
                'fname' => 'required|string|max:255',
                'middle' => 'required|string|max:255',
                'lname' => 'required|string|max:255',
                'class' => 'required|integer|exists:grades,id',
                'group' => 'required|in:A,B,C,D',
                'gender' => 'required|max:255',
                'dob' => 'required|date|date_format:Y-m-d',
                'driver' => 'integer|nullable|exists:transports,id',
                'image' => 'nullable|image|mimes:jpg,png,jpeg|max:512',
            ]);
            $student = Student::findOrFail($students);

            if($student->school_id != $user->school_id) {
                Alert()->toast('You are not authorized to perform this action', 'error');
                return back();
            }
            // return $student->first_name . ' '. $student->school_id. ' '. $student->parent_id;
            $student->first_name = $request->fname;
            $student->middle_name = $request->middle;
            $student->last_name = $request->lname;
            $student->class_id = $request->class;
            $student->group = $request->group;
            $student->gender = $request->gender;
            $student->dob = $request->dob;
            $student->transport_id = $request->driver;

            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageFile = time() . '.' . $image->getClientOriginalExtension();
                $imagePath = public_path('assets/img/students');

                // Ensure the directory exists
                if (!file_exists($imagePath)) {
                    mkdir($imagePath, 0775, true);
                }

                // Check if the existing file exists and delete it
                if (!empty($student->image)) {
                    $existingFile = $imagePath . '/' . $student->image;
                    if (file_exists($existingFile) && is_file($existingFile)) {
                        unlink($existingFile);
                    }
                }

                // Move the new file
                $image->move($imagePath, $imageFile);

                // Save the file name to the database
                $student->image = $imageFile;
            }

            $student->save();
            Alert()->toast('Student records updated successfully', 'success');
            return redirect()->route('Students.show', $students);
        }
        catch(\Exception $e) {
            Alert()->toast($e->getMessage(), 'error');
            return back();
        }

    }


    /**
     * Remove the resource from storage.
     */

    //  destroy or delete student records and move to trash ***********************************************
     public function destroy($student)
     {
         // Find the student record
         $user = Auth::user();
         $student = Student::findOrFail($student);

         if($student->school_id != $user->school_id) {
             Alert()->toast('You are not authorized to perform this action', 'error');
             return back();
         }
         //update status ------------
         $student->status = 2;
         $student->save();
         // Show success message
         Alert()->toast('Student records deleted successfully', 'success');
         return back();
     }

    //  get students information and compile to the form *******************************************************
    public function showStudent($class)
    {
        $user = Auth::user();
        $classId = Grade::findOrFail($class);
        $parents = Parents::query()
                            ->join('users', 'users.id', '=', 'parents.user_id')
                            ->join('schools', 'schools.id', '=', 'parents.school_id')
                            ->select('parents.id', 'users.first_name', 'users.last_name')
                            ->where('parents.school_id', '=', $user->school_id)
                            ->where('parents.status', '=', 1)
                            ->orderBy('users.first_name', 'ASC')
                            ->get();
        $buses = Transport::where('school_id', '=', $user->school_id)->where('status', '=', 1)->orderBy('bus_no', 'ASC')->get();
        $students = Student::query()
                            ->join('parents', 'parents.id', '=', 'students.parent_id')
                            ->join('users', 'users.id', '=', 'parents.user_id')
                            ->join('grades', 'grades.id', '=', 'students.class_id')
                            ->join('schools', 'schools.id', '=', 'students.school_id')
                            ->leftJoin('transports', 'transports.id', '=', 'students.transport_id')
                            ->select('students.id', 'students.first_name', 'students.middle_name', 'students.admission_number', 'students.last_name', 'students.gender', 'students.dob',
                                'transports.driver_name', 'transports.gender as driver_gender', 'transports.phone as driver_phone', 'transports.bus_no',
                                'transports.routine', 'schools.school_reg_no', 'schools.abbriv_code')
                            ->where('students.class_id', '=', $classId->id)
                            ->where('students.school_id', '=', $user->school_id)
                            ->where('students.status', 1)
                            ->orderBy('students.first_name', 'ASC')
                            ->get();
        $classes = Grade::where('id', '!=', $classId->id)->where('school_id', $user->school_id)->orderBy('class_code')->get();

        return view('Students.index', ['students' => $students, 'classId' => $classId, 'parents' => $parents, 'buses' => $buses, 'classes' => $classes]);
    }

    //promote students to the next class *******************************************************************
    public function promoteClass($id, Request $request)
    {
        $user = Auth::user();
        $class = Grade::find($id);
        if (! $class) {
            Alert()->toast('No such class was found', 'error');
            return back();
        }

        if($class->school_id != $user->school_id) {
            Alert()->toast('You are not authorized to perform this action', 'error');
            return back();
        }

        $this->validate($request, [
            'class_id' => 'required',
        ]);

        if ($request->class_id == 0) {
            // Mark students as graduated
            Student::where('class_id', $class->id)->where('school_id', $user->schoo_id)->update(['graduated' => true, 'status' => 0]);
            Alert()->toast('Students graduate batch has been submitted successfully', 'success');
            return back();
        } else {
            // Promote students to the next class
            Student::where('class_id', $class->id)->where('school_id', $user->school_id)->update(['class_id' => $request->class_id]);

            Alert()->toast('Students batch have been upgraded to the next class', 'success');
            return back();
        }

    }

    //call graduate class list by year updated_at ****************************************************************
    public function callGraduateStudents()
    {
        $user = Auth::user();
        $studentsByYear = Student::where('school_id', $user->school_id)
                        ->where('graduated', true)
                        ->where('status', 0)
                        ->select(DB::raw('YEAR(updated_at) as year'), 'id', 'first_name', 'updated_at')
                        ->orderBy('updated_at', )
                        ->get()
                        ->groupBy('year');
        return view('Students.graduate', compact('studentsByYear'));

    }

    //show graduated students in a specific year **************************************************************
    public function graduatedStudentByYear($year)
    {
        $user = Auth::user();

        $GraduatedStudents = Student::query()
                                    ->join('schools', 'schools.id', '=', 'students.school_id')
                                    ->select('students.*', 'schools.school_reg_no', 'schools.abbriv_code')
                                    ->where('school_id', $user->school_id)
                                    ->where('students.graduated', true)
                                    ->where('students.status', 0)
                                    ->whereYear('students.updated_at', $year)
                                    ->orderBy('students.first_name')
                                    ->get();
        return view('Students.graduate_students_list', compact('GraduatedStudents', 'year'));
    }

    //export graduated students in a specific year**********************************************************
    public function exportGraduateStudents($year)
    {
        $user = Auth::user();

        $studentExport = Student::query()
                                    ->join('schools', 'schools.id', '=', 'students.school_id')
                                    ->select('students.*', 'schools.school_reg_no', 'schools.abbriv_code')
                                    ->where('school_id', $user->school_id)
                                    ->where('students.graduated', true)
                                    ->where('students.status', 0)
                                    ->whereYear('students.updated_at', $year)
                                    ->orderBy('students.first_name')
                                    ->get();
        $pdf = \PDF::loadView('Students.ExportedGraduates', compact('studentExport', 'year'));
        return $pdf->stream('Graduate_students_'.$year.'.pdf');
    }

    //export to pdf ======================********************************************************************
    public function exportPdf($classId)
    {
        // return $classId;
        $user = Auth::user();
        $students = Student::query()->join('grades', 'grades.id', '=', 'students.class_id')
                                    ->join('schools', 'schools.id', '=', 'students.school_id')
                                    ->join('parents', 'parents.id', 'students.parent_id')
                                    ->leftJoin('users', 'users.id', '=', 'parents.user_id')
                                    ->select(
                                        'students.*',
                                        'grades.class_name', 'grades.class_code',
                                        'schools.school_name', 'schools.school_reg_no', 'schools.logo', 'schools.postal_address',
                                        'schools.postal_name', 'schools.country', 'schools.abbriv_code',
                                        'parents.address', 'users.phone'
                                    )
                                    ->where('students.class_id', $classId)
                                    ->where('students.status', 1)
                                    ->where('students.school_id', $user->school_id)
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
        return $pdf->stream($fileName);
    }

    // get information for parent to register him or herself ************************************************
    public function parentByStudent()
    {
        $user = Auth::user();
        $buses = Transport::where('school_id', '=', $user->school_id)->where('status', '=', 1)->orderBy('bus_no', 'ASC')->get();
        $classes = Grade::where('school_id', '=', $user->school_id)->where('status', '=', 1)->orderBy('class_code')->get();
        return view('Students.register', ['buses' => $buses, 'classes' => $classes ]);
    }

    // parent to register students **************************************************************************
    public function registerStudent (Request $request)
    {
        try {
            $user = Auth::user();
            $parent = Parents::where('user_id', $user->id)->first();

            $request->merge([
                'group' => strtoupper($request->input('group')),
            ]);

            $request->validate([
                'fname' => 'required|string|max:255',
                'middle' => 'required|string|max:255',
                'lname' => 'required|string|max:255',
                'gender' => 'required|string|max:255',
                'grade' => 'required|integer|exists:grades,id',
                'dob' => 'required|date|date_format:Y-m-d',
                'driver' => 'nullable|integer|exists:transports,id',
                'group' => 'required|string|in:A,B,C,D',
                'image' => 'nullable|image|mimes:jpg,png,jpeg|max:512',
                'school_id' => 'exists:schools,id',
            ]);

            // Check for existing student records
            $existingRecords = Student::where('first_name', $request->fname)
                                        ->where('middle_name',  $request->middle)
                                        ->where('last_name',  $request->lname)
                                        ->where('school_id', $user->school_id)
                                        ->exists();

            if ($existingRecords) {
                Alert()->toast('Student with the same records already exists', 'error');
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
            Alert()->toast('Student records saved successfully', 'success');
            return redirect()->route('home');
        }
        catch (\Exception $e) {
            Alert::error('Error', $e->getMessage());
            return back();
        }
    }

    // show student profile *****************************************************************************
    public function showRecords($student)
    {
        $user = Auth::user();
        // return $user;
        $parent = Parents::where('user_id', $user->id)->first();
        // return $parent;
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
                            'schools.school_reg_no', 'schools.abbriv_code'
                        )
                        ->where('students.id', '=', $student)
                        ->where('students.parent_id', $parent->id)
                        ->where('students.school_id', $user->school_id)
                        ->where('students.status', 1)
                        ->first();

        // Check if the data is found
        if (!$data) {
            // Handle the case where no data is found, e.g., return a 404 response
            Alert()->toast('No student found', 'error');
            return back();
        }

        // The student gender can be accessed directly from $data
        $studentGender = $data->gender;

        return view('Students.show', [
            'data' => $data,
            'studentGender' => $studentGender
        ]);
    }

    // parent modify student data ****************************************************************************
    public function modify($student)
    {
        $user = Auth::user();
        $parent = Parents::where('user_id', $user->id)->first();
        $students = Student::query()->join('parents', 'parents.id', '=', 'students.parent_id')
                                    ->join('users', 'users.id', '=', 'parents.user_id')
                                    ->join('grades', 'grades.id', '=', 'students.class_id')
                                    ->leftJoin('transports', 'transports.id', '=', 'students.transport_id')
                                    ->select('students.*', 'grades.class_name', 'grades.class_code', 'transports.driver_name', 'transports.bus_no')
                                    ->where('students.parent_id', $parent->id)
                                    ->findOrFail($student);

        $buses = Transport::where('status', '=', 1)->where('school_id', $user->school_id)->orderBy('bus_no', 'ASC')->get();
        $classes = Grade::where('status', '=', 1)->where('school_id', $user->school_id)->orderBy('class_code')->get();
        return view('Students.edit', ['buses' => $buses, 'students' => $students, 'classes' => $classes ]);
    }

    // show student list in the trash *****************************************************************
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

    // restore student in the trash *************************************************************************

    public function restoreTrashList ($id, Request $request)
    {
        $student = Student::find($id);

        if(! $student ) {
            Alert()->toast('No such student was found', 'error');
            return back();
        }

        $user = Auth::user();
        if($student->school_id != $user->school_id) {
            Alert()->toast('You are not authorized to perform this action', 'error');
            return back();
        }

        $status = 1;
        $student->update(['status' => $request->input('status', $status)]);
        Alert()->toast('Student has been restored successfully', 'success');
        return back();
    }

    // delete student partmently ***************************************************************************
    public function deletePerStudent($id)
    {
        $student = Student::find($id);


        if(! $student) {
            Alert()->toast('No such student was found', 'error');
            return back();
        }

        $user = Auth::user();
        if($student->school_id != $user->school_id) {
            Alert()->toast('You are not authorized to perform this action', 'error');
            return back();
        }

        $student->delete();
        Alert()->toast('Student has been deleted permanently', 'success');
        return back();
    }

}
