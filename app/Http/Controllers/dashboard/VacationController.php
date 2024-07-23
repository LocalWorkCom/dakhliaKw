<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use App\Models\EmployeeVacation;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class VacationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($id = 0)
    {
        return view('vacation.index', compact('id'));
        // return $dataTable->render('vacation.index');
    }
    public function getVacations($id)
    {
        if ($id) {

            $EmployeeVacations = EmployeeVacation::where('employee_id', $id)
                ->with('employee', 'vacation_type')
                ->get();
            foreach ($EmployeeVacations as  $EmployeeVacation) {
                # code...
                $EmployeeVacation['StartVacation'] = CheckStartVacationDate($EmployeeVacation->id);
            }
            return DataTables::of($EmployeeVacations)

                ->rawColumns(['action'])
                ->make(true);
        } else {
            $EmployeeVacations = EmployeeVacation::with('employee', 'vacation_type')->get();
            foreach ($EmployeeVacations as  $EmployeeVacation) {
                # code...
                $EmployeeVacation['StartVacation'] = CheckStartVacationDate($EmployeeVacation->id);
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
        return view('vacation.add', compact('employees', 'vacation_types', 'id'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $id)
    {

        $employee_vacation = new EmployeeVacation();
        $employee_vacation->vacation_type_id = $request->vacation_type_id;
        $employee_vacation->date_from = $request->date_from;
        $employee_vacation->date_to = isset($request->date_to) ? $request->date_to : null;
        $employee_vacation->employee_id = isset($request->employee_id) ? $request->employee_id : null;
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
        $vacation = EmployeeVacation::find($id);
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
        return view('vacation.edit', compact('employees', 'vacation', 'vacation_types', 'id'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {

        $employee_vacation =  EmployeeVacation::find($id);
        $employee_vacation->vacation_type_id = $request->vacation_type_id;
        $employee_vacation->date_from = $request->date_from;
        $employee_vacation->date_to = isset($request->date_to) ? $request->date_to : null;
        $employee_vacation->employee_id = $request->employee_id;
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

        return redirect()->route('vacations.list');
    }
    public  function delete($id)
    {
        $EmployeeVacation = EmployeeVacation::find($id);
        $EmployeeVacation->delete();
        session()->flash('success', 'تم الحذف بنجاح.');

        return redirect()->route('vacations.list');
    }
}
