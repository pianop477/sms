<?php
use App\Exports\StudentsExport;
use App\Exports\TeachersExport;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BiometricController;
use App\Http\Controllers\CertificateGeneratorController;
use App\Http\Controllers\CertificateTemplateController;
use App\Http\Controllers\ClassesController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\CoursesController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\GeneratedReportController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ManagerController;
use App\Http\Controllers\PackagesController;
use App\Http\Controllers\ParentsController;
use App\Http\Controllers\PdfGeneratorController;
use App\Http\Controllers\ResultsController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\SchoolsController;
use App\Http\Controllers\SendMessageController;
use App\Http\Controllers\SmsController;
use App\Http\Controllers\StudentsController;
use App\Http\Controllers\SubjectsController;
use App\Http\Controllers\TeachersController;
use App\Http\Controllers\TimetableController;
use App\Http\Controllers\TodRosterController;
use App\Http\Controllers\TransportController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\WebAuthn\WebAuthnLoginController;
use App\Http\Controllers\WebAuthn\WebAuthnRegisterController;
use App\Models\Attendance;
use App\Models\Examination_result;
use App\Models\Parents;
use App\Models\Student;
use App\Models\Transport;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
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

Route::get('/check-session', function () {
    return response()->json(['active' => auth()->check()]);
});

// Throttle login route - max 5 attempts per 1 minute
Route::post('login', [LoginController::class, 'login'])
            ->middleware('throttle:3,1')
            ->name('login');

Auth::routes();

// Show biometric login page
Route::get('/login/biometric', [WebAuthnLoginController::class, 'show'])->name('webauthn.login');

// Start WebAuthn login challenge
Route::post('/login/biometric/options', [WebAuthnLoginController::class, 'options'])->name('webauthn.login.options');

// Verify credential and login
Route::post('/login/biometric/verify', [WebAuthnLoginController::class, 'verify'])->name('webauthn.login.verify');


Route::post('/webauthn/register/options', [WebAuthnRegisterController::class, 'options'])->name('webauthn.register.options');
Route::post('/webauthn/register/verify', [WebAuthnRegisterController::class, 'register'])->name('webauthn.register.verify');

// ROUTE ACCESS FOR PARENTS REGISTRATION =================================================================================================
    Route::prefix('Register')->group(function () {
        Route::get('Parents', [UsersController::class, 'index'])->name('users.form');
        Route::post('Parents', [UsersController::class, 'create'])->name('users.create');
    });

// ROUTES ACCESS FOR USERS TO SEND FEEDBACK TO THE SYSTEM AND GET SUPPORT ================================================================
    Route::post('/Feedback', [SendMessageController::class, 'store'])->name('send.feedback.message');

//ROUTES ACCESS TO SCAN AND VERIFY QR CODE FOR CONTRACTS =========================================================
    Route::get('/contracts/verify/{token}', [ContractController::class, 'verify'])->name('contracts.verify');

    // Biometric OTP Routes
    Route::post('/biometric/send-otp', [BiometricController::class, 'sendOtp']);
    Route::post('/biometric/verify-otp', [BiometricController::class, 'verifyOtp']);

    // WebAuthn Routes (existing)
    Route::post('/webauthn/register/options', [WebAuthnRegisterController::class, 'registerOptions']);
    Route::post('/webauthn/register/verify', [WebAuthnRegisterController::class, 'registerVerify']);
    Route::post('/webauthn/login/options', [WebAuthnLoginController::class, 'loginOptions']);
    Route::post('/webauthn/login/verify', [WebAuthnLoginController::class, 'loginVerify']);

    // routes/web.php
    Route::post('/webauthn/delete-credentials', [BiometricController::class, 'deleteCredentials']);

// MIDDLEWARE FILTERING STARTS HERE **************************************************************************************
Route::middleware('auth', 'activeUser', 'throttle:30,1', 'checkSessionTimeout', 'block.ip', 'user.agent')->group(function () {

    /*
        =======================MPANGILIO WA ROUTES ZOTE UKO HAPA KULINGANA NA MAHITAJI YA MFUMO NA=========================================
        =================================AINA YA USER, PERMISSION NA ROLE YAKE KATIKA MFUMO================================================
    */

    // 0. ROUTES ACCESS FOR SUPER USER ADMIN (SYSTEM ADMINISTRATOR) ======================================================================
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
        Route::get('{school}/Invoice/send', [SchoolsController::class, 'sendInvoice'])->name('admin.send.invoice');
        Route::post('school/{school}/manager/{manager}/Invoice/send', [SchoolsController::class, 'sendSmsInvoce'])->name('send.sms.invoice');
        //edit school information ===================================================================================
        Route::get('{school}/Edit-school', [SchoolsController::class, 'edit'])->name('schools.edit');
        Route::put('{school}/Update-school', [SchoolsController::class, 'updateSchool'])->name('schools.update.school');
        Route::get('{school}/Delete-school', [SchoolsController::class, 'destroy'])->name('schools.destroy');
        Route::get('Feedback', [SchoolsController::class, 'showFeedback'])->name('feedback');
        Route::get('{sms}/Delete-feedback', [SchoolsController::class, 'deletePost'])->name('delete.post');
        Route::post('Send-reply-message', [SchoolsController::class, 'sendFeebackReply'])->name('send.reply.message');
        Route::get('{sms}/Reply', [SchoolsController::class, 'replyFeedback'])->name('reply.post');

        //check for failed login attempts
        Route::get('/failed-logins', [SchoolsController::class, 'faileLoginAttempts'])->name('failed.login.attempts');

        //Approve or reject school registration ====================================================================
        Route::get('Approve-school/{school}', [SchoolsController::class, 'approveSchool'])->name('approve.school');
        Route::put('Approve-school-request/{school}', [SchoolsController::class, 'addActiveTime'])->name('approve.school.request');

        //manager assign other teacher to become manager too.
        Route::put('{user}/Change-usertype', [RolesController::class, 'changeUsertype'])->name('change.usertype');

        //manage admin accounts
        Route::get('Admin-accounts', [UsersController::class, 'manageAdminAccounts'])->name('admin.accounts');
        Route::post('Register-admin-accounts', [UsersController::class, 'addAdminAccount'])->name('admin.accounts.registration');
        Route::put('{user}/Block-admin-accounts', [UsersController::class, 'blockAdminAccount'])->name('admin.account.block');
        Route::put('{user}/Unblock-admin-accounts', [UsersController::class, 'unblockAdminAccount'])->name('admin.account.unblock');
        Route::get('{user}/Delete-admin-accounts', [UsersController::class, 'deleteAdminAccount'])->name('admin.account.destroy');
        Route::get('{user}/Edit-admin-accounts', [UsersController::class, 'editAdminAccount'])->name('admin.account.edit');
        Route::put('{user}/Update-admin-accounts', [UsersController::class, 'updateAdminAccount'])->name('admin.account.update');
    });

    //1. SHARED ROUTES ACCESS MANAGER/HEAD TEACHER/ACADEMIC =======================================
    Route::middleware(['manager.head.academic'])->group(function () {
        // teachers management
        Route::get('Teachers-list', [TeachersController::class, 'showTeachersList'])->name('Teachers.index');
        Route::post('Teachers-registration', [TeachersController::class, 'registerTeachers'])->name('Teachers.store');
        Route::put('{teachers}/Update-teachers', [TeachersController::class, 'updateTeachers'])->name('Update.teachers');
        Route::put('{teacher}/Block-teacher', [TeachersController::class, 'updateStatus'])->name('update.teacher.status');
        Route::put('{teacher}/Restore-teacher', [TeachersController::class, 'restoreStatus'])->name('teachers.restore');
        Route::get('{teacher}/Teacher-show', [TeachersController::class, 'showProfile'])->name('Teachers.show.profile');
        Route::get('Teacher/profile/id/{teacher}', [TeachersController::class, 'teacherProfile'])->name('teacher.profile');
        Route::get('/teachers/export', function () {
            return Excel::download(new TeachersExport, 'teachers.xlsx');
        })->name('teachers.excel.export');
        Route::get('/teachers/pdf', [TeachersController::class, 'export'])->name('teachers.pdf.export');

        // parents management
        Route::get('Parents', [ParentsController::class, 'showAllParents'])->name('Parents.index');
        Route::post('Register-parents', [ParentsController::class, 'registerParents'])->name('Parents.store');
        Route::put('{parent}/Update-teachers-status', [ParentsController::class, 'updateStatus'])->name('Update.parents.status');
        Route::put('{parent}/Restore-parents-status', [ParentsController::class, 'restoreStatus'])->name('restore.parents.status');
        Route::put('{parent}/Delete-permanent', [ParentsController::class, 'deleteParent'])->name('Parents.remove');
        Route::get('{parent}/Edit-parents', [ParentsController::class, 'editParent'])->name('Parents.edit');
        Route::put('{parents}/Update-parents', [ParentsController::class, 'updateParent'])->name('Parents.update');
        Route::post('/import-parents-students', [ParentsController::class, 'import'])->name('import.parents.students');
        Route::get('Export-templates', [ParentsController::class, 'exportFile'])->name('template.export');

        //send sms to specific class
        Route::get('Send-messages-by-class', [SmsController::class, 'smsForm'])->name('sms.form');
        Route::post('Send-sms', [SmsController::class, 'sendSms'])->name('send.message.byBeem'); //send using beem api
        Route::post('Send-message', [SmsController::class, 'sendSmsUsingNextSms'])->name('Send.message.byNext'); //send using nextSms api

        //manage student registration forms and list ===========================================================
        Route::get('Class-lists', [StudentsController::class, 'index'])->name('classes.list');
        Route::get('{class}/Create-selected-class', [StudentsController::class, 'showStudent'])->name('create.selected.class');
        Route::get('{classId}/Student-registration', [StudentsController::class, 'create'])->name('student.create');
        Route::post('{class}/Student-registration', [StudentsController::class, 'createNew'])->name('student.store');
        Route::put('{class}/Promote-students', [StudentsController::class, 'promoteClass'])->name('promote.student.class');
        Route::get('Graduate-student', [StudentsController::class, 'callGraduateStudents'])->name('graduate.students');
        Route::get('Graduate-students/year/{year}', [StudentsController::class, 'graduatedStudentByYear'])->name('graduate.student.by.year');
        Route::get('Export-graduate-students/year/{year}', [StudentsController::class, 'exportGraduateStudents'])->name('graduate.students.export');
        Route::get('/graduate-students', [StudentsController::class, 'graduatedStudentByYear'])->name('graduate.students.by.year');
        Route::put('graduate-students/revert/year/{year}', [StudentsController::class, 'revertStudentBatch'])->name('revert.student.batch');
        Route::post('{student}/Delete-student', [StudentsController::class, 'destroy'])->name('Students.destroy');
        Route::get('Student-trash', [StudentsController::class, 'studentTrashList'])->name('students.trash');
        Route::put('{student}/Restore-trashed-students', [StudentsController::class, 'restoreTrashList'])->name('student.restored.trash');
        Route::get('{student}/Delete-student-permanent', [StudentsController::class, 'deletePerStudent'])->name('student.delete.permanent');
        Route::get('Manage/Student-profile/id/{student}', [StudentsController::class, 'getStudentProfile'])->name('manage.student.profile');
        Route::get('{student}/Show-Students', [StudentsController::class, 'showRecords'])->name('Students.show');
        Route::get('Edit-student/{students}', [StudentsController::class, 'modify'])->name('students.modify');
        Route::put('{students}/Update-student', [StudentsController::class, 'updateRecords'])->name('students.update.records');

        // classes and students management
        Route::get('Classes-list', [ClassesController::class, 'showAllClasses'])->name('Classes.index');
        Route::get('{class}/Export-students', [StudentsController::class, 'exportPdf'])->name('export.student.pdf');
        Route::post('/students/batch-update-stream', [StudentsController::class, 'batchUpdateStream'])->name('students.batchUpdateStream');

        // courses and class course management
        Route::get('View-all', [CoursesController::class, 'index'])->name('courses.index');
        Route::get('{id}/Class-courses', [CoursesController::class, 'classCourses'])->name('courses.view.class');
        Route::get('{class}/Class-Teacher', [RolesController::class, 'index'])->name('Class.Teachers');

        // examination management
        Route::get('Examination-test', [ExamController::class, 'index'])->name('exams.index');

        // results management
        Route::get('General-results/{school}', [ResultsController::class, 'general'])->name('results.general');
        Route::get('General-results/{school}/year/{year}', [ResultsController::class, 'classesByYear'])->name('results.classesByYear');
        Route::get('General-results/{school}/year/{year}/class/{class}', [ResultsController::class, 'examTypesByClass'])->name('results.examTypesByClass');
        Route::get('General-results/{school}/year/{year}/class/{class}/exam-type/{examType}/months', [ResultsController::class, 'monthsByExamType'])->name('results.monthsByExamType');
        Route::get('General-results/{school}/year/{year}/class/{class}/exam-type/{examType}/month/{month}/date/{date}', [ResultsController::class, 'resultsByMonth'])->name('results.resultsByMonth');
        Route::get('Individual-student-reports/school/{school}/year/{year}/class/{class}/examType/{examType}/month/{month}/date/{date}', [ResultsController::class, 'individualStudentReports'])->name('individual.student.reports');
        Route::get('Download-individual-report/school/{school}/year/{year}/class/{class}/examType/{examType}/month/{month}/student/{student}/date/{date}/', [ResultsController::class, 'downloadIndividualReport'])->name('download.individual.report');
        Route::get('Delete/generated-report/school/{school}/year/{year}/class/{class}/report/{report}', [ResultsController::class, 'destroyReport'])->name('generated.report.delete');
        Route::get('/Students/generated-report/school/{school}/year/{year}/class/{class}/report/{report}', [ResultsController::class, 'studentGeneratedCombinedReport'])->name('students.combined.report');
        Route::get('/student/generated-report/school/{school}/year/{year}/class/{class}/report/{report}/student/{student}', [ResultsController::class, 'showStudentCompiledReport'])->name('students.report');
        Route::post('/send/sms/combine/report/school/{school}/year/{year}/class/{class}/report/{report}/student/{student}', [ResultsController::class, 'sendSmsForCombinedReport'])->name('send.sms.combine.report');
        Route::get('Download-combined-report/school/{school}/year/{year}/class/{class}/report/{report}/student/{student}', [ResultsController::class, 'downloadCombinedReport'])->name('download.combined.report');
        Route::put('Publish-combine-report/school/{school}/year/{year}/class/{class}/report/{report}', [ResultsController::class, 'publishCombinedReport'])->name('publish.combined.report');
        Route::get('/General-combined-report/school/{school}/year/{year}/class/{class}/report/{report}', [ResultsController::class, 'downloadGeneralCombinedReport'])->name('download.general.combined');
        Route::put('Unpublish/combined-report/school/{school}/year/{year}/class/{class}/report/{report}', [ResultsController::class, 'unpublishCombinedReport'])->name('Unpublish.combined.report');
        Route::post('Send-results-sms/school/{school}/year/{year}/class/{class}/examType/{examType}/month/{month}/student/{student}/date/{date}', [ResultsController::class, 'sendResultSms'])->name('sms.results');
        Route::get('Delete-student-result/school/{school}/year/{year}/class/{class}/examType/{examTyoe}/month/{month}/student/{student}/date/{date}', [ResultsController::class, 'deleteStudentResult'])->name('delete.student.result');

        // packages management
        Route::get('/Packages/year', [PackagesController::class, 'packagesByYear'])->name('package.byYear');
        Route::get('Package/by/class/year/{year}', [PackagesController::class, 'packageByClass'])->name('package.byClass');
        Route::get('/holiday/package/list/year/{year}/class/{class}', [PackagesController::class, 'packagesLists'])->name('packages.list');
        Route::get('Download/package/id/{id}', [PackagesController::class, 'downloadPackage'])->name('download.holiday.package');

        //generate general attendance report
        Route::get('Attendance-report', [AttendanceController::class, 'getField'])->name('attendance.fill.form');
        Route::post('Attendances', [AttendanceController::class, 'genaralAttendance'])->name('manage.attendance');
        Route::post('Generate-attendance-report', [AttendanceController::class, 'generateClassReport'])->name('class.attendance.report');
        Route::get('Deleted-teachers', [TeachersController::class, 'trashedTeachers'])->name('Teachers.trashed');
        Route::get('/api/search-students', [StudentsController::class, 'searchStudent'])->name('api.search.students');
    });

    // 2. ROUTE ACCESS FOR EITHER MANAGER OR HEAD TEACHER ONLY ===========================================================================
    Route::middleware(['ManagerOrTeacher'])->group(function(){
        //teachers panel management =======================================================================
        Route::put('{teacher}/Delete-teacher', [TeachersController::class, 'deleteTeacher'])->name('Teachers.remove');

        // users management & permission
        Route::get('Password-Reset', [RolesController::class, 'userPassword'])->name('users.lists');
        Route::put('{user}/Reset', [RolesController::class, 'resetPassword'])->name('users.reset.password');

        //school bus management
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
        Route::post('Update/transport/batch', [TransportController::class, 'transferStudentBus'])->name('update.transport.batch');

        //roles and permission management
        Route::get('Update-roles', [RolesController::class, 'updateRoles'])->name('roles.updateRole');
        Route::get('{user}/Assign-role', [RolesController::class, 'assignRole'])->name('roles.assign');
        Route::put('{user}/Update-role', [RolesController::class, 'AssignNewRole'])->name('roles.assign.new');

        Route::get('/roles/confirmation', function () {
            // dd(session()->all()); // Itaonyesha session zote
            if (!session()->has('confirm_role_change')) {
                return redirect()->route('roles.updateRole');
            }
            return view('Roles.confirm');
        })->name('roles.confirmation');

        Route::get('/roles/cancel-confirmation', function () {
            session()->forget('confirm_role_change'); // Clear session when canceling
            return redirect()->route('roles.updateRole');
        })->name('roles.cancelConfirmation');
        Route::post('/roles/confirmProceed', [RolesController::class, 'confirmProceed'])->name('roles.confirmProceed');

        // contracts and legals management
        Route::get('Contracts-management', [ContractController::class, 'contractManager'])->name('contract.management');
        Route::get('{id}/Admin-preview-file', [ContractController::class, 'adminPreviewFile'])->name('contract.admin.preview');
        Route::put('{id}/Approve-contract', [ContractController::class, 'approveContract'])->name('contract.approval');
        Route::put('{id}/Reject-contract', [ContractController::class, 'rejectContract'])->name('contract.rejection');
        Route::get('year/{year}/Contracts-group', [ContractController::class, 'contractByMonths'])->name('contract.by.months');
        Route::get('year/{year}/month/{month}/All-approved-contract', [ContractController::class, 'getAllApprovedContract'])->name('contract.approved.all');
    });

    //3. ROUTE ACCESS FOR EITHER HEAD TEACHER OR ACADEMIC ONLY ============================================================================
    Route::middleware(['CheckUsertype:3', 'CheckRoleType:2,3'])->group(function () {
        // classes management
        Route::post('Register-class', [ClassesController::class, 'registerClass'])->name('Classes.store');
        Route::get('{id}/Edit-class', [ClassesController::class, 'editClass'])->name('Classes.edit');
        Route::put('{id}/Update-class', [ClassesController::class, 'updateClass'])->name('Classes.update');
        Route::delete('{id}/Delete-class', [ClassesController::class, 'deleteClass'])->name('Classes.destroy');

        // roles assignment management
        Route::get('{teacher}/Edit', [RolesController::class, 'edit'])->name('roles.edit');
        Route::put('{classTeacher}/Update-class-teacher', [RolesController::class, 'update'])->name('roles.update.class.teacher');
        Route::get('{teacher}/Delete', [RolesController::class, 'destroy'])->name('roles.destroy');
        Route::post('{classes}/Assign-Teacher', [RolesController::class, 'store'])->name('Class.teacher.assign');

        //timetable settings ********************************
        Route::get('/timetable/settings', [TimetableController::class, 'showSettingsForm'])->name('timetable.settings');
        Route::post('/timetable/settings', [TimetableController::class, 'storeSettings'])->name('timetable.settings.store');

        Route::get('/timetable/generate', [TimetableController::class, 'showGenerator'])->name('timetable.generator');
        Route::post('/timetable/generate', [TimetableController::class, 'generateTimetable'])->name('timetable.generate');
        Route::delete('{timetable}/timtable/delete/settings', [TimetableController::class, 'deleteTimetable'])->name('timetable.delete.settings');

        // examination management
        Route::post('/Examination-type/Register', [ExamController::class, 'store'])->name('exams.store');
        Route::get('{exam}/Examination-type/Delete', [ExamController::class, 'destroy'])->name('exams.destroy');
        Route::put('{exam}/Examination-type/Block', [ExamController::class, 'blockExams'])->name('exams.block');
        Route::put('{exam}/Examination-type/Unblock', [ExamController::class, 'unblockExams'])->name('exams.unblock');
        Route::get('{exam}/Examination-type/Edit', [ExamController::class, 'edit'])->name('exams.type.edit');
        Route::put('{exams}/Examination-type/Update', [ExamController::class, 'update'])->name('exams.update');

        //results management
        Route::put('Publish-results/school/{school}/year/{year}/class/{class}/examType/{examType}/month/{month}/date/{date}', [ResultsController::class, 'publishResult'])->name('publish.results');
        Route::put('Unpublish-results/school/{school}/year/{year}/class/{class}/examType/{examType}/month/{month}/date/{date}', [ResultsController::class, 'unpublishResult'])->name('unpublish.results');
        Route::get('Delete-results/school/{school}/year/{year}/class/{class}/examType/{examType}/month/{month}/date/{date}', [ResultsController::class, 'deleteResults'])->name('delete.results');
        Route::post('Submit-compiled-results/school/{school}/year/{year}/class/{class}', [ResultsController::class, 'saveCompiledResults'])->name('submit.compiled.results');
        Route::post('/update-score', [ResultsController::class, 'updateScore'])->name('update.score');

        // courses and class courses management
        Route::get('{id}/Delete-course', [CoursesController::class, 'deleteCourse'])->name('courses.delete');
        Route::put('{id}/Block', [CoursesController::class, 'blockCourse'])->name('courses.block');
        Route::put('{id}/Unblock', [CoursesController::class, 'unblockCourse'])->name('courses.unblock');
        Route::get('{id}/Assign-teacher', [CoursesController::class, 'assign'])->name('courses.assign');
        Route::put('{id}/Assigned-teacher', [CoursesController::class, 'assignedTeacher'])->name('courses.assigned.teacher');
        Route::put('{id}/Block-assigned-class-course', [CoursesController::class, 'blockAssignedCourse'])->name('block.assigned.course');
        Route::put('{id}/Unblock-assigned-class-course', [CoursesController::class, 'unblockAssignedCourse'])->name('unblock.assigned.course');

        // courses and class courses management
        Route::post('Register-courses', [CoursesController::class, 'addCourse'])->name('course.registration');
        Route::get('{id}/Edit-courses', [CoursesController::class, 'editCourse'])->name('course.edit');
        Route::put('{id}/Update-courses', [CoursesController::class, 'updateCourse'])->name('course.update');
        Route::post('Assign-class-course', [CoursesController::class, 'assignClassCourse'])->name('course.assign');

        //holiday packages management
        Route::post('Upload/package', [PackagesController::class, 'uploadPackage'])->name('package.upload');
        Route::get('Delete/package/id/{id}', [PackagesController::class, 'deletePackage'])->name('delete.holiday.package');
        Route::put('Activate/package/id/{id}', [PackagesController::class, 'activatePackage'])->name('activate.holiday.package');
        Route::put('Deactivate/package/id/{id}', [PackagesController::class, 'deactivatePackage'])->name('deactivate.holiday.package');

        //head teacher or academic report access daily report
        Route::get('Daily-school-report', [TodRosterController::class, 'getSchoolReport'])->name('get.school.report');
        Route::get('Daily-school-report/date/{date}', [TodRosterController::class, 'reportByDate'])->name('report.by.date');
        Route::delete('Daily-school-report/{date}/delete', [TodRosterController::class, 'destroyReport'])->name('report.reject');
        Route::put('Daily-school-report/{id}/update', [TodRosterController::class, 'updateDailyReport'])->name('report.update');
        Route::get('Daily-school-report/view', [TodRosterController::class, 'viewReport'])->name('report.fetch.preview');
    });

    //4. ROUTES ACCESS FOR ALL USERS ========================================================================================================
    Route::middleware('CheckUsertype:1,2,3,4')->group(function () {
        // Dashboard redirection
        Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
        Route::get('Change-password', [HomeController::class, 'changepassword'])->name('change.password');
        Route::post('Change-password', [HomeController::class, 'storePassword'])->name('change.new.password');
        Route::get('Personal-details', [HomeController::class, 'showProfile'])->name('show.profile');
        Route::put('{user}/Personal-details', [HomeController::class, 'updateProfile'])->name('update.profile');
        Route::get('/student-profile-picture/{student}', [StudentsController::class, 'downloadProfilePicture'])->name('student.profile.picture');
    });

    // 5. ROUTE ACCESS FOR PARENTS ONLY ===================================================================================================
    Route::middleware(['CheckUsertype:4'])->group(function () {
        // student registration by parent/guardian himself
        Route::get('Register-student', [StudentsController::class, 'parentByStudent'])->name('parent.student.registration');
        Route::post('Register-students', [StudentsController::class, 'registerStudent'])->name('register.student');

        // student profile management
        Route::get('{student}/My-children', [StudentsController::class, 'showMyChildren'])->name('parent.show.student');
        Route::get('Edit-my-children/{students}', [StudentsController::class, 'editMyStudent'])->name('parent.edit.student');
        Route::put('{students}/Update-my-children', [StudentsController::class, 'updateMyChildren'])->name('parent.update.student');

        // attendance management
        Route::get('Student-attendance/{student}/{year}', [AttendanceController::class, 'show'])->name('students.show.attendance');
        Route::get('{student}/Attendance-year', [AttendanceController::class, 'attendanceYear'])->name('attendance.byYear');

        // results management
        Route::get('Exam-results/Student/{student}', [ResultsController::class, 'index'])->name('results.index');
        Route::get('Result-type/Student/{student}/Year/{year}', [ResultsController::class, 'resultByType'])->name('result.byType');
        Route::get('Result-months/Student/{student}/Year/{year}/Type/{exam_type}', [ResultsController::class, 'resultByMonth'])->name('result.byMonth');
        Route::get('Result/student/{student}/year/{year}/exam-type/{exam_id}/month/{month}/date/{date}', [ResultsController::class, 'viewStudentResult'])->name('results.student.get');
        Route::get('student/{student}/Courses-list', [CoursesController::class, 'viewStudentCourses'])->name('student.courses.list');
        Route::get('/Student-report/school/{school}/year/{year}/class/{class}/report/{report}/student/{student}', [ResultsController::class, 'parentDownloadStudentCombinedReport'])->name('student.combined.report');
        Route::get('Student/view/id/{student}', [StudentsController::class, 'studentProfile'])->name('students.profile');

        //parent download holiday package
        Route::get('/download/package/id/{id}', [PackagesController::class, 'parentDownloadPackage'])->name('student.holiday.package');
    });

    // 6. ROUTE ACCESS FOR CLASS TEACHER ONLY ============================================================================================
    Route::middleware(['CheckUsertype:3'])->group(function() {
        Route::middleware('CheckRoleType:4')->group(function () {
            Route::get('{class}/Student-list', [AttendanceController::class, 'index'])->name('get.student.list');
            Route::post('{student_class}/Create-Attendance', [AttendanceController::class, 'store'])->name('store.attendance');
            Route::get('{class}/Class-attendance', [AttendanceController::class, 'teacherAttendance'])->name('teachers.show.attendance');
            Route::get('{class}/Download-Attendance-PDF', [AttendanceController::class, 'downloadAttendancePDF'])->name('download.attendance.pdf');
            Route::get('{class}/Generate-attendance-report', [AttendanceController::class, 'getFormReport'])->name('attendance.get.form');
            Route::post('{classTeacher}/Generate-attendance-report', [AttendanceController::class, 'generateReport'])->name('attendance.generate.report');
            Route::get('Student/profile/id/{student}', [StudentsController::class, 'classTeacherStudentProfile'])->name('class.teacher.student.profile');
        });
    });

    // 7. ROUTE ACCESS FOR TEACHER WITH TEACHING SUBJECTS=================================================================================
    Route::middleware(['CheckUsertype:3', 'CheckRoleType:1,2,3,4'])->group(function() {
        // Insert examination score
        Route::get('{id}/Prepare', [ExamController::class, 'prepare'])->name('score.prepare.form');
        Route::post('Examination-result-create', [ExamController::class, 'captureValues'])->name('score.captured.values');
        Route::post('Upload/results', [ExamController::class, 'storeScore'])->name('exams.store.score');
        Route::get('/Results-saved/course/{course}/teacher/{teacher}/school/{school}/class/{class}/style/{style}/term/{term}/type/{type}/date/{date}', [ExamController::class, 'continuePendingResults'])->name('form.saved.values');
        Route::get('Results/confirmation', function() {
            return view('Examinations.confirm_results');
        })->name('results.confirm');
        Route::post('/results/edit-draft', [ExamController::class, 'editDraft'])->name('results.edit.draft');
        Route::post('/results/update-draft', [ExamController::class, 'updateDraftResults'])->name('results.update.draft');
        Route::get('/Delete/draft-results/course/{course}/teacher/{teacher}/type/{type}/class/{class}/date/{date}', [ExamController::class, 'deleteDraftResults'])->name('results.draft.delete');

        //teachers  examination results =============================
        Route::get('course/{id}/Results', [ExamController::class, 'courseResults'])->name('results_byCourse');
        Route::get('/Results/course/{course}/&year/{year}', [ExamController::class, 'resultByYear'])->name('results.byYear');
        Route::get('/Results/course/{course}/&year/{year}/&examination/{examType}', [ExamController::class, 'resultByExamType'])->name('results.byExamType');
        Route::get('/Results/course/{course}/&year/{year}/&examination/{examType}/&month/{month}/&date/{date}', [ExamController::class, 'resultByMonth'])->name('results.byMonth');
        Route::get('/Results/delete/course/{course}/&year/{year}/&examination/{examType}/&month/{month}/&date/{date}', [ExamController::class, 'TeacherDeleteResults'])->name('results.delete.byTeacher');

        // contracts and legals management
        Route::get('Contract-application', [ContractController::class, 'index'])->name('contract.index');
        Route::post('Contract-submission', [ContractController::class, 'store'])->name('contract.store');
        Route::get('{id}/Preview-application', [ContractController::class, 'previewMyApplication'])->name('preview.my.application');
        Route::get('{id}/Edit-contract-application', [ContractController::class, 'edit'])->name('contract.edit');
        Route::put('{id}/Update-contract-application', [ContractController::class, 'update'])->name('contract.update');
        Route::get('{id}/Delete-contract-application', [ContractController::class, 'destroy'])->name('contract.destroy');
        Route::get('{id}/Download-approved-contract', [ContractController::class, 'downloadContract'])->name('contract.download');
    });

    // 8. ROUTES ACCESS FOR ERROR PAGE REDIRECTION =======================================================================================
    Route::get('Error', [UsersController::class, 'errorPage'])->name('error.page');
    Route::get('Construction-page', [UsersController::class, 'constructionPage'])->name('under.construction.page');

    // 9. ROUTES ACCESS FOR LOGOUT AND REDIRECTION =======================================================================================
    Route::post('Logout', function () {
        Auth::logout();
        Alert()->toast('Goodbyee see you back later 👋', 'success');
        return redirect()->route('login');
    })->name('logout');

    Route::get('Teachers-roster', [TodRosterController::class, 'index'])->name('tod.roster.index');
    Route::post('Submit-tod-roster', [TodRosterController::class, 'assignTeachers'])->name('tod.roster.store');
    Route::delete('Delete-tod-roster/{id}', [TodRosterController::class, 'destroy'])->name('tod.roster.destroy');
    Route::put('Activate-tod-roster/{id}', [TodRosterController::class, 'activate'])->name('tod.roster.activate');

    // teacher to fill their report for the day
    Route::get('Daily-school-report/create', [TodRosterController::class, 'create'])->name('tod.report.create');
    Route::get('/api/attendance/fetch', [TodRosterController::class, 'fetchAttendance']);
    Route::post('Daily-school-report', [TodRosterController::class, 'store'])->name('tod.report.store');
});
