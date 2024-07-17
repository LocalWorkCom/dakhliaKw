<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\outgoingController;

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
});

Route::get('/login', function () {
    return view('login');
});

//  Auth
Route::post('/create', [UserController::class, 'store'])->name('u');
Route::post('/login', [UserController::class, 'login'])->name('login');
// Route::resource('departments', DepartmentController::class);
Route::post('departments_store', [DepartmentController::class, 'store']);
Route::put('departments_update/{department}', [DepartmentController::class, 'update']);
Route::delete('departments_delete/{department}', [DepartmentController::class, 'destroy']);


//Start Export routes
Route::resource('Export', outgoingController::class);
Route::get('/Export/All', [outgoingController::class, 'outgoingAll'])->name('Export.view.all');
Route::get('/Export/{id}/upload', [outgoingController::class, 'uploadFiles'])->name('Export.upload.files');
Route::get('/Export/{id}/vieFiles', [outgoingController::class, 'showFiles'])->name('Export.view.files');



//End outgoing routes