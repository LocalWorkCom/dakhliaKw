<?php

namespace App\Http\Controllers\dashboard;

use App\DataTables\VacationDataTable;
use App\Http\Controllers\Controller;
use App\Models\departements;
use App\Models\ExternalDepartment;
use App\Models\io_files;
use App\Models\Vacation;
use App\Models\Postman;
use App\Models\User;
use App\Models\EmployeeVacation;
use App\Models\VacationType;
use Illuminate\Http\Request;

class VacationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(VacationDataTable $dataTable)
    {
        return $dataTable->render('vacation.index');
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create($id = 0)
    {
        $employees = getEmployees();
        $vacation_types = getVactionTypes();
        return view('vacation.add', compact('employees', 'vacation_types', 'id'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $employee_vacation = new EmployeeVacation();
        $employee_vacation->vacation_type_id = $request->vacation_type_id;
        $employee_vacation->date_from = $request->date_from;
        $employee_vacation->date_to = isset($request->date_to) ? $request->date_to : null;
        $employee_vacation->employee_id = isset($request->employee_id) ? $request->employee_id : null;
        $employee_vacation->created_by = auth()->id();
        $employee_vacation->created_departement = auth()->user()->department_id;
        $employee_vacation->save();

        session()->flash('success', 'تم الحفظ بنجاح.');

        return redirect()->route('vacations.list');
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

        return view('vacation.show', compact('vacation', 'employees', 'vacation_types'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $employees = getEmployees();
        $vacation = EmployeeVacation::find($id);
        $vacation_types = getVactionTypes();

        return view('vacation.edit', compact('employees', 'vacation', 'vacation_types'));
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

        session()->flash('success', 'تم التعديل بنجاح.');

        return redirect()->route('vacations.list');
    }
}
