<?php

use App\Http\Controllers\dashboard\IoTelegramController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\outgoingController;

use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RuleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\PostmanController;


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

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::get('/login', function () {
    return view('login');
});



//  Auth verfication_code
Route::post('/create', [UserController::class, 'store'])->name('create');
Route::post('/login', [UserController::class, 'login'])->name('login');
Route::any('/logout', [UserController::class, 'logout'])->name('logout');
Route::post('/verfication_code', [UserController::class, 'verfication_code'])->name('verfication_code');
Route::post('/resend_code', [UserController::class, 'resend_code'])->name('resend_code');

Route::get('/forget-password', function () {
    return view('forgetpassword');
})->name('forget_password');

Route::any('/forget_password2', [UserController::class, 'forget_password2'])->name('forget_password2');
Route::any('/reset_password', [UserController::class, 'reset_password'])->name('reset_password');


// view All Models permission
Route::middleware(['auth', 'check.permission:view Rule,view Permission,view departements'])->group(function () {
    Route::any('/permission', [PermissionController::class, 'index'])->name('permission.index');
    Route::any('/permission_create', [PermissionController::class, 'create'])->name('permission.create');

    Route::any('/rule', [RuleController::class, 'index'])->name('rule.index');
    Route::any('/rule_create',[RuleController::class, 'create'])->name('rule.create');

});
// create All Models permission
Route::middleware(['auth', 'check.permission:create Permission,create Rule,create departements'])->group(function () {
    Route::any('/permission_store', [PermissionController::class, 'store'])->name('permission.store');
    Route::any('/rule_store',[RuleController::class, 'store'])->name('rule.store');
});
// edit All Models permission
Route::middleware(['auth', 'check.permission:edit Rule,edit Permission,edit departements'])->group(function () {
    Route::any('/permission_edit/{id}', [PermissionController::class, 'edit'])->name('permissions_edit');
    Route::any('/rule_edit/{id}',[RuleController::class, 'edit'])->name('rule_edit');
    Route::any('/rule_update/{id}',[RuleController::class, 'update'])->name('rule_update');
    // Route::resource('permissions', PermissionController::class);
    // Route::resource('rules', RuleController::class);
});

Route::get('/sub_departments', [DepartmentController::class, 'index_1'])->name('sub_departments.index');
Route::get('/sub_departments/create', [DepartmentController::class, 'create_1'])->name('sub_departments.create');
    Route::post('/sub_departments', [DepartmentController::class, 'store_1'])->name('sub_departments.store');
// //permission
    // Route::any('/permission_destroy',[PermissionController::class, 'destroy'])->name('permission.destroy');
    // Route::any('/permission_view',[PermissionController::class, 'show'])->name('permission.view');





//role
    // Route::any('/rule_destroy',[RuleController::class, 'destroy'])->name('rule.destroy');
    // Route::any('/rule_view',[RuleController::class, 'show'])->name('rule.view');

// department
    // Route::resource('departments', DepartmentController::class);
    Route::post('departments_store', [DepartmentController::class, 'store']);
    Route::put('departments_update/{department}', [DepartmentController::class, 'update']);
    Route::delete('departments_delete/{department}', [DepartmentController::class, 'destroy']);
    // Department routes
    Route::get('/departments', [DepartmentController::class, 'index'])->name('departments.index');

    Route::get('/departments/create', [DepartmentController::class, 'create'])->name('departments.create');
    Route::get('/departments/show/{department}', [DepartmentController::class, 'show'])->name('departments.show');
    Route::post('/departments', [DepartmentController::class, 'store'])->name('departments.store');
    Route::get('/departments/{department}/edit', [DepartmentController::class, 'edit'])->name('departments.edit');
    Route::put('/departments/{department}', [DepartmentController::class, 'update'])->name('departments.update');
    Route::delete('departments/{department}/delete', [DepartmentController::class, 'destroy'])->name('departments.destroy');

//Start Export routes
Route::resource('Export', outgoingController::class);
Route::get('/Export/All', [outgoingController::class, 'outgoingAll'])->name('Export.view.all');
Route::get('/Export/{id}/upload', [outgoingController::class, 'uploadFiles'])->name('Export.upload.files');
Route::get('/Export/{id}/vieFiles', [outgoingController::class, 'showFiles'])->name('Export.view.files');
Route::post('exportuser/ajax', [outgoingController::class, 'addUaersAjax'])->name('userexport.ajax');
Route::get('external/users', [outgoingController::class, 'getExternalUsersAjax'])->name('external.users');

Route::post('/testUpload', [outgoingController::class, 'testUpload'])->name('testUpload');
Route::get('/downlaodfile/{id}', [outgoingController::class, 'downlaodfile'])->name('downlaodfile');

//End Export routes


Route::post('postman/ajax', [IoTelegramController::class, 'addPostmanAjax'])->name('postman.ajax');
Route::get('postmans', [IoTelegramController::class, 'getPostmanAjax'])->name('postman.get');
Route::post('department/ajax', [IoTelegramController::class, 'addExternalDepartmentAjax'])->name('department.ajax');
Route::get('external/departments', [IoTelegramController::class, 'getExternalDepartments'])->name('external.departments');
Route::get('internal/departments', [IoTelegramController::class, 'getDepartments'])->name('internal.departments');
Route::get('iotelegrams', [IoTelegramController::class, 'index'])->name('iotelegrams.list');
Route::get('iotelegram/add', [IoTelegramController::class, 'create'])->name('iotelegrams.add');
Route::post('iotelegram/store', [IoTelegramController::class, 'store'])->name('iotelegram.store');
Route::get('iotelegram/edit/{id}', [IoTelegramController::class, 'edit'])->name('iotelegram.edit');
Route::post('iotelegram/update/{id}', [IoTelegramController::class, 'update'])->name('iotelegram.update');
Route::get('iotelegram/show/{id}', [IoTelegramController::class, 'show'])->name('iotelegram.show');
Route::get('iotelegram/archives', [IoTelegramController::class, 'Archives'])->name('iotelegram.archives');
Route::get('iotelegram/archive/{id}', [IoTelegramController::class, 'AddArchive'])->name('iotelegram.archive.add');
Route::get('iotelegram/downlaod/{id}', [IoTelegramController::class, 'downlaodfile'])->name('iotelegram.downlaodfile');








// Route::resource('postmans', PostmanController::class);
Route::get('/postmans/create', [PostmanController::class, 'create'])->name('postmans.create');
Route::post('/postmans', [PostmanController::class, 'store'])->name('postmans.store');
Route::get('/postmans/{postman}/edit', [PostmanController::class, 'edit'])->name('postmans.edit');
Route::put('/postmans/{postman}', [PostmanController::class, 'update'])->name('postmans.update');
