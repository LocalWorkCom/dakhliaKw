<?php

use App\Http\Controllers\dashboard\IoTelegramController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\outgoingController;
use App\Http\Controllers\dashboard\VacationController;

use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RuleController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\UserController;

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\PostmanController;
use App\Http\Controllers\settingController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\qualificationController;
use App\Http\Controllers\regionsController;

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


// Route::middleware('auth')->group(function () {
//     Route::get('/dashboard', function () {
//         // Matches /admin/dashboard URL
//     });

// });


Route::get('/login', function () {
    return view('login');
});
Route::post('/login', [UserController::class, 'login'])->name('login');
Route::any('/logout', [UserController::class, 'logout'])->name('logout');
Route::post('/verfication_code', [UserController::class, 'verfication_code'])->name('verfication_code');
Route::post('/resend_code', [UserController::class, 'resend_code'])->name('resend_code');

Route::get('/forget-password', function () {
    return view('forgetpassword');
})->name('forget_password');

Route::any('/forget_password2', [UserController::class, 'forget_password2'])->name('forget_password2');
Route::any('/reset_password', [UserController::class, 'reset_password'])->name('reset_password');


//  Auth verfication_code
Route::middleware(['auth'])->group(function () {

    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/users/{id}', [UserController::class, 'index'])->name('user.index')->middleware('check.permission:view User');
    Route::get('api/users/{id}', [UserController::class, 'getUsers'])->name('api.users')->middleware('check.permission:view User');
    Route::get('/users_create/{id}', [UserController::class, 'create'])->name('user.create')->middleware('check.permission:create User');
    Route::post('/store', [UserController::class, 'store'])->name('user.store')->middleware('check.permission:create User');

    Route::get('/employees/{id}', [UserController::class, 'index'])->name('user.employees')->middleware('check.permission:view User');
    Route::get('/edit/{id}', [UserController::class, 'edit'])->name('user.edit')->middleware('check.permission:edit User');
    Route::get('/show/{id}', [UserController::class, 'show'])->name('user.show')->middleware('check.permission:view User');
    Route::post('/update/{id}', [UserController::class, 'update'])->name('user.update')->middleware('check.permission:edit User');
    Route::any('/unsigned/{id}', [UserController::class, 'unsigned'])->name('user.unsigned');

    // permission
    Route::any('/permission', [PermissionController::class, 'index'])->name('permission.index')->middleware('check.permission:view Permission');
    Route::get('api/permission', [PermissionController::class, 'getPermision'])->name('api.permission')->middleware('check.permission:view Permission');
    Route::any('/permission_create', [PermissionController::class, 'create'])->name('permission.create')->middleware('check.permission:create Permission');
    Route::any('/permission_edit/{id}', [PermissionController::class, 'edit'])->name('permissions_edit')->middleware('check.permission:edit Permission');
    Route::any('/permission_show/{id}', [PermissionController::class, 'show'])->name('permissions_show')->middleware('check.permission:edit Permission');
    Route::any('/permission_store', [PermissionController::class, 'store'])->name('permission.store')->middleware('check.permission:edit Permission');
    Route::any('/permission_delete/{id}', [PermissionController::class, 'destroy'])->name('permissions_destroy')->middleware('check.permission:delete Permission');


    // rule
    Route::any('/rule', [RuleController::class, 'index'])->name('rule.index') ->middleware('check.permission:view Rule');
    Route::any('api/rule', [RuleController::class, 'getRule'])->name('api.rule')->middleware('check.permission:view Rule');
    Route::any('/rule_create', [RuleController::class, 'create'])->name('rule.create')->middleware('check.permission:create Rule');
    Route::any('/rule_store', [RuleController::class, 'store'])->name('rule.store')->middleware('check.permission:edit Rule');
    Route::any('/rule_edit/{id}', [RuleController::class, 'edit'])->name('rule_edit')->middleware('check.permission:edit Rule');
    Route::any('/rule_show/{id}', [RuleController::class, 'show'])->name('rule_show')->middleware('check.permission:edit Rule');
    Route::any('/rule_update/{id}', [RuleController::class, 'update'])->name('rule_update')->middleware('check.permission:edit Rule');

    // export
    //Start Export routes
        Route::get('/export/all', [outgoingController::class, 'index'])->name('Export.index')->middleware('check.permission:view outgoings');
        Route::get('/export/{id}/edit', [outgoingController::class, 'edit'])->name('Export.edit')->middleware('check.permission:edit outgoings');
        Route::get('/export/{id}/show', [outgoingController::class, 'show'])->name('Export.show')->middleware('check.permission:view outgoings');
        Route::post('/export/{id}', [outgoingController::class, 'update'])->name('Export.update')->middleware('check.permission:edit outgoings');
        Route::get('/export/create', [outgoingController::class, 'create'])->name('Export.create')->middleware('check.permission:create outgoings');
        Route::post('/export', [outgoingController::class, 'store'])->name('Export.store')->middleware('check.permission:edit outgoings');

        Route::get('/export/All/Archive', [outgoingController::class, 'getExportInActive'])->name('Export.view.archive')->middleware('check.permission:archive outgoings');
        Route::get('exports/get/active', [outgoingController::class, 'getExportActive'])->name('exports.view.all')->middleware('check.permission:view outgoings');
        Route::post('export/archive/add', [outgoingController::class, 'addToArchive'])->name('export.archive.add')->middleware('check.permission:add_archive outgoings');
        Route::get('export/AllArchives', [outgoingController::class, 'showArchive'])->name('Export.AllArchive')->middleware('check.permission:archive outgoings');
        //external users
        Route::get('external/users', [outgoingController::class, 'getExternalUsersAjax'])->name('external.users')->middleware('check.permission:view exportuser');
        Route::post('exportuser/ajax', [outgoingController::class, 'addUaersAjax'])->name('userexport.ajax')->middleware('check.permission:edit exportuser');
        //outgingfiles
        Route::get('export/{id}/upload', [outgoingController::class, 'uploadFiles'])->name('Export.upload.files')->middleware('check.permission:edit outgoing_files');
        Route::get('export/{id}/vieFiles', [outgoingController::class, 'showFiles'])->name('Export.view.files')->middleware('check.permission:view outgoing_files');
        // Route::post('/testUpload', [outgoingController::class, 'testUpload'])->name('testUpload')->middleware('check.permission:view ExternalDepartment');
        Route::get('/downlaodfile/{id}', [outgoingController::class, 'downlaodfile'])->name('downlaodfile')->middleware('check.permission:download outgoing_files');
        //End Export routes

        Route::get('generateNumber/{counter}', [outgoingController::class, 'generateUniqueNumber']);
        Route::get('getLatest', [outgoingController::class, 'getTheLatestExport']);

    
    // getDepartment
        Route::get('api/department', [DepartmentController::class, 'getDepartment'])->name('api.department')->middleware('check.permission:view departements');
        Route::get('api/sub_department', [DepartmentController::class, 'getSub_Department'])->name('api.sub_department')->middleware('check.permission:view departements');
        Route::get('/sub_departments', [DepartmentController::class, 'index_1'])->name('sub_departments.index')->middleware('check.permission:view departements');
        Route::get('/sub_departments/create', [DepartmentController::class, 'create_1'])->name('sub_departments.create')->middleware('check.permission:create departements');
        Route::post('/sub_departments', [DepartmentController::class, 'store_1'])->name('sub_departments.store')->middleware('check.permission:edit departements');
        Route::get('/sub_departments/{department}/edit', [DepartmentController::class, 'edit_1'])->name('sub_departments.edit')->middleware('check.permission:edit departements');
        Route::put('/sub_departments/{department}', [DepartmentController::class, 'update_1'])->name('sub_departments.update')->middleware('check.permission:edit departements');
        // Route::post('departments_store', [DepartmentController::class, 'store'])->middleware('check.permission:view departements');
        // Route::put('departments_update/{department}', [DepartmentController::class, 'update']);
        // Route::delete('departments_delete/{department}', [DepartmentController::class, 'destroy']);
        Route::get('/departments', [DepartmentController::class, 'index'])->name('departments.index')->middleware('check.permission:view departements');
        Route::get('/departments/create', [DepartmentController::class, 'create'])->name('departments.create')->middleware('check.permission:create departements');
        Route::get('/departments/show/{department}', [DepartmentController::class, 'show'])->name('departments.show')->middleware('check.permission:view departements');
        Route::post('/departments', [DepartmentController::class, 'store'])->name('departments.store')->middleware('check.permission:edit departements');
        Route::get('/departments/{department}/edit', [DepartmentController::class, 'edit'])->name('departments.edit')->middleware('check.permission:edit departements');
        Route::put('/departments/{department}', [DepartmentController::class, 'update'])->name('departments.update')->middleware('check.permission:edit departements');
        Route::get('departments/delete/{department}', [DepartmentController::class, 'destroy'])->name('departments.destroy')->middleware('check.permission:delete departements');;
        // Route::resource('postmans', PostmanController::class);
        Route::get('/postmans/create', [PostmanController::class, 'create'])->name('postmans.create')->middleware('check.permission:create Postman');
        Route::post('/postmans', [PostmanController::class, 'store'])->name('postmans.store')->middleware('check.permission:edit Postman');
        Route::get('/postmans/{postman}/edit', [PostmanController::class, 'edit'])->name('postmans.edit')->middleware('check.permission:edit Postman');
        Route::put('/postmans/{postman}', [PostmanController::class, 'update'])->name('postmans.update')->middleware('check.permission:edit Postman');

    //start jobs
        Route::get('setting/jobs', [settingController::class, 'getAlljob'])->name('setting.getAlljob')->middleware('check.permission:view job');
        Route::get('setting/jobs/all', [settingController::class, 'indexjob'])->name('job.index')->middleware('check.permission:view job');
        Route::post('setting/jobs/add', [settingController::class, 'addjob'])->name('job.add')->middleware('check.permission:edit job');
        //Route::get('setting/jobs/create', [settingController::class,'createjob'])->name('job.create');
        Route::post('setting/jobs/update', [settingController::class, 'updatejob'])->name('job.update')->middleware('check.permission:edit job');
        //Route::get('setting/jobs/edit/{id}', [settingController::class,'editjob'])->name('job.edit');
        Route::get('setting/jobs/show/{id}', [settingController::class, 'showjob'])->name('job.show')->middleware('check.permission:view job');
        Route::post('setting/jobs/delete', [settingController::class, 'deletejob'])->name('job.delete')->middleware('check.permission:delete job');
    //end jobs
    //start vacation type
        Route::get('setting/vacationType', [settingController::class, 'getAllvacationType'])->name('setting.getAllvacationType')->middleware('check.permission:view VacationType');
        Route::get('setting/vacationType/all', [settingController::class, 'indexvacationType'])->name('vacationType.index')->middleware('check.permission:view VacationType');
        Route::post('setting/vacationType/add', [settingController::class, 'addvacationType'])->name('vacationType.add')->middleware('check.permission:create VacationType');
        //Route::get('setting/vacationType/create', [settingController::class,'createvacationType'])->name('vacationType.create');
        Route::post('setting/vacationType/update', [settingController::class, 'updatevacationType'])->name('vacationType.update')->middleware('check.permission:edit VacationType');
        //Route::post('setting/vacationType/edit', [settingController::class,'editvacationType'])->name('vacationType.edit');
        Route::get('setting/vacationType/show/{id}', [settingController::class, 'showvacationType'])->name('vacationType.show')->middleware('check.permission:view VacationType');
        Route::post('setting/vacationType/delete', [settingController::class, 'deletevacationType'])->name('vacationType.delete')->middleware('check.permission:delete VacationType');
    //end vacation type
    //start gards
        Route::get('setting/grads', [settingController::class, 'getAllgrads'])->name('setting.getAllgrads')->middleware('check.permission:view grade');
        Route::get('setting/grads/all', [settingController::class, 'indexgrads'])->name('grads.index')->middleware('check.permission:view grade');
        Route::post('setting/grads/add', [settingController::class, 'addgrads'])->name('grads.add')->middleware('check.permission:edit grade');
        Route::post('setting/grads/update', [settingController::class, 'updategrads'])->name('grads.update')->middleware('check.permission:edit grade');
        Route::get('setting/grads/show/{id}', [settingController::class, 'showgrads'])->name('grads.show')->middleware('check.permission:view grade');
        Route::post('setting/grads/delete', [settingController::class, 'deletegrads'])->name('grads.delete')->middleware('check.permission:delete grade');
    //end grads
    //Start qualifications -------- Need middleware for gard
        Route::get('setting/qualifications/all', [qualificationController::class, 'index'])->name('qualifications.index')->middleware('check.permission:view Qualification');
        Route::get('setting/qualifications/ajax', [qualificationController::class, 'getqualification'])->name('getAllqualification')->middleware('check.permission:view Qualification');
        Route::post('setting/qualifications/create', [qualificationController::class, 'store'])->name('qualification.store')->middleware('check.permission:view Qualification');
        Route::post('setting/qualifications/edit', [qualificationController::class, 'update'])->name('qualification.update')->middleware('check.permission:view Qualification');
        Route::post('setting/qualifications/delete', [qualificationController::class, 'destroy'])->name('qualification.delete')->middleware('check.permission:view Qualification');

    //End qualifications
    //start government
    Route::get('setting/government', [regionsController::class, 'getAllgovernment'])->name('setting.getAllgovernment')->middleware('check.permission:view Government');
    Route::get('setting/government/all', [regionsController::class, 'indexgovernment'])->name('government.all')->middleware('check.permission:view Government');
    Route::post('setting/government/add', [regionsController::class, 'addgovernment'])->name('government.add')->middleware('check.permission:edit Government');
    Route::get('setting/government/create', [regionsController::class, 'creategovernment'])->name('government.create')->middleware('check.permission:create Government');
    Route::post('setting/government/update', [regionsController::class, 'updategovernment'])->name('government.update')->middleware('check.permission:edit Government');
    Route::get('setting/government/edit/{id}', [regionsController::class, 'editgovernment'])->name('government.edit')->middleware('check.permission:edit Government');
    Route::get('setting/government/show/{id}', [regionsController::class, 'showgovernment'])->name('government.show')->middleware('check.permission:view Government');
//endgovernment
    //Start Regions -------- Need middleware for gard
      Route::get('setting/Regions/all/{id}', [regionsController::class, 'index'])->name('regions.index')->middleware('check.permission:view Region');
      Route::get('setting/Regions/ajax', [regionsController::class, 'getregions'])->name('getAllregions')->middleware('check.permission:view Region');
      Route::get('setting/RegionBygovernment', [regionsController::class, 'getregionBygovernment'])->name('getAllregionsBygovernment')->middleware('check.permission:view Region');
      Route::post('setting/Regions/create', [regionsController::class, 'store'])->name('regions.store')->middleware('check.permission:view Region');
      Route::post('setting/Regions/edit', [regionsController::class, 'update'])->name('regions.update')->middleware('check.permission:view Region');
      Route::post('setting/Regions/delete', [regionsController::class, 'destroy'])->name('regions.delete')->middleware('check.permission:view Region');

    //End Regions
    //setting end



    Route::post('postman/ajax', [IoTelegramController::class, 'addPostmanAjax'])->name('postman.ajax')->middleware('check.permission:create Postman');
    Route::get('postmans', [IoTelegramController::class, 'getPostmanAjax'])->name('postman.get')->middleware('check.permission:view Postman');
    Route::post('department/ajax', [IoTelegramController::class, 'addExternalDepartmentAjax'])->name('department.ajax')->middleware('check.permission:create ExternalDepartment');
    Route::get('external/departments', [IoTelegramController::class, 'getExternalDepartments'])->name('external.departments')->middleware('check.permission:view ExternalDepartment');
    Route::get('internal/departments', [IoTelegramController::class, 'getDepartments'])->name('internal.departments')->middleware('check.permission:view departements');
    Route::get('iotelegrams', [IoTelegramController::class, 'index'])->name('iotelegrams.list')->middleware('check.permission:view Iotelegram');
    Route::get('iotelegrams/get/{id?}', [IoTelegramController::class, 'getIotelegrams'])->name('iotelegrams.get')->middleware('check.permission:view Iotelegram');
    Route::get('iotelegram/add', [IoTelegramController::class, 'create'])->name('iotelegrams.add')->middleware('check.permission:create Iotelegram');
    Route::post('iotelegram/store', [IoTelegramController::class, 'store'])->name('iotelegram.store')->middleware('check.permission:edit Iotelegram');
    Route::get('iotelegram/edit/{id}', [IoTelegramController::class, 'edit'])->name('iotelegram.edit')->middleware('check.permission:edit Iotelegram');
    Route::post('iotelegram/update/{id}', [IoTelegramController::class, 'update'])->name('iotelegram.update')->middleware('check.permission:edit Iotelegram');
    Route::get('iotelegram/show/{id}', [IoTelegramController::class, 'show'])->name('iotelegram.show')->middleware('check.permission:view Iotelegram');
    Route::get('iotelegram/archives', [IoTelegramController::class, 'archives'])->name('iotelegram.archives')->middleware('check.permission:archive Iotelegram');
    Route::get('iotelegram/archives/get', [IoTelegramController::class, 'getArchives'])->name('iotelegram.archives.get')->middleware('check.permission:archive Iotelegram');
    Route::get('iotelegram/archive/{id}', [IoTelegramController::class, 'AddArchive'])->name('iotelegram.archive.add')->middleware('check.permission:add_archive Iotelegram');
    Route::get('iotelegram/downlaod/{id}', [IoTelegramController::class, 'downlaodfile'])->name('iotelegram.downlaodfile')->middleware('check.permission:download Iotelegram');

    
    Route::get('vacation/list/{id?}', [VacationController::class, 'index'])->name('vacations.list')->middleware('check.permission:view EmployeeVacation');
    Route::get('vacation/get/{id?}', [VacationController::class, 'getVacations'])->name('employee.vacations')->middleware('check.permission:view EmployeeVacation');
    Route::get('vacation/add/{id?}', [VacationController::class, 'create'])->name('vacation.add')->middleware('check.permission:create EmployeeVacation');
    Route::post('vacation/store/{id?}', [VacationController::class, 'store'])->name('vacation.store')->middleware('check.permission:edit EmployeeVacation');
    Route::get('vacation/edit/{id}', [VacationController::class, 'edit'])->name('vacation.edit')->middleware('check.permission:edit EmployeeVacation');
    Route::post('vacation/update/{id}', [VacationController::class, 'update'])->name('vacation.update')->middleware('check.permission:edit EmployeeVacation');
    Route::get('vacation/show/{id}', [VacationController::class, 'show'])->name('vacation.show')->middleware('check.permission:view EmployeeVacation');
    Route::get('vacation/delete/{id}', [VacationController::class, 'delete'])->name('vacation.delete')->middleware('check.permission:delete EmployeeVacation');
    Route::get('vacation/downlaod/{id}', [VacationController::class, 'downlaodfile'])->name('vacation.downlaodfile')->middleware('check.permission:download EmployeeVacation');
    Route::get('/employees/by-department/{departmentId}', [DepartmentController::class, 'getEmployeesByDepartment']);



    
});




// // view All Models permission
// Route::middleware(['auth', 'check.permission:view Rule,view Permission,view departements'])->group(function () {
// });
// // create All Models permission
// Route::middleware(['auth', 'check.permission:create Permission,create Rule,create departements'])->group(function () {
// });
// // edit All Models permission
// Route::middleware(['auth', 'check.permission:edit Rule,edit Permission,edit departements'])->group(function () {

//     // Route::resource('permissions', PermissionController::class);
//     // Route::resource('rules', RuleController::class);
// });


// //permission
// Route::any('/permission_destroy',[PermissionController::class, 'destroy'])->name('permission.destroy');
// Route::any('/permission_view',[PermissionController::class, 'show'])->name('permission.view');





//role
// Route::any('/rule_destroy',[RuleController::class, 'destroy'])->name('rule.destroy');
// Route::any('/rule_view',[RuleController::class, 'show'])->name('rule.view');

// department
// Route::resource('departments', DepartmentController::class);

// Department routes


//Start Export routes


//End Export routes
//setting start
// Route::resource('setting', settingController::class);
// Route::get('setting', [settingController::class,'index'])->name('setting.index');
// Route::get('setting/all/grade', [settingController::class, 'getAllGrade'])->name('setting.getAllGrade');
// Route::get('setting/all/job', [settingController::class, 'getAllJob'])->name('setting.getAllJob');
// Route::get('setting/all/vacation', [settingController::class, 'getAllVacation'])->name('setting.getAllVacation');
// Route::get('setting/all/government', [settingController::class, 'getAllgovernment'])->name('setting.getAllgovernment');


// Route::post('jobs/add', [settingController::class,'addJob'])->name('jobs.add');
// Route::post('jobs', [settingController::class,'editJob'])->name('jobs.edit');
// Route::post('jobs/delete', [settingController::class,'deletejob'])->name('jobs.delete');


// Route::post('grade/add', [settingController::class,'addgrade'])->name('grade.add');
// Route::post('grade', [settingController::class,'editgrade'])->name('grade.edit');
// Route::post('grade/delete', [settingController::class,'deletegrade'])->name('grade.delete');


// Route::post('vacationType/add', [settingController::class,'addVacation'])->name('vacationType.add');
// Route::post('vacationType', [settingController::class,'editVacation'])->name('vacation.edit');
// Route::post('vacationType/delete', [settingController::class,'deleteVacation'])->name('vacation.delete');


// Route::post('government/add', [settingController::class,'addgovernment'])->name('government.add');
// Route::post('government', [settingController::class,'editgovernment'])->name('government.edit');
// Route::post('government/delete', [settingController::class,'deletegovernment'])->name('government.delete');