<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\InspectorMissionController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('/login', 'App\Http\Controllers\Api\UserController@login');
Route::post('/resetpassword', 'App\Http\Controllers\Api\UserController@reset_password');

Route::post('/check_military_number', 'App\Http\Controllers\Api\UserController@check_military_number');
Route::any('/Violation_type/{id}', 'App\Http\Controllers\Api\ViolationController@get_Violation_type');
Route::post('/add_Violation', 'App\Http\Controllers\Api\ViolationController@add_Violation');


Route::get('/inspector/{inspectorId}/missions', [InspectorMissionController::class, 'getMissionsByInspector']);


