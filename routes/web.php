<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ClassesController;
use App\Http\Controllers\CoursesController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ManagerController;
use App\Http\Controllers\ParentsController;
use App\Http\Controllers\ResultsController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\SchoolsController;
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
});

Auth::routes();

// User registration controller redirection ===================================================================
    Route::prefix('Register')->group(function () {
        Route::get('Users', [UsersController::class, 'index'])->name('users.form');
        Route::post('Users', [UsersController::class, 'create'])->name('users.create');
    });
//end of condition ===========================================================================================

Route::group(['middleware' => ['auth']], function () {

    Route::middleware('CheckUsertype:1,2,3,4')->group(function () {
        // Home controller redirection ==============================================================================
        Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    });

    //this routes is special for admin - system administrator only =================================================
    Route::middleware('CheckUsertype:1')->group(function() {
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
    });
    // end of routes for administrator =============================================================

    /* parents route management==================================================================================
    route will be managed by school head teacher or school manager==============================================*/
    Route::middleware('ManagerOrTeacher')->group(function(){
        //teachers panel management =======================================================================
        Route::resource('Teachers', TeachersController::class);
        Route::put('{teachers}/Update-teachers', [TeachersController::class, 'updateTeachers'])->name('Update.teachers');
        Route::put('{teacher}/Teachers', [TeachersController::class, 'updateStatus'])->name('update.teacher.status');
        Route::put('{teacher}/Restore', [TeachersController::class, 'restoreStatus'])->name('teachers.restore');
        Route::get('{teacher}/Teacher-show', [TeachersController::class, 'showProfile'])->name('Teachers.show.profile');
        Route::get('{teacher}/Teacher', [TeachersController::class, 'destroy'])->name('Teachers.remove');

        //generate general attendance report =============================================================
        Route::get('Attendance-report', [AttendanceController::class, 'getField'])->name('attendance.fill.form');
        Route::post('Attendances', [AttendanceController::class, 'genaralAttendance'])->name('manage.attendance');

        //manage student registration forms and list ===========================================================
        Route::get('Class-lists', [StudentsController::class, 'index'])->name('classes.list');
        Route::get('{class}/Create-selected-class', [StudentsController::class, 'showStudent'])->name('create.selected.class');
        Route::get('{classId}/Student-registration', [StudentsController::class, 'create'])->name('student.create');
        Route::post('{class}/Student-registration', [StudentsController::class, 'createNew'])->name('student.store');

        //manage classses ========================================================================================
        Route::resource('Classes', ClassesController::class);
    });
    //manage parents informations ================================================================================
    Route::middleware('GeneralMiddleware')->group(function () {
        Route::resource('Parents', ParentsController::class);
        Route::put('{parent}/Update-teachers-status', [ParentsController::class, 'updateStatus'])->name('Update.parents.status');
        Route::put('{parent}/Restore-parents-status', [ParentsController::class, 'restoreStatus'])->name('restore.parents.status');
        Route::get('{parent}/Delete-permanent', [ParentsController::class, 'destroy'])->name('Parents.remove');
        Route::get('{parent}/Parents', [ParentsController::class, 'edit'])->name('Parents.edit');
        Route::put('{parents}/Update', [ParentsController::class, 'update'])->name('Parents.update');
    });

    Route::middleware('CheckUsertype:1,2,3,4')->group(function () {
        Route::prefix('Profile-management')->group(function () {
            Route::get('Change-password', [HomeController::class, 'changepassword'])->name('change.password');
            Route::post('Change-password', [HomeController::class, 'storePassword'])->name('change.new.password');
            Route::get('Persona-details', [HomeController::class, 'showProfile'])->name('show.profile');
            Route::put('{user}/Personal-details', [HomeController::class, 'updateProfile'])->name('update.profile');
        });
    });

    //parents its self manage its students individually ====================================================
    Route::middleware('CheckUsertype:4')->group(function () {
        Route::get('Register-student', [StudentsController::class, 'parentByStudent'])->name('parent.student.registration');
        Route::post('Register-students', [StudentsController::class, 'registerStudent'])->name('register.student');
        Route::get('{student}/Edit-student', [StudentsController::class, 'modify'])->name('students.modify');
        Route::put('{students}/Update-student', [StudentsController::class, 'updateRecords'])->name('students.update.records');
    });

    //access students information ===========================================================================
    Route::middleware('CheckUsertype:2,3,4')->group(function () {
        Route::get('{student}/Show-Students', [StudentsController::class, 'showRecords'])->name('Students.show');
    });

    //assign class teachers======================================================================================
    Route::middleware('CheckUsertype:3')->group(function () {
        Route::middleware('CheckRoleType:3')->group(function () {
            Route::post('{classes}/Assign-Teacher', [RolesController::class, 'store'])->name('Class.teacher.assign');
            Route::get('{teacher}/Edit', [RolesController::class, 'edit'])->name('roles.edit');
            Route::put('{classTeacher}/Update', [RolesController::class, 'update'])->name('roles.update');
            Route::get('{teacher}/Delete', [RolesController::class, 'destroy'])->name('roles.destroy');
        });
    });

    //teacher manager attendance .......................======================================================
    Route::middleware('CheckUsertype:3')->group(function() {
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

    Route::middleware('CheckUsertype:3')->group(function() {
        Route::middleware('CheckRoleType:1,3,4')->group(function () {
            Route::get('{course}/Prepare', [ExamController::class, 'prepare'])->name('score.prepare.form');
            Route::post('Examination-result-create', [ExamController::class, 'captureValues'])->name('score.captured.values');
            Route::post('Upload/results', [ExamController::class, 'storeScore'])->name('exams.store.score');
            Route::get('{courses}/view-results', [ExamController::class, 'viewResultCourse'])->name('course.results');
            Route::get('/exams/{year}', [ExamController::class, 'viewResultsByYear'])->name('exams.byYear');
            Route::get('/exams/{year}/{type}', [ExamController::class, 'viewResultsByType'])->name('exams.byType');
        });
    });

    Route::middleware('CheckRoleType:1,3,4')->group(function () {
        Route::post('Register-course', [CoursesController::class, 'store'])->name('courses.store');
        Route::get('{course}/Edit-course', [CoursesController::class, 'edit'])->name('courses.edit');
        Route::put('{courses}/Update-course', [CoursesController::class, 'update'])->name('courses.update');

    });

    //access this routes for subjects if the usertype as manager of school head teacher only ==========
    Route::middleware('ManagerOrTeacher')->group(function () {
        Route::get('View-all', [CoursesController::class, 'index'])->name('courses.index');
        Route::get('{class}/Class-courses', [CoursesController::class, 'classCourses'])->name('courses.view.class');
        Route::get('{course}/Delete-course', [CoursesController::class, 'deleteCourse'])->name('courses.delete');
        Route::put('{course}/Block', [CoursesController::class, 'blockCourse'])->name('courses.block');
        Route::put('{course}/Unblock', [CoursesController::class, 'unblockCourse'])->name('courses.unblock');
        Route::get('{course}/Assign-teacher', [CoursesController::class, 'assign'])->name('courses.assign');
        Route::put('{courses}/Assigned-teacher', [CoursesController::class, 'assignedTeacher'])->name('courses.assigned.teacher');
        //view class teachers-----------------------
        Route::get('{class}/Class-Teacher', [RolesController::class, 'update'])->name('Class.Teachers');
    });
    //end of condition =================================================================================

    //anothe routes for registering examination and this is performed by academic only ============================
    Route::middleware('CheckUsertype:3')->group(function () {
        Route::middleware('CheckRoleType:3')->group(function () {
            Route::post('Register', [ExamController::class, 'store'])->name('exams.store');
            Route::get('{exam}/Delete', [ExamController::class, 'destroy'])->name('exams.destroy');
            Route::put('{exam}/Block', [ExamController::class, 'blockExams'])->name('exams.block');
            Route::put('{exam}/Unblock', [ExamController::class, 'unblockExams'])->name('exams.unblock');
            Route::get('{exam}/Edit', [ExamController::class, 'edit'])->name('exams.edit');
            Route::put('{exams}/Update', [ExamController::class, 'update'])->name('exams.update');
        });
    });
    //manage examination results in general schools ========================================================
    Route::middleware('ManagerOrTeacher')->group(function () {
        // view examination lists =========================================================================
        Route::get('Examination-test', [ExamController::class, 'index'])->name('exams.index');
        //end or examination lists =======================================================================
        Route::get('Results-management/{school}', [ResultsController::class, 'general'])->name('results.general');
        Route::get('Results-management/{school}/year/{year}', [ResultsController::class, 'classesByYear'])->name('results.classesByYear');
        Route::get('Results-management/{school}/year/{year}/class/{class}', [ResultsController::class, 'examTypesByClass'])->name('results.examTypesByClass');
        Route::get('Results-management/{school}/year/{year}/class/{class}/exam-type/{examType}/months', [ResultsController::class, 'monthsByExamType'])->name('results.monthsByExamType');
        Route::get('Results-management/{school}/year/{year}/class/{class}/exam-type/{examType}/month/{month}', [ResultsController::class, 'resultsByMonth'])->name('results.resultsByMonth');
    });
    //end of condition ===========================================================================================

    //error page routes redirection==================================
    Route::get('Error', [UsersController::class, 'errorPage'])->name('error.page');

    //results viewing by parents routes redirection ==================================================================
    Route::middleware('CheckUsertype:4')->group(function () {
        Route::get('Exam-results/{student}', [ResultsController::class, 'index'])->name('results.index');
        Route::get('Result-type/{student}/{year}', [ResultsController::class, 'resultByType'])->name('result.byType');
        Route::get('Results/{student}/{year}/{type}', [ResultsController::class, 'viewStudentResult'])->name('results.student.get');
    });
    //End of condition ==============================================================================================

    //delete students records =======================================================================================
    Route::middleware('GeneralMiddleware')->group(function () {
        Route::get('{student}/Delete-student', [StudentsController::class, 'destroy'])->name('Students.destroy');

        //reset users passwords =====================================================================================
        Route::prefix('User-management')->group(function () {
            Route::get('Password-Reset', [RolesController::class, 'userPassword'])->name('users.lists');
            Route::put('{user}/Reset', [RolesController::class, 'resetPassword'])->name('users.reset.password');
        });

        //students using school bus==================================================================================
        Route::get('{trans}/Student-tranport', [TransportController::class, 'showStudents'])->name('students.transport');
        Route::resource('Transportation', TransportController::class);
        Route::put('{trans}/Transport-block', [TransportController::class, 'update'])->name('transport.update');
        Route::put('{trans}/Transport-unblock', [TransportController::class, 'restore'])->name('transport.restore');
        Route::get('{trans}/Delete-permanent', [TransportController::class, 'destroy'])->name('transport.remove');
        Route::get('{trans}/Transport-Edit', [TransportController::class, 'Edit'])->name('transport.edit');
        Route::put('{transport}/Update', [TransportController::class, 'UpdateRecords'])->name('transport.update.records');

        //assign new roles ==========================================================================================
        Route::middleware('GeneralMiddleware')->group(function () {
            Route::get('Update-roles', [RolesController::class, 'updateRoles'])->name('roles.updateRole');
            Route::get('{user}/Assign-role', [RolesController::class, 'assignRole'])->name('roles.assign');
            Route::put('{user}/Update-role', [RolesController::class, 'AssignNewRole'])->name('roles.assign.new');
        });
    });
    //end of condition =========================================================================================
});
