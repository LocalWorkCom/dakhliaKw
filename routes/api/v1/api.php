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

Route::get('/inspector/{inspectorId}/missions', [InspectorMissionController::class, 'getMissionsByInspector']);

