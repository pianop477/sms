<?php

namespace App\Http\Controllers;

use App\Models\class_learning_courses;
use App\Models\Class_teacher;
use App\Models\Grade;
use App\Models\holiday_package;
use App\Models\Parents;
use App\Models\school;
use App\Models\Student;
use App\Models\Transport;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Psy\Command\WhereamiCommand;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Storage;
use Vinkla\Hashids\Facades\Hashids;

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
            $decoded = Hashids::decode($class);
            if(empty($decoded)) {
                Alert()->toast('No such class was found', 'error');
                return back();
            }
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
                'image' => [
                    'nullable',
                    'file',
                    'mimetypes:image/jpeg,image/png,image/jpg',
                    'max:1024'
                ],
                'school_id' => 'exists:schools,id'
            ], [
                'fname.required' => 'First name is required',
                'middle.required' => 'Middle name is required',
                'lname.required' => 'Last name is required',
                'dob.required' => 'Date of birth is required',
                'group.required' => 'stream must be provided',
                'image.mimetypes' => 'Image must be a file of type: jpg, png, jpeg',
                'image.max' => 'Image size must not exceed 1MB',
            ]);

            if($request->hasFile('image')) {
                $scanResult = $this->scanFileForViruses($request->file('image'));
                if (!$scanResult['clean']) {
                    Alert()->toast('File security check failed: ' . $scanResult['message'], 'error');
                    return redirect()->back();
                }
            }

            DB::beginTransaction();
            try {
                // Check for existing student records
                            $existingStudent = Student::whereRaw('LOWER(first_name) = ?', [strtolower($request->fname)])
                            ->whereRaw('LOWER(middle_name) = ?', [strtolower($request->middle)])
                            ->whereRaw('LOWER(last_name) = ?', [strtolower($request->lname)])
                            ->where('school_id', $user->school_id)
                            ->first();

                if ($existingStudent) {
                Alert()->toast('Student with the same records already exists in our records', 'error');
                return back();
                }

                // Retrieve the class
                $class = Grade::findOrFail($decoded[0]);

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

                DB::commit();
                if($student) {
                    Alert()->toast('Student records saved successfully', 'success');
                    return redirect()->route('create.selected.class', Hashids::encode($class->id));
                }
            } catch (\Exception $e) {
                DB::rollBack();
                Alert()->toast($e->getMessage(), 'error');
                return back();
            }
    }

    //generate uniqe student admission number randomly*******************************************************
    protected function getAdmissionNumber()
    {
        $user = Auth::user();
        $schoolData = School::findOrFail($user->school_id);

        // Pata ID ya mwisho ya mwanafunzi na uongeze 1
        $lastStudent = Student::where('school_id', $user->school_id)
                            ->orderBy('id', 'desc')
                            ->first();

        $lastId = $lastStudent ? $lastStudent->id + 1 : 1;

        // Hakikisha kuwa ID ni ya kipekee
        $admissionNumber = str_pad($lastId, 4, '0', STR_PAD_LEFT);

        // Hakikisha admission number ni ya kipekee
        while (Student::where('admission_number', $schoolData->abbriv_code . '-' . $admissionNumber)->exists()) {
            $lastId++;
            $admissionNumber = str_pad($lastId, 4, '0', STR_PAD_LEFT);
        }

        // Rudisha nambari ya kujiunga kwa kutumia kifupi cha shule na ID
        return $schoolData->abbriv_code . '-' . $admissionNumber;
    }

    /**
     * Display students belongs to a certain parent.
     */
    public function showMyChildren($student)
    {
        //
        $decoded = Hashids::decode($student);
        // return $decoded;
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
                            'grades.id as class_id',
                            'transports.driver_name as driver', 'transports.gender as driver_gender', 'transports.phone as driver_phone', 'transports.bus_no as bus_number',
                            'transports.routine as bus_routine',
                            'grades.id as grade_class_id',
                            'schools.school_reg_no', 'schools.abbriv_code'
                        )
                        ->where('students.id', '=', $decoded[0])
                        // ->where('students.parent_id', $parent->id)
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

        return view('Students.parent_show_student', [
            'data' => $data,
            'studentGender' => $studentGender
        ]);
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
        $decoded = Hashids::decode($students);
        // return $decoded;
        if(empty($decoded)) {
            Alert()->toast('No such student was found', 'error');
            return back();
        }
        $studentId = $decoded[0];

        $student = Student::find($studentId);

            $request->merge(['group' => strtoupper($request->input('group'))]);

            $request->validate([
                'fname' => 'required|string|max:255',
                'middle' => 'required|string|max:255',
                'lname' => 'required|string|max:255',
                'parent' => 'integer|exists:parents,id',
                'class' => 'required|integer|exists:grades,id',
                'group' => 'required|in:A,B,C,D',
                'gender' => 'required|max:255',
                'dob' => 'required|date|date_format:Y-m-d',
                'driver' => 'integer|nullable|exists:transports,id',
                'image' => [
                    'nullable',
                    'file',
                    'mimetypes:image/jpeg,image/png,image/jpg',
                    'max:1024'
                ],
            ], [
                'fname.required' => 'First name is required',
                'middle.required' => 'Middle name is required',
                'lname.required' => 'Last name is required',
                'parent.exists' => 'Parent does not exist',
                'class.exists' => 'Class does not exist',
                'group.required' => 'stream must be provided',
                'dob.required' => 'Date of birth is required',
                'image.mimetypes' => 'Image must be a file of type: jpg, png, jpeg',
                'image.max' => 'Image size must not exceed 1MB',
            ]);

            if($request->hasFile('image')) {
                $scanResult = $this->scanFileForViruses($request->file('image'));
                if (!$scanResult['clean']) {
                    Alert()->toast('File security check failed: ' . $scanResult['message'], 'error');
                    return redirect()->back();
                }
            }

            // return $student->first_name . ' '. $student->school_id. ' '. $student->parent_id;
            $student->first_name = $request->fname;
            $student->middle_name = $request->middle;
            $student->last_name = $request->lname;
            $student->class_id = $request->class;
            $student->parent_id = $request->parent;
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
            return redirect()->route('manage.student.profile', Hashids::encode($student->id));

    }

    //parent update student information
    public function updateMyChildren(Request $request, $students)
    {
        $decoded = Hashids::decode($students);
        // return $decoded;
        if(empty($decoded)) {
            Alert()->toast('No such student was found', 'error');
            return back();
        }
        $studentId = $decoded[0];

        $student = Student::find($studentId);

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
                'image' => [
                    'nullable',
                    'file',
                    'mimetypes:image/jpeg,image/png,image/jpg',
                    'max:1024'
                ],
            ], [
                'fname.required' => 'First name is required',
                'middle.required' => 'Middle name is required',
                'lname.required' => 'Last name is required',
                'class.exists' => 'Class does not exist',
                'group.required' => 'stream must be provided',
                'dob.required' => 'Date of birth is required',
                'image.mimetypes' => 'Image must be a file of type: jpg, png, jpeg',
                'image.max' => 'Image size must not exceed 1MB',
            ]);

            if($request->hasFile('image')) {
                $scanResult = $this->scanFileForViruses($request->file('image'));
                if (!$scanResult['clean']) {
                    Alert()->toast('File security check failed: ' . $scanResult['message'], 'error');
                    return redirect()->back();
                }
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
            return redirect()->route('students.profile', Hashids::encode($student->id));
    }


    /**
     * Remove the resource from storage.
     */

    //  destroy or delete student records and move to trash ***********************************************
     public function destroy($student)
     {
         // Find the student record
         $decoded = Hashids::decode($student);
         if(empty($decoded)) {
             Alert()->toast('No such student was found', 'error');
             return back();
         }
         $user = Auth::user();
         $student = Student::findOrFail($decoded[0]);

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
        $decoded = Hashids::decode($class);
        // return $decoded;
        $user = Auth::user();
        $classId = Grade::findOrFail($decoded[0]);
        $parents = Parents::query()
                            ->join('users', 'users.id', '=', 'parents.user_id')
                            ->join('schools', 'schools.id', '=', 'parents.school_id')
                            ->select('parents.id', 'users.first_name', 'users.last_name', 'users.phone')
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
                                'transports.routine', 'schools.school_reg_no', 'schools.abbriv_code', 'students.group', 'students.image')
                            ->where('students.class_id', '=', $classId->id)
                            ->where('students.school_id', '=', $user->school_id)
                            ->where('students.status', 1)
                            ->orderBy('students.first_name', 'ASC')
                            ->get();
        $classes = Grade::where('id', '!=', $classId->id)->where('school_id', $user->school_id)->orderBy('class_code')->get();

        return view('Students.index', ['students' => $students, 'classId' => $classId, 'parents' => $parents, 'buses' => $buses, 'classes' => $classes]);
    }

    //promote students to the next class *******************************************************************
    public function promoteClass($class, Request $request)
    {
        $decoded = Hashids::decode($class);
        if (empty($decoded)) {
            Alert()->toast('Invalid class identifier', 'error');
            return back();
        }

        $user = Auth::user();
        $classes = Grade::find($decoded[0]);

        if (!$classes) {
            Alert()->toast('No such class was found', 'error');
            return back();
        }

        if ($classes->school_id != $user->school_id) {
            Alert()->toast('You are not authorized to perform this action', 'error');
            return back();
        }

        $this->validate($request, [
            'class_id' => 'required|integer',
            'graduation_year' => 'nullable|integer|min:'.(date('Y')-5).'|max:'.(date('Y'))
        ], [
            'class_id.required' => "Select class you want to upgrade to",
            'graduation_year.min' => 'Invalid year format, minimum is five years ago',
            'graduation_year.max' => 'Invalid year format'
        ]);

        try {
            DB::beginTransaction();

            if ($request->class_id == 0) {
                $updated = Student::where('class_id', $classes->id)
                    ->where('school_id', $user->school_id) // Fix typo here
                    ->update([
                        'graduated' => true,
                        'graduated_at' => $request->graduation_year,
                        'status' => $request->class_id, // or whatever status value you want
                    ]);
            } else {
                // Promote to next class
                $updated = Student::where('class_id', $classes->id)
                    ->where('school_id', $user->school_id)
                    ->update(['class_id' => $request->class_id]);
            }

            DB::commit();

            if ($updated) {
                Alert()->toast(
                    $request->class_id == 0
                        ? 'Students graduated batch saved successfully'
                        : 'Students promoted and upgraded successfully',
                    'success'
                );
            } else {
                Alert()->toast('No students were found to update', 'info');
            }

        } catch (\Exception $e) {
            DB::rollBack();
            Alert()->toast('An error occurred during the operation', 'error');
            // Log::error("Promotion error: " . $e->getMessage());
        }

        return back();
    }

    //call graduate class list by year updated_at ****************************************************************
    public function callGraduateStudents()
    {
        $user = Auth::user();
        $studentsByYear = Student::where('school_id', $user->school_id)
                        ->where('graduated', true)
                        ->where('status', 0)
                        ->select(DB::raw('YEAR(graduated_at) as year'), 'id', 'first_name', 'graduated_at')
                        ->orderBy('graduated_at', 'desc')
                        ->get()
                        ->groupBy('year');
        return view('Students.graduate', compact('studentsByYear'));

    }

    //show graduated students in a specific year **************************************************************
    public function graduatedStudentByYear()
    {
        $user = Auth::user();

        // Get all distinct years for grouping links
        $graduationYears = Student::where('school_id', $user->school_id)
                            ->where('graduated', true)
                            ->where('status', 0)
                            ->selectRaw('YEAR(graduated_at) as year')
                            ->distinct()
                            ->orderBy('year', 'DESC')
                            ->pluck('year');
        // dd($graduationYears);
        // Get students for the selected year
        $GraduatedStudents = Student::query()
                                    ->join('schools', 'schools.id', '=', 'students.school_id')
                                    ->select('students.*', 'schools.school_reg_no', 'schools.abbriv_code')
                                    ->where('school_id', $user->school_id)
                                    ->where('students.graduated', true)
                                    ->where('students.status', 0)
                                    // ->whereYear('students.graduated_at', $graduationYears)
                                    ->orderBy('students.first_name')
                                    ->get();

        return view('Students.graduate_students_list', compact('GraduatedStudents', 'graduationYears'));
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
                                    ->whereYear('students.graduated_at', $year)
                                    ->orderBy('students.first_name')
                                    ->get();
        $pdf = \PDF::loadView('Students.ExportedGraduates', compact('studentExport', 'year'));
        return $pdf->stream('Graduate_students_'.$year.'.pdf');
    }

    // revert graduate student batch **********************************************************************
    public function revertStudentBatch(Request $request, $year)
    {
        $user = Auth::user();

        try {
            DB::beginTransaction();

            // Update the students who graduated in the specified year
            $updated = Student::where('school_id', $user->school_id)
                ->whereYear('graduated_at', $year)
                ->update([
                    'graduated' => false,
                    'graduated_at' => null,
                    'status' => 1, // Set status to active
                ]);

            DB::commit();

            if ($updated) {
                Alert()->toast('Students reverted to active status successfully', 'success');
            } else {
                Alert()->toast('No students were found to update', 'info');
            }

        } catch (\Exception $e) {
            DB::rollBack();
            Alert()->toast('An error occurred during the operation', 'error');
        }

        return back();
    }

    //export to pdf ======================********************************************************************
    public function exportPdf($class)
    {
        $decoded_class = Hashids::decode($class);

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
                                    ->where('students.class_id', $decoded_class[0])
                                    ->where('students.status', 1)
                                    ->where('students.school_id', $user->school_id)
                                    ->orderBy('students.first_name')
                                    ->get();
        $pdf = \PDF::loadView('Students.student_export', compact('students'));
        // return $pdf->download($students->first()->class_name.' students.pdf');

        if ($students->isNotEmpty()) {
            $className = $students->first()->class_name;
            $fileName = "{$className} Student-list.pdf";
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
                'image' => [
                    'nullable',
                    'file',
                    'mimetypes:image/jpeg,image/png,image/jpg',
                    'max:1024'
                ],
                'school_id' => 'exists:schools,id',
            ], [
                'fname.required' => 'First name is required',
                'middle.required' => 'Middle name is required',
                'lname.required' => 'Last name is required',
                'grade.exists' => 'Selected class does not exist',
                'group.required' => 'stream must be provided',
                'dob.required' => 'Date of birth is required',
                'image.mimetypes' => 'Image must be a file of type: jpg, png, jpeg',
                'image.max' => 'Image size must not exceed 1MB',
            ]);

            $scanResult = $this->scanFileForViruses($request->file('image'));
            if (!$scanResult['clean']) {
                Alert()->toast('File security check failed: ' . $scanResult['message'], 'error');
                return redirect()->back();
            }

            // Check for existing student records
            $existingStudent = Student::whereRaw("
                                LOWER(TRIM(REPLACE(first_name, '  ', ' '))) = ?
                                AND LOWER(TRIM(REPLACE(middle_name, '  ', ' '))) = ?
                                AND LOWER(TRIM(REPLACE(last_name, '  ', ' '))) = ?
                                AND school_id = ?", [
                                strtolower(preg_replace('/\s+/', ' ', trim($request->fname))),
                                strtolower(preg_replace('/\s+/', ' ', trim($request->middle))),
                                strtolower(preg_replace('/\s+/', ' ', trim($request->lname))),
                                $user->school_id
                            ])->first();

            if ($existingStudent) {
                    Alert()->toast('Student with the same records already exists', 'error');
                    return back();
            }

            DB::beginTransaction();
            try {
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
            DB::commit();

            // Return success message
            Alert()->toast('Student records saved successfully', 'success');
            return redirect()->route('home');
            }
            catch(Exception $e) {
                DB::rollBack();
                Alert()->toast($e->getMessage(), 'error');
                return back();
            }
    }

    // show student profile *****************************************************************************
    public function showRecords($student)
    {
        $decoded = Hashids::decode($student);
        // return $decoded;
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
                            'parents.address',
                            'users.first_name as parent_first_name',
                            'users.last_name as parent_last_name',
                            'users.id as user_id',
                            'parents.id as parent_id',
                            'users.phone',
                            'users.gender as parent_gender', 'users.created_at as parent_created_at',
                            'grades.class_name',
                            'grades.class_code',
                            'grades.id as class_id',
                            'transports.driver_name', 'transports.gender as driver_gender', 'transports.phone as driver_phone', 'transports.bus_no',
                            'transports.routine',
                            'schools.school_reg_no', 'schools.abbriv_code'
                        )
                        ->where('students.id', '=', $decoded[0])
                        // ->where('students.parent_id', $parent->id)
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
            'students' => $data,
            'studentGender' => $studentGender
        ]);
    }

    //show student profile new version by teacher
    public function getStudentProfile($student)
    {
        $decoded = Hashids::decode($student);
        // return $decoded;
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
                            'parents.address',
                            'users.first_name as parent_first_name',
                            'users.last_name as parent_last_name',
                            'users.id as user_id',
                            'users.created_at as parent_created_at',
                            'parents.id as parent_id',
                            'users.phone',
                            'users.gender as parent_gender',
                            'grades.class_name',
                            'grades.class_code',
                            'grades.id as class_id',
                            'transports.driver_name', 'transports.gender as driver_gender', 'transports.phone as driver_phone', 'transports.bus_no',
                            'transports.routine',
                            'grades.id as grade_class_id',
                            'schools.school_reg_no', 'schools.abbriv_code'
                        )
                        ->where('students.id', '=', $decoded[0])
                        // ->where('students.parent_id', $parent->id)
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

        return view('Students.teacher_student_profile', [
            'students' => $data,
            'studentGender' => $studentGender
        ]);
    }

    public function classTeacherStudentProfile($student)
    {
        $decoded = Hashids::decode($student);
        // return $decoded;
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
                            'parents.address',
                            'users.first_name as parent_first_name',
                            'users.last_name as parent_last_name',
                            'users.id as user_id',
                            'users.created_at as parent_created_at',
                            'parents.id as parent_id',
                            'users.phone',
                            'users.gender as parent_gender',
                            'grades.class_name',
                            'grades.class_code',
                            'grades.id as class_id',
                            'transports.driver_name', 'transports.gender as driver_gender', 'transports.phone as driver_phone', 'transports.bus_no',
                            'transports.routine',
                            'grades.id as grade_class_id',
                            'schools.school_reg_no', 'schools.abbriv_code'
                        )
                        ->where('students.id', '=', $decoded[0])
                        // ->where('students.parent_id', $parent->id)
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

        return view('Students.class_teacher_student_profile', [
            'students' => $data,
            'studentGender' => $studentGender
        ]);
    }


    // parent modify student data ****************************************************************************
    public function modify($student)
    {
        $user = Auth::user();

        $decoded = Hashids::decode($student);

        if(empty($decoded)) {
            Alert()->toast('No such student was found', 'error');
            return back();
        }
        // return $user;
        $students = Student::query()->join('parents', 'parents.id', '=', 'students.parent_id')
                                    ->leftJoin('users', 'users.id', '=', 'parents.user_id')
                                    ->join('grades', 'grades.id', '=', 'students.class_id')
                                    ->leftJoin('transports', 'transports.id', '=', 'students.transport_id')
                                    ->select('students.*', 'grades.class_name',
                                    'grades.id as grade_class_id', 'users.phone',
                                    'grades.class_code', 'transports.driver_name', 'transports.bus_no')
                                    ->findOrFail($decoded[0]);

        $parent = Parents::query()
                        ->join('users', 'users.id', '=', 'parents.user_id')
                        ->select('users.first_name', 'users.last_name', 'parents.id as parent_id', 'users.phone')
                        ->findOrFail($students->parent_id);

        $allParents = Parents::query()
                            ->join('users', 'users.id', '=', 'parents.user_id')
                            ->select('users.first_name', 'users.status', 'users.last_name', 'parents.id as parent_id', 'users.phone')
                            ->where('users.status', 1)
                            ->where('users.school_id', $user->school_id)
                            ->orderBy('users.first_name')
                            ->get();

        $buses = Transport::where('status', '=', 1)->where('school_id', $user->school_id)->orderBy('bus_no', 'ASC')->get();
        $classes = Grade::where('status', '=', 1)->where('school_id', $user->school_id)->orderBy('class_code')->get();
        return view('Students.edit', ['buses' => $buses, 'students' => $students, 'classes' => $classes, 'parents' => $parent, 'allParents' => $allParents ]);
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
                            ->orderBy('students.first_name', 'ASC')
                            ->get();
        return view('Students.trash', compact('students'));
    }

    //parent edit file blade view *************************************************************************
    public function editMyStudent($student)
    {
        $user = Auth::user();
        $parent = Parents::where('user_id', $user->id)->first();

        $decoded = Hashids::decode($student);

        if(empty($decoded)) {
            Alert()->toast('No such student was found', 'error');
            return back();
        }
        // return $user;
        $students = Student::query()->join('parents', 'parents.id', '=', 'students.parent_id')
                                    ->join('users', 'users.id', '=', 'parents.user_id')
                                    ->join('grades', 'grades.id', '=', 'students.class_id')
                                    ->leftJoin('transports', 'transports.id', '=', 'students.transport_id')
                                    ->select('students.*', 'grades.class_name',
                                    'grades.id as grade_class_id',
                                    'grades.class_code', 'transports.driver_name', 'transports.bus_no')
                                    ->findOrFail($decoded[0]);

        $buses = Transport::where('status', '=', 1)->where('school_id', $user->school_id)->orderBy('bus_no', 'ASC')->get();
        $classes = Grade::where('status', '=', 1)->where('school_id', $user->school_id)->orderBy('class_code')->get();
        return view('Students.parent_edit_student', ['buses' => $buses, 'students' => $students, 'classes' => $classes ]);
    }

    // restore student in the trash *************************************************************************

    public function restoreTrashList ($student, Request $request)
    {
        $decoded = Hashids::decode($student);
        if(empty($decoded)) {
            Alert()->toast('No such student was found', 'error');
            return back();
        }
        $student = Student::find($decoded[0]);

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
    public function deletePerStudent($student)
    {
        $decoded = Hashids::decode($student);
        if(empty($decoded)) {
            Alert()->toast('No such student was found', 'error');
            return back();
        }
        $student = Student::find($decoded[0]);


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

    public function batchUpdateStream (Request $request)
    {
       try {
            $request->validate([
                'student' => 'required|array',
                'new_stream' => 'required|string|max:10',
            ]);

            Student::whereIn('id', $request->student)
                    ->update(['group' => $request->new_stream]);

            Alert()->toast('Students stream updated successfully', 'success');
            return back();
       }
       catch(Exception $e) {
            Alert()->toast($e->getMessage(), 'error');
            return back();
       }
    }

    public function studentProfile($student)
    {
        $id = Hashids::decode($student);

        $students = Student::query()
                            ->join('grades', 'grades.id', '=', 'students.class_id')
                            ->join('parents', 'parents.id', '=', 'students.parent_id')
                            ->leftJoin('users', 'users.id', '=', 'parents.user_id')
                            ->leftJoin('transports', 'transports.id', '=', 'students.transport_id')
                            ->select(
                                'students.*',
                                'grades.class_name', 'grades.class_code',
                                'parents.address',
                                'users.first_name as parent_first_name', 'users.last_name as parent_last_name',
                                'users.phone', 'transports.driver_name', 'transports.bus_no', 'transports.routine', 'transports.gender as driver_gender',
                                'transports.phone as driver_phone', 'users.gender as parent_gender', 'users.created_at as parent_created_at',
                                'users.email',
                            )
                            ->findOrFail($id[0]);

        $class = Grade::findOrFail($students->class_id);

        $user = Auth::user();

        if(! $class) {
            Alert()->toast('No class details was found', 'error');
            return back();
        }
        $class_course = class_learning_courses::query()
                                            ->join('subjects', 'subjects.id', '=', 'class_learning_courses.course_id')
                                            ->join('grades', 'grades.id', '=', 'class_learning_courses.class_id')
                                            ->join('teachers', 'teachers.id', '=', 'class_learning_courses.teacher_id')
                                            ->leftJoin('users', 'users.id', '=', 'teachers.user_id')
                                            ->select(
                                                'class_learning_courses.*', 'grades.class_name', 'subjects.course_name', 'subjects.course_code',
                                                'users.first_name', 'users.last_name', 'users.phone as teacher_phone', 'users.image'
                                            )
                                            ->where('class_learning_courses.class_id', $class->id)
                                            ->where('class_learning_courses.school_id', $user->school_id)
                                            ->get();
        //fetch class teacher details

        $myClassTeacher = Class_teacher::query()
                                        ->join('teachers', 'teachers.id', '=', 'class_teachers.teacher_id')
                                        ->leftJoin('users', 'users.id', '=', 'teachers.user_id')
                                        ->join('grades', 'grades.id', '=', 'class_teachers.class_id')
                                        ->select('users.first_name', 'users.last_name', 'users.phone', 'users.gender', 'users.image', 'class_teachers.*', 'grades.class_name')
                                        ->where('class_teachers.class_id', $class->id)
                                        ->where('class_teachers.group', $students->group)
                                        ->where('class_teachers.school_id', $user->school_id)
                                        ->orderBY('users.first_name')
                                        ->get();

        // fetch active holiday package
        $packages = holiday_package::where('school_id', $user->school_id)
                                    ->where('class_id', $class->id)
                                    // ->where('is_active', true)
                                    ->whereYear('year', date('Y'))
                                    ->orderBy('created_at', 'DESC')
                                    ->orderBy('updated_at', 'DESC')
                                    ->take(5)
                                    ->get();

        return view('profile.student_profile', compact('students', 'myClassTeacher', 'class_course', 'class', 'packages'));
    }

    private function scanFileForViruses($file): array
    {
        // For production, use actual API
        if (app()->environment('production')) {
            $apiKey = config('services.virustotal.key');
            try {
                $response = Http::withHeaders(['x-apikey' => $apiKey])
                            ->attach('file', fopen($file->path(), 'r'))
                            ->post('https://www.virustotal.com/api/v3/files');

                if ($response->successful()) {
                    $scanId = $response->json()['data']['id'];
                    $analysis = Http::withHeaders(['x-apikey' => $apiKey])
                                ->get("https://www.virustotal.com/api/v3/analyses/{$scanId}");

                    return [
                        'clean' => $analysis->json()['data']['attributes']['stats']['malicious'] === 0,
                        'message' => $analysis->json()['data']['attributes']['status']
                    ];
                }
            } catch (\Exception $e) {
                return [
                    'clean' => false,
                    'message' => 'Scan failed: '.$e->getMessage()
                ];
            }
        }

        // For local development, just mock a successful scan
        return ['clean' => true, 'message' => 'Development mode - scan bypassed'];
    }
}
