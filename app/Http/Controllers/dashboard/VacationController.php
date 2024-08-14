<?php

namespace App\Http\Controllers\dashboard;

use Log;
use App\Models\Country;
use App\Models\Inspector;
use Illuminate\Http\Request;
use App\Models\EmployeeVacation;

use App\Models\InspectorMission;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class VacationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($id = 0)
    {
        return view('vacation.index', compact('id'));
    }
    public function getVacations($id)
    {
        if ($id) {

            $EmployeeVacations = EmployeeVacation::where('employee_id', $id)
                ->with('employee', 'vacation_type')
                ->orderby('created_at', 'desc')
                ->get();
            foreach ($EmployeeVacations as  $EmployeeVacation) {
                # code...
                $EmployeeVacation['StartVacation'] = CheckStartVacationDate($EmployeeVacation->id);
                $EmployeeVacation['VacationStatus'] = GetEmployeeVacationType($EmployeeVacation);
                $EmployeeVacation['EndDate'] = ExpectedEndDate($EmployeeVacation)[0];
                $EmployeeVacation['StartWorkDate'] = ExpectedEndDate($EmployeeVacation)[1];
                $EmployeeVacation['DaysLeft'] = VacationDaysLeft($EmployeeVacation);
            }
            return DataTables::of($EmployeeVacations)

                ->rawColumns(['action'])
                ->make(true);
        } else {
            $EmployeeVacations = EmployeeVacation::with('employee', 'vacation_type')
                ->orderby('created_at', 'desc')
                ->get();
            foreach ($EmployeeVacations as  $EmployeeVacation) {
                $EmployeeVacation['StartVacation'] = CheckStartVacationDate($EmployeeVacation->id);
                $EmployeeVacation['VacationStatus'] = GetEmployeeVacationType($EmployeeVacation);
                $EmployeeVacation['EndDate'] = ExpectedEndDate($EmployeeVacation)[0];
                $EmployeeVacation['StartWorkDate'] = ExpectedEndDate($EmployeeVacation)[1];
                $EmployeeVacation['DaysLeft'] = VacationDaysLeft($EmployeeVacation);
            }
            return DataTables::of($EmployeeVacations)

                ->rawColumns(['action'])
                ->make(true);
        }
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create($id = 0)
    {
        $employees = getEmployees();
        $vacation_types = getVactionTypes();
        if ($id) {

            $vacation_types = getVactionTypes()->where('id', '<>', '3');
        } else {
            $vacation_types = getVactionTypes();
        }
        $countries = getCountries();
        return view('vacation.add', compact('employees', 'vacation_types', 'id', 'countries'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $id)
    {
        $rules = [
            'vacation_type_id' => 'required',
            'start_date' => 'required|date',
            'employee_id' => 'required',
        ];

        $messages = [

            'employee_id.required' => 'يجب عليك اختيار موظف',
            'vacation_type_id.required' => 'يجب ادخال نوع الاجازة',
            'start_date.required' => 'يجب ادخال تاريخ البداية',
        ];
        $validatedData = Validator::make($request->all(), $rules, $messages);

        if ($validatedData->fails()) {
            session()->flash('errors', $validatedData->errors());

            return redirect()->route('vacations.list', $id);
        }

        if ($id == 0) {
            $employee_id = $request->employee_id;
        } else {
            $employee_id = $id;
        }

        $employee_vacation = new EmployeeVacation();
        $employee_vacation->vacation_type_id = $request->vacation_type_id;
        $employee_vacation->start_date = $request->start_date;
        $employee_vacation->days_number = $request->days_num;
        $employee_vacation->country_id = $request->country_id;
        $employee_vacation->employee_id = $employee_id;
        $employee_vacation->created_by = auth()->id();
        $employee_vacation->created_departement = auth()->user()->department_id;
        $employee_vacation->save();

        if ($request->hasFile('reportImage')) {
            $file = $request->reportImage;
            // You can modify the UploadFiles function call according to your needs
            $path = 'vacations/employee';

            UploadFiles($path, 'report_image', 'report_image_real', $employee_vacation, $file);
        }

        session()->flash('success', 'تم الحفظ بنجاح.');

        return redirect()->route('vacations.list', $id);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        //
        $vacation = EmployeeVacation::with('employee', 'vacation_type', 'country')->where('id', $id)->first();
        $employees = getEmployees();
        $vacation_types = getVactionTypes();

        return view('vacation.show', compact('vacation', 'employees', 'vacation_types', 'id'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $employees = getEmployees();
        $vacation = EmployeeVacation::find($id);
        if ($vacation->employee_id) {

            $vacation_types = getVactionTypes()->where('id', '<>', '3');
        } else {
            $vacation_types = getVactionTypes();
        }
        $countries = getCountries();

        return view('vacation.edit', compact('employees', 'vacation', 'vacation_types', 'id', 'countries'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {

        $rules = [
            'vacation_type_id' => 'required',
            'date_from' => 'required|date|before_or_equal:date_to',
            'date_to' => 'required|date|after_or_equal:date_from',

        ];

        $messages = [

            'vacation_type_id.required' => 'يجب ادخال نوع الاجازة',
            'date_from.required' => 'يجب ادخال تاريخ البداية',
            'date_to.required' => 'يجب ادخال تاريخ النهاية',
            'date_from.before_or_equal' => 'تاريخ البداية يجب ان يكون قبل او يساوي تاريخ النهاية',
            'date_to.after_or_equal' => 'تاريخ النهاية يجب ان يكون بعد او يساوي تاريخ البداية',
        ];
        $validatedData = Validator::make($request->all(), $rules, $messages);

        if ($validatedData->fails()) {
            session()->flash('errors', $validatedData->errors());

            return redirect()->route('vacations.list', $id);
        }

        $employee_vacation =  EmployeeVacation::find($id);
        $employee_vacation->vacation_type_id = $request->vacation_type_id;
        $employee_vacation->date_from = $request->date_from;
        $employee_vacation->date_to = isset($request->date_to) ? $request->date_to : null;
        $employee_vacation->employee_id = ($request->employee_id && $request->vacation_type_id != 3) ? $request->employee_id : null;
        $employee_vacation->created_by = auth()->id();
        $employee_vacation->created_departement = auth()->user()->department_id;
        $employee_vacation->save();
        if ($request->hasFile('reportImage')) {
            $file = $request->reportImage;
            // You can modify the UploadFiles function call according to your needs
            $path = 'vacations/employee';

            UploadFiles($path, 'report_image', 'report_image_real', $employee_vacation, $file);
        }
        session()->flash('success', 'تم التعديل بنجاح.');
        if ($request->employee_id) {
            return redirect()->route('vacations.list', $request->employee_id);
        } else {

            return redirect()->route('vacations.list');
        }
    }
    public  function delete($id)
    {
        $EmployeeVacation = EmployeeVacation::find($id);
        $EmployeeVacation->delete();
        session()->flash('success', 'تم الحذف بنجاح.');

        return redirect()->route('vacations.list');
    }
    public function downlaodfile($id)
    {
        $file = EmployeeVacation::find($id);
        // $download=downloadFile($file->file_name,$file->real_name);
        $file_path = public_path($file->file_name);
        $file_name = basename($file->real_name);

        return response()->download($file_path, $file_name);
        //echo 'downloaded';
    }

    public function acceptVacation($id)
    {
        // Find the vacation record
        $vacation = EmployeeVacation::find($id);

        if ($vacation) {
            // Update the status to Approved
            $vacation->status = 'Approved';
            $vacation->save();

            // Find the inspector based on employee_id
            $inspector = Inspector::where('user_id', $vacation->employee_id)->first();

            if ($inspector) {
                // Fetch InspectorMission records for the found inspector ID
                $inspectorMissions = InspectorMission::where('inspector_id', $inspector->id)
                    ->whereDate('date', '>=', $vacation->start_date)
                    ->get();

                if ($inspectorMissions->isEmpty()) {
                    session()->flash('info', 'لا توجد مهام للمفتش لتحديثها.');
                } else {
                    $daysNumber = $vacation->days_number; // Ensure this field exists in your EmployeeVacation model

                    foreach ($inspectorMissions as $mission) {
                        // Check if the mission date is within the vacation period
                        if ($mission->date->diffInDays($vacation->start_date) < $daysNumber) {
                            // Update the InspectorMission record with the vacation ID
                            $mission->vacation_id = $vacation->id;
                            // $mission->status = 'Canceled'; // Or another appropriate status
                            $mission->save();
                        }
                    }
                    session()->flash('success', 'تمت الموافقة على الإجازة بنجاح وتم تحديث المهام الخاصة بالمفتش.');
                }
            } else {
                session()->flash('error', 'المفتش غير موجود.');
                return redirect()->back();
            }
        } else {
            session()->flash('error', 'الإجازة غير موجودة.');
        }

        return redirect()->back();
    }

    public function rejectVacation($id)
    {
        // Find the vacation record
        $vacation = EmployeeVacation::find($id);

        if ($vacation) {
            // Update the status to Rejected
            $vacation->status = 'Rejected';
            $vacation->save();

            session()->flash('success', 'تم رفض الإجازة بنجاح.');
        } else {
            session()->flash('error', 'الإجازة غير موجودة.');
        }
        return redirect()->back();

        // return redirect()->route('vacations.list', $vacation->employee_id ?? null);
    }
}
