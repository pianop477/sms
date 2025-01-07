<?php
use App\Exports\StudentsExport;
use App\Exports\TeachersExport;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ClassesController;
use App\Http\Controllers\CoursesController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ManagerController;
use App\Http\Controllers\ParentsController;
use App\Http\Controllers\PdfGeneratorController;
use App\Http\Controllers\ResultsController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\SchoolsController;
use App\Http\Controllers\SendMessageController;
use App\Http\Controllers\StudentsController;
use App\Http\Controllers\SubjectsController;
use App\Http\Controllers\TeachersController;
use App\Http\Controllers\TransportController;
use App\Http\Controllers\UsersController;
use App\Models\Attendance;
use App\Models\Examination_result;
use App\Models\Parents;
use App\Models\Student;
use App\Models\Transport;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use PhpParser\Builder\ClassConst;
use PHPUnit\Runner\ResultCache\ResultCache;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
})->name('welcome');


Auth::routes();

// User registration controller redirection ===================================================================
    Route::prefix('Register')->group(function () {
        Route::get('Users', [UsersController::class, 'index'])->name('users.form');
        Route::post('Users', [UsersController::class, 'create'])->name('users.create');
    });
//end of condition ===========================================================================================
//any user to send message as feedback ==============================
    Route::post('/Feedback', [SendMessageController::class, 'store'])->name('send.feedback.message');
//end of condition =================================================

Route::middleware('auth', 'activeUser', 'throttle:60,1')->group(function () {
    Route::middleware('checkSessionTimeout')->group(function() {
        Route::middleware('CheckUsertype:1,2,3,4')->group(function () {
            // Home controller redirection ==============================================================================
            Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
        });
        //this routes is special for admin - system administrator only =================================================
        Route::middleware(['CheckUsertype:1'])->group(function() {
            Route::get('Registration', [UsersController::class, 'managerForm'])->name('register.manager');
            Route::post('Manager-register', [ManagerController::class, 'store'])->name('manager.store');
            Route::put('{school}/Deactivation', [ManagerController::class, 'updateStatus'])->name('deactivate.status');
            Route::put('{school}/Activate', [ManagerController::class, 'activateStatus'])->name('activate.status');
            //register new schools or institutions=====================================================================
            Route::resource('Schools', SchoolsController::class);
            Route::get('Admin-reset-password', [ManagerController::class, 'reset'])->name('admin.reset.password');
            Route::put('{user}/Update', [ManagerController::class, 'resetPassword'])->name('admin.update.password');
            Route::get('{school}/About-school', [SchoolsController::class, 'show'])->name('schools.show');
            Route::get('{school}/Invoice', [SchoolsController::class, 'invoceCreate'])->name('admin.generate.invoice');
            //edit school information ===================================================================================
            Route::get('{school}/Edit-school', [SchoolsController::class, 'edit'])->name('schools.edit');
            Route::put('{school}/Update-school', [SchoolsController::class, 'updateSchool'])->name('schools.update.school');
            Route::get('{id}/Delete-school', [SchoolsController::class, 'destroy'])->name('schools.destroy');
            Route::get('Feedback', [SchoolsController::class, 'showFeedback'])->name('feedback');
            Route::get('{sms}/Delete-feedback', [SchoolsController::class, 'deletePost'])->name('delete.post');

            //manager assign other teacher to become manager too.
            Route::put('{user}/Change-usertype', [RolesController::class, 'changeUsertype'])->name('change.usertype');

            //manage admin accounts
            Route::get('Admin-accounts', [UsersController::class, 'manageAdminAccounts'])->name('admin.accounts');
            Route::post('Register-admin-accounts', [UsersController::class, 'addAdminAccount'])->name('admin.accounts.registration');
            Route::put('{id}/Block-admin-accounts', [UsersController::class, 'blockAdminAccount'])->name('admin.account.block');
            Route::put('{id}/Unblock-admin-accounts', [UsersController::class, 'unblockAdminAccount'])->name('admin.account.unblock');
            Route::get('{id}/Delete-admin-accounts', [UsersController::class, 'deleteAdminAccount'])->name('admin.account.destroy');
        });
        // end of routes for administrator =============================================================

        /* parents route management==================================================================================
        route will be managed by school head teacher or school manager==============================================*/
        Route::middleware(['ManagerOrTeacher'])->group(function(){
            //teachers panel management =======================================================================
            // Route::resource('Teachers', TeachersController::class);
            Route::get('Teachers-list', [TeachersController::class, 'showTeachersList'])->name('Teachers.index');
            Route::post('Teachers-registration', [TeachersController::class, 'registerTeachers'])->name('Teachers.store');
            Route::put('{teachers}/Update-teachers', [TeachersController::class, 'updateTeachers'])->name('Update.teachers');
            Route::put('{teacher}/Block-teacher', [TeachersController::class, 'updateStatus'])->name('update.teacher.status');
            Route::put('{teacher}/Restore-teacher', [TeachersController::class, 'restoreStatus'])->name('teachers.restore');
            Route::get('{teacher}/Teacher-show', [TeachersController::class, 'showProfile'])->name('Teachers.show.profile');
            Route::put('{teacher}/Delete-teacher', [TeachersController::class, 'deleteTeacher'])->name('Teachers.remove');
            Route::get('Deleted-teachers', [TeachersController::class, 'trashedTeachers'])->name('Teachers.trashed');

            //generate general attendance report =============================================================
            Route::get('Attendance-report', [AttendanceController::class, 'getField'])->name('attendance.fill.form');
            Route::post('Attendances', [AttendanceController::class, 'genaralAttendance'])->name('manage.attendance');

            //manage student registration forms and list ===========================================================
            Route::get('Class-lists', [StudentsController::class, 'index'])->name('classes.list');
            Route::get('{class}/Create-selected-class', [StudentsController::class, 'showStudent'])->name('create.selected.class');
            Route::get('{classId}/Student-registration', [StudentsController::class, 'create'])->name('student.create');
            Route::post('{class}/Student-registration', [StudentsController::class, 'createNew'])->name('student.store');
            Route::put('{id}/Promote-students', [StudentsController::class, 'promoteClass'])->name('promote.student.class');
            Route::get('Graduate-student', [StudentsController::class, 'callGraduateStudents'])->name('graduate.students');
            Route::get('Graduate-students/year/{year}', [StudentsController::class, 'graduatedStudentByYear'])->name('graduate.student.by.year');
            Route::get('Export-graduate-students/year/{year}', [StudentsController::class, 'exportGraduateStudents'])->name('graduate.students.export');
            Route::put('{student}/Delete-student', [StudentsController::class, 'destroy'])->name('Students.destroy');
            Route::get('Student-trash', [StudentsController::class, 'studentTrashList'])->name('students.trash');
            Route::put('{id}/Restore-trashed-students', [StudentsController::class, 'restoreTrashList'])->name('student.restored.trash');
            Route::get('{id}/Delete-student-permanent', [StudentsController::class, 'deletePerStudent'])->name('student.delete.permanent');

            //manage classses ========================================================================================
            // Route::resource('Classes', ClassesController::class);
            Route::get('Classes-list', [ClassesController::class, 'showAllClasses'])->name('Classes.index');
            Route::post('Register-class', [ClassesController::class, 'registerClass'])->name('Classes.store');
            Route::get('{id}/Edit-class', [ClassesController::class, 'editClass'])->name('Classes.edit');
            Route::put('{id}/Update-class', [ClassesController::class, 'updateClass'])->name('Classes.update');
            Route::delete('{id}/Delete-class', [ClassesController::class, 'deleteClass'])->name('Classes.destroy');
        });

        //manage parents informations ================================================================================
    Route::middleware(['ManagerOrTeacher'])->group(function () {
        // Route::resource('Parents', ParentsController::class);
        Route::get('Parents', [ParentsController::class, 'showAllParents'])->name('Parents.index');
        Route::post('Register-parents', [ParentsController::class, 'registerParents'])->name('Parents.store');
        Route::put('{parent}/Update-teachers-status', [ParentsController::class, 'updateStatus'])->name('Update.parents.status');
        Route::put('{parent}/Restore-parents-status', [ParentsController::class, 'restoreStatus'])->name('restore.parents.status');
        Route::put('{parent}/Delete-permanent', [ParentsController::class, 'deleteParent'])->name('Parents.remove');
        Route::get('{parent}/Edit-parents', [ParentsController::class, 'editParent'])->name('Parents.edit');
        Route::put('{parents}/Update-parents', [ParentsController::class, 'updateParent'])->name('Parents.update');
    });

    Route::middleware(['CheckUsertype:1,2,3,4'])->group(function () {
        Route::prefix('Profile-management')->group(function () {
            Route::get('Change-password', [HomeController::class, 'changepassword'])->name('change.password');
            Route::post('Change-password', [HomeController::class, 'storePassword'])->name('change.new.password');
            Route::get('Persona-details', [HomeController::class, 'showProfile'])->name('show.profile');
            Route::put('{user}/Personal-details', [HomeController::class, 'updateProfile'])->name('update.profile');
        });
    });

    //parents its self manage its students individually ====================================================
    Route::middleware(['CheckUsertype:4'])->group(function () {
        Route::get('Register-student', [StudentsController::class, 'parentByStudent'])->name('parent.student.registration');
        Route::post('Register-students', [StudentsController::class, 'registerStudent'])->name('register.student');
    });

    //free role routes for edit students
    Route::get('{student}/Edit-student', [StudentsController::class, 'modify'])->name('students.modify');
    Route::put('{students}/Update-student', [StudentsController::class, 'updateRecords'])->name('students.update.records');

    //access students information ===========================================================================
    Route::middleware(['CheckUsertype:2,3,4'])->group(function () {
        Route::get('{student}/Show-Students', [StudentsController::class, 'showRecords'])->name('Students.show');
    });

    //assign class teachers======================================================================================
    Route::middleware(['CheckUsertype:3'])->group(function () {
        Route::middleware('CheckRoleType:3')->group(function () {
            Route::get('{teacher}/Edit', [RolesController::class, 'edit'])->name('roles.edit');
            Route::put('{classTeacher}/Update-class-teacher', [RolesController::class, 'update'])->name('roles.update.class.teacher');
            Route::get('{teacher}/Delete', [RolesController::class, 'destroy'])->name('roles.destroy');
        });
    });

    //assign class teacher ===========
   Route::middleware(['CheckUsertype:3'])->group(function () {
        Route::middleware('CheckRoleType:2,3')->group(function () {
            Route::post('{classes}/Assign-Teacher', [RolesController::class, 'store'])->name('Class.teacher.assign');
        });
   });

    //teacher manager attendance .......................======================================================
    Route::middleware(['CheckUsertype:3'])->group(function() {
        Route::middleware('CheckRoleType:4')->group(function () {
            Route::get('{class}/Student-list', [AttendanceController::class, 'index'])->name('get.student.list');
            Route::post('{student_class}/Create-Attendance', [AttendanceController::class, 'store'])->name('store.attendance');
            Route::get('{class}/Class-attendance', [AttendanceController::class, 'teacherAttendance'])->name('teachers.show.attendance');
            Route::get('{class}/Download-Attendance-PDF', [AttendanceController::class, 'downloadAttendancePDF'])->name('download.attendance.pdf');
            Route::get('{class}/Generate-attendance-report', [AttendanceController::class, 'getFormReport'])->name('attendance.get.form');
            Route::get('{student_class}/Today-summary-report', [AttendanceController::class, 'todayAttendance'])->name('today.attendance');
            Route::post('{classTeacher}/Generate-attendance-report', [AttendanceController::class, 'generateReport'])->name('attendance.generate.report');
        });
    });

    //parents view attendance of specific student============================================================
    Route::middleware(['CheckUsertype:4'])->group(function() {
        Route::get('Student-attendance/{student}/{year}', [AttendanceController::class, 'show'])->name('students.show.attendance');
        Route::get('{student}/Attendance-year', [AttendanceController::class, 'attendanceYear'])->name('attendance.byYear');
    });

    Route::middleware(['CheckUsertype:3'])->group(function() {
        Route::middleware('CheckRoleType:1,3,4')->group(function () {
            Route::get('{id}/Prepare', [ExamController::class, 'prepare'])->name('score.prepare.form');
            Route::post('Examination-result-create', [ExamController::class, 'captureValues'])->name('score.captured.values');
            Route::post('Upload/results', [ExamController::class, 'storeScore'])->name('exams.store.score');
            Route::get('/Results-saved', [ExamController::class, 'savedDataForm'])->name('form.saved.values');
            //teachers  examination results =============================
            Route::get('course{id}/Results', [ExamController::class, 'courseResults'])->name('results_byCourse');
            Route::get('/Results/course{course}/&year{year}', [ExamController::class, 'resultByYear'])->name('results.byYear');
            Route::get('/Results/course{course}/&year{year}/&examination{examType}', [ExamController::class, 'resultByExamType'])->name('results.byExamType');
            Route::get('/Results/course{course}/&year{year}/&examination{examType}/{month}', [ExamController::class, 'resultByMonth'])->name('results.byMonth');
        });
    });

    //access this routes for subjects if the usertype as manager of school head teacher only ==========
    Route::middleware(['ManagerOrTeacher'])->group(function () {
        Route::get('View-all', [CoursesController::class, 'index'])->name('courses.index');
        Route::get('{id}/Class-courses', [CoursesController::class, 'classCourses'])->name('courses.view.class');
        Route::get('{id}/Delete-course', [CoursesController::class, 'deleteCourse'])->name('courses.delete');
        Route::put('{id}/Block', [CoursesController::class, 'blockCourse'])->name('courses.block');
        Route::put('{id}/Unblock', [CoursesController::class, 'unblockCourse'])->name('courses.unblock');
        Route::get('{id}/Assign-teacher', [CoursesController::class, 'assign'])->name('courses.assign');
        Route::put('{id}/Assigned-teacher', [CoursesController::class, 'assignedTeacher'])->name('courses.assigned.teacher');
        //view class teachers-----------------------
        Route::get('{class}/Class-Teacher', [RolesController::class, 'index'])->name('Class.Teachers');
        Route::put('{id}/Block-assigned-class-course', [CoursesController::class, 'blockAssignedCourse'])->name('block.assigned.course');
        Route::put('{id}/Unblock-assigned-class-course', [CoursesController::class, 'unblockAssignedCourse'])->name('unblock.assigned.course');

    });
    //end of condition =================================================================================

    //anothe routes for registering examination and this is performed by academic only ============================
    Route::middleware(['CheckUsertype:3'])->group(function () {
        Route::middleware('CheckRoleType:3')->group(function () {
            Route::post('/Examination-type/Register', [ExamController::class, 'store'])->name('exams.store');
            Route::get('{exam}/Examination-type/Delete', [ExamController::class, 'destroy'])->name('exams.destroy');
            Route::put('{exam}/Examination-type/Block', [ExamController::class, 'blockExams'])->name('exams.block');
            Route::put('{exam}/Examination-type/Unblock', [ExamController::class, 'unblockExams'])->name('exams.unblock');
            Route::get('{exam}/Examination-type/Edit', [ExamController::class, 'edit'])->name('exams.type.edit');
            Route::put('{exams}/Examination-type/Update', [ExamController::class, 'update'])->name('exams.update');
        });
    });
    //manage examination results in general schools ========================================================
    Route::middleware(['ManagerOrTeacher'])->group(function () {
        // view examination lists =========================================================================
        Route::get('Examination-test', [ExamController::class, 'index'])->name('exams.index');
        //end or examination lists =======================================================================
        Route::get('General-results/{school}', [ResultsController::class, 'general'])->name('results.general');
        Route::get('General-results/{school}/year/{year}', [ResultsController::class, 'classesByYear'])->name('results.classesByYear');
        Route::get('General-results/{school}/year/{year}/class/{class}', [ResultsController::class, 'examTypesByClass'])->name('results.examTypesByClass');
        Route::get('General-results/{school}/year/{year}/class/{class}/exam-type/{examType}/months', [ResultsController::class, 'monthsByExamType'])->name('results.monthsByExamType');
        Route::get('General-results/{school}/year/{year}/class/{class}/exam-type/{examType}/month/{month}', [ResultsController::class, 'resultsByMonth'])->name('results.resultsByMonth');
    });
    //end of condition ===========================================================================================

    //error page routes redirection==================================
    Route::get('Error', [UsersController::class, 'errorPage'])->name('error.page');

    //results viewing by parents routes redirection ==================================================================
    Route::middleware(['CheckUsertype:4'])->group(function () {
        Route::get('Exam-results/Student/{student}', [ResultsController::class, 'index'])->name('results.index');
        Route::get('Result-type/Student/{student}/Year/{year}', [ResultsController::class, 'resultByType'])->name('result.byType');
        // For displaying months
        Route::get('Result-months/Student/{student}/Year/{year}/Type/{exam_type}', [ResultsController::class, 'resultByMonth'])->name('result.byMonth');
        Route::get('Results/Student/{student}/Year/{year}/Exam-type/{type}/Month/{month}', [ResultsController::class, 'viewStudentResult'])->name('results.student.get');
        Route::get('student/{id}/Courses-list', [CoursesController::class, 'viewStudentCourses'])->name('student.courses.list');

    });
    //End of condition ==============================================================================================

    //delete students records =======================================================================================
    Route::middleware(['GeneralMiddleware'])->group(function () {
        //reset users passwords =====================================================================================
        Route::prefix('User-management')->group(function () {
            Route::get('Password-Reset', [RolesController::class, 'userPassword'])->name('users.lists');
            Route::put('{user}/Reset', [RolesController::class, 'resetPassword'])->name('users.reset.password');
        });

        //students using school bus==================================================================================
        Route::get('{trans}/Student-tranport', [TransportController::class, 'showStudents'])->name('students.transport');
        // Route::resource('Transportation', TransportController::class);
        Route::get('Transport', [TransportController::class, 'getSchoolBuses'])->name('Transportation.index');
        Route::post('Register-transport', [TransportController::class, 'registerDrivers'])->name('Transportation.store');
        Route::put('{trans}/Transport-block', [TransportController::class, 'update'])->name('transport.update');
        Route::put('{trans}/Transport-unblock', [TransportController::class, 'restore'])->name('transport.restore');
        Route::get('{trans}/Delete-permanent', [TransportController::class, 'destroy'])->name('transport.remove');
        Route::get('{trans}/Transport-Edit', [TransportController::class, 'Edit'])->name('transport.edit');
        Route::put('{transport}/Update', [TransportController::class, 'UpdateRecords'])->name('transport.update.records');
        Route::get('{trans}/Export-transport', [TransportController::class, 'export'])->name('transport.export');

        //assign new roles ==========================================================================================
        Route::get('Update-roles', [RolesController::class, 'updateRoles'])->name('roles.updateRole');
        Route::get('{user}/Assign-role', [RolesController::class, 'assignRole'])->name('roles.assign');
        Route::put('{user}/Update-role', [RolesController::class, 'AssignNewRole'])->name('roles.assign.new');
    });

    Route::middleware(['ManagerOrTeacher'])->group(function() {
        Route::put('Publish-results/school/{school}/year/{year}/class/{class}/examType/{examType}/month/{month}', [ResultsController::class, 'publishResult'])->name('publish.results');
        Route::put('Unpublish-results/school/{school}/year/{year}/class/{class}/examType/{examType}/month/{month}', [ResultsController::class, 'unpublishResult'])->name('unpublish.results');
        //export students records to PDF
        Route::get('{classId}/Export-students', [StudentsController::class, 'exportPdf'])->name('export.student.pdf');

        //register school courses/manage all
        Route::post('Register-courses', [CoursesController::class, 'addCourse'])->name('course.registration');
        Route::get('{id}/Edit-courses', [CoursesController::class, 'editCourse'])->name('course.edit');
        Route::put('{id}/Update-courses', [CoursesController::class, 'updateCourse'])->name('course.update');
        Route::post('Assign-class-course', [CoursesController::class, 'assignClassCourse'])->name('course.assign');

    });
    //end of condition =========================================================================================

     //generate /export teachers excel template====================
        Route::get('/teachers/export', function () {
            return Excel::download(new TeachersExport, 'teachers.xlsx');
        })->name('teachers.excel.export');
        Route::get('/teachers/pdf', [TeachersController::class, 'export'])->name('teachers.pdf.export');
    });
});
