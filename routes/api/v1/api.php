<?php

use App\Http\Controllers\Api\ApiAbsenceController;
use App\Http\Controllers\Api\personalMissionController;
use App\Http\Controllers\Api\reportsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\Api\InspectorMissionController;
use App\Http\Controllers\Api\paperTransactionController;
use App\Http\Controllers\Api\VacationController;
use App\Http\Controllers\Api\ViolationController;

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

Route::post('/login', 'App\Http\Controllers\Api\UserController@login');
Route::post('/resetpassword', 'App\Http\Controllers\Api\UserController@reset_password');
Route::post('/check_military_number', 'App\Http\Controllers\Api\UserController@check_military_number');

/* Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
}); */

Route::group(['middleware' => 'auth:api'], function () {

    Route::any('/Violation_type', 'App\Http\Controllers\Api\ViolationController@get_Violation_type');
    Route::post('/add_Violation', 'App\Http\Controllers\Api\ViolationController@add_Violation');
    Route::any('/get_voilation_instantMission', 'App\Http\Controllers\Api\ViolationController@get_voilation_instantMission');
    Route::any('/get_all_violation', 'App\Http\Controllers\Api\ViolationController@get_all_violation');
    Route::any('/get_absence', 'App\Http\Controllers\Api\ApiAbsenceController@index');
    Route::post('/add_absence', 'App\Http\Controllers\Api\ApiAbsenceController@store');
    Route::get('/inspector/missions', [InspectorMissionController::class, 'getMissionsByInspector']);
    Route::any('/absenceReport', [reportsController::class, 'getAbsence']);
    Route::any('/reports/inspector', [reportsController::class, 'allReportInspector']);
    Route::any('/reports/inspector/points', [reportsController::class, 'getAllPoints']);
    Route::any('/home/statistics', [reportsController::class, 'getAllstatistics']);
    Route::any('/statistics', [reportsController::class, 'getstatistics']);
    Route::any('/accept/abcense/report', [reportsController::class, 'changeStatus']);

    Route::any('/notification', [reportsController::class, 'getNotifi']);

    Route::post('/inspector/add/mission', [personalMissionController::class, 'addPersonalMission']);
    Route::get('/getAll/points', [personalMissionController::class, 'getAllPoints']);
    Route::get('/grade/{type}', [ViolationController::class, 'getGrade']);


    Route::any('/inspector/update_absence',  [ApiAbsenceController::class, 'update']);
    Route::any('/inspector/update_violation',  [ViolationController::class, 'updateViolation']);

    /**
     * /Lizam
     */
    Route::any('/lizamat', [InspectorMissionController::class, 'get_shift']);
    Route::post('/changepassword', 'App\Http\Controllers\Api\UserController@changePassword');


    //papertransactions
    Route::any('/paper_transactions',  [paperTransactionController::class, 'store']);

    Route::any('/papertransaction/all',  [paperTransactionController::class, 'index']);

    Route::get('/countries', [VacationController::class, 'getCountries']);
    Route::get('/vacation_types', [VacationController::class, 'getVacationTypes']);
    Route::post('/vacation_request', [VacationController::class, 'requestVacation']);
    Route::get('/all_vacations', [VacationController::class, 'getAllVacations']);
});
