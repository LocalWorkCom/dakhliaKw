<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\User;
use App\Models\grade;
use App\Models\GroupTeam;
use App\Models\Inspector;
use App\Models\Violation;
use Illuminate\Http\Request;
use App\Models\instantmission;
use App\Models\ViolationTypes;
use App\Models\InspectorMission;
use App\Http\Controllers\Controller;
use App\Models\EmployeeVacation;
use App\Models\Point;
use App\Models\PointDays;
use App\Models\Setting;
use App\Models\VacationType;
use App\Models\WorkingTime;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


class VacationController  extends Controller
{
    function getCountries()
    {
        $countries = getCountries();
        $success['countries'] = $countries;

        return $this->respondSuccess($success, 'Get Data successfully.');
    }
    function getVacationTypes()
    {
        $vacation_types = VacationType::where('flag', 1)->get();
        $success['vacation_types'] = $vacation_types;

        return $this->respondSuccess($success, 'Get Data successfully.');
    }
    function requestVacation(Request $request)
    {

        $rules = [
            'vacation_type_id' => 'required',
            'start_date' => 'required|date',
            'days_num' => 'required|integer|min:1', // Added validation for days_num
        ];

        if ($request->has('check_country')) {
            $rules['country_id'] = 'required';
        }


        $messages = [
            'vacation_type_id.required' => 'يجب ادخال نوع الاجازة',
            'start_date.required' => 'يجب ادخال تاريخ البداية',
            'days_num.required' => 'يجب ادخال عدد الأيام', // Added custom message for days_num
            'country_id.required' => 'يجب اختيار دولة عند تحديد دولة خارجية',
        ];

        $validatedData = Validator::make($request->all(), $rules, $messages);


        if ($validatedData->fails()) {
            return $this->respondError('Validation Error.', $validatedData->errors(), 400);
        }
        $check_vacation = EmployeeVacation::where('employee_id', auth()->user()->id)->get();
        // pending
        foreach ($check_vacation as $value) {
            if ($value->status == 'Pending') {
                $ExpectedEndDate = ExpectedEndDate($value)[0];

                if ($ExpectedEndDate >= $request->start_date && $value->start_date <= $request->start_date) {
                    return $this->respondError('Duplicate vacation ', ['error' => 'يوجد اجازة اخرى بنفس تاريخ البداية أو في نطاق التواريخ لنفس الموظف'], 403);

                    // return redirect()->route('vacation.add', $id)->withErrors(['يوجد اجازة اخرى بنفس تاريخ البداية أو في نطاق التواريخ لنفس الموظف']);
                }
            } elseif ($value->status != 'Rejected' && $value->end_date) {
                if ($value->end_date <= $request->start_date && $value->start_date <= $request->start_date) {
                    return $this->respondError('Duplicate vacation ', ['error' => 'يوجد اجازة اخرى بنفس تاريخ البداية أو في نطاق التواريخ لنفس الموظف'], 403);

                    // return redirect()->route('vacation.add', $id)->withErrors(['يوجد اجازة اخرى بنفس تاريخ البداية أو في نطاق التواريخ لنفس الموظف']);
                }
            }
            //not rejected
            elseif ($value->status != 'Rejected' && !$value->end_date) {
                $currentDate = date('Y-m-d');
                $ExpectedEndDate = ExpectedEndDate($value)[0];

                if ($currentDate <= $request->start_date && $value->start_date <= $request->start_date && $ExpectedEndDate >= $request->start_date) {
                    return $this->respondError('Duplicate vacation', ['error' => 'يوجد اجازة اخرى بنفس تاريخ البداية أو في نطاق التواريخ لنفس الموظف'], 403);
                }
            }
        }


        $Vacation = new EmployeeVacation;
        $Vacation->employee_id =  auth()->user()->id;
        $Vacation->created_by = auth()->id();
        $Vacation->vacation_type_id  = $request->vacation_type_id;
        $Vacation->country_id = $request->country_id;
        $Vacation->days_number = $request->days_num;
        $Vacation->start_date = $request->start_date;
        $Vacation->save();

        if ($request->hasFile('reportImage')) {
            $file = $request->reportImage;
            $path = 'vacations/employee';

            UploadFiles($path, 'report_image', 'report_image_real', $Vacation, $file);
        }
        return $this->respondSuccess(json_decode('{}'), 'تم الحفظ بنجاح');
    }
    function getAllVacations(Request $request)
    {
        $PendingVacations = EmployeeVacation::where('status', $request->status)->where('employee_id', auth()->user()->id)->get();
        $AcceptedVacations = EmployeeVacation::where('status', $request->status)->where('employee_id', auth()->user()->id)->get();
        $RejectedVacations = EmployeeVacation::where('status', $request->status)->where('employee_id', auth()->user()->id)->get();
      
        $success['PendingVacations'] = $PendingVacations;
        $success['AcceptedVacations'] = $AcceptedVacations;
        $success['RejectedVacations'] = $RejectedVacations;
        

        return $this->respondSuccess($success, 'Get Data successfully.');
    }
}
