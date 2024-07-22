<?php

namespace App\Http\Controllers;

use App\DataTables\gradeDataTable;
use App\DataTables\jobDataTable;
use App\DataTables\VacationDataTable;
use App\DataTables\vacationTypeDataTable;
use App\Http\Controllers\Controller;
use App\Models\grade;
use App\Models\job;
use App\Models\VacationType;
use Illuminate\Http\Request;

class settingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // public function index(vacationTypeDataTable $dataTable ,gradeDataTable $dataTableGrade ,jobDataTable $jobDataTable )
    public function index(gradeDataTable $dataTable )
    {
    //    $vacation = $dataTable->html();
    //   $grade = $dataTable;
      return $dataTable->render("setting.view");
    //return view("setting.view",compact('grade'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function addJob(Request $request){
        $request=$request->except('_token');
        $job = job::create($request);
        return redirect()->back()->with("success","تم اضافه الوظيفه");
    }

    public function addgrade(Request $request){
        $grade = grade::create($request->all());
        return redirect()->back()->with("success","تم اضافه رتبه عسكريه جديده");
    }

    public function addVacation(Request $request){
        $vacation = VacationType::create($request->all());
        return redirect()->back()->with("success","تم اضافه نوع اجازه جديد");
    }
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
