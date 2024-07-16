<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DepartmentController;

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
Route::get('/departments', [DepartmentController::class, 'index'])->name('departments.index');

Route::get('/departments/create', [DepartmentController::class, 'create'])->name('departments.create');
Route::post('/departments', [DepartmentController::class, 'store'])->name('departments.store');
Route::get('/departments/{department}/edit', [DepartmentController::class, 'edit'])->name('departments.edit');
Route::put('/departments/{department}', [DepartmentController::class, 'update'])->name('departments.update');
Route::delete('departments/{department}/delete', [DepartmentController::class, 'destroy'])->name('departments.destroy');

