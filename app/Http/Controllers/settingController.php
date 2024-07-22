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
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class settingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // public function index(vacationTypeDataTable $dataTable ,gradeDataTable $dataTableGrade ,jobDataTable $jobDataTable )
    public function index(gradeDataTable $dataTable )
    {
   
      //return $dataTable->render("setting.view");
      $activeTab=1;
    return view("setting.view",compact('activeTab'));
    }
    public function getAllGrade()
    {
        $data = grade::get();
       
        return DataTables::of($data)->addColumn('action', function ($row) {
            return '<button class="btn btn-primary btn-sm">Edit</button>'
                    ;
        })
        ->rawColumns(['action'])
        ->make(true);
    }
    public function getAllJob()
    {
        $data = job::get();
       
        return DataTables::of($data)->addColumn('action', function ($row) {
            return '<button class="btn btn-primary btn-sm">Edit</button>'
                    ;
        })
        ->rawColumns(['action'])
        ->make(true);
    }
    public function getAllVacation()
    {
        $data = VacationType::get();
       
        return DataTables::of($data)->addColumn('action', function ($row) {
            return '<button class="btn btn-primary btn-sm">Edit</button>'
                    ;
        })
        ->rawColumns(['action'])
        ->make(true);
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
        $request=$request->except('_token');
        $grade = grade::create($request);
        return redirect()->back()->with("success","تم اضافه رتبه عسكريه جديده");
    }

    public function addVacation(Request $request){
        $request=$request->except('_token');
        $vacation = VacationType::create($request);
        return redirect()->back()->with("success","تم اضافه نوع اجازه جديد");
    }
    
    public function editJob(Request $request ){
        $job = job::find($request->id);
        
        if (!$job) {
            return response()->json(['error' => 'Grade not found'], 404);
        }
        $job->name=$request->namegrade;
        $job->save();
        $activeTab =$request->tab;
        return redirect()->back()->with(compact('activeTab'));
        
    }

    public function editgrade(Request $request ){
     
       $grade = Grade::find($request->id);
        
        if (!$grade) {
            return response()->json(['error' => 'Grade not found'], 404);
        }
        $grade->name=$request->namegrade;
        $grade->save();
    
        return redirect()->back()->with("success","تم اضافه نوع اجازه جديد");
    }

    public function editVacation(Request $request ){
        $type = VacationType::find($request->id);
        
        if (!$type) {
            return response()->json(['error' => 'Grade not found'], 404);
        }
        $type->name=$request->namegrade;
        $type->save();
    
        return redirect()->back()->with("success","تم اضافه نوع اجازه جديد");
    }
    public function deleteVacation(Request $request ){
        $isForeignKeyUsed = DB::table('employee_vacations')->where('vacation_type_id', $request->id)->exists();
        //dd($isForeignKeyUsed);  
        if( $isForeignKeyUsed ){
            return redirect()->back()->with("success",'عفوا هذه الاجازه مرتبطه بموظفين');

        }else{
            $type= VacationType::find($request->id);
            $type->delete();
            return redirect()->back()->with("success",' تم المسح');

        }
    }

    public function deletegrade(Request $request ){
        $isForeignKeyUsed = DB::table('users')->where('grade_id', $request->id)->exists();
        //dd($isForeignKeyUsed);  
        if( $isForeignKeyUsed ){
            return redirect()->back()->with("success",'عفوا هذه الرتبه مرتبطه بموظفين');

        }else{
            $type= grade::find($request->id);
            $type->delete();
            return redirect()->back()->with("success",' تم المسح');

        }
    }

    public function deletejob(Request $request ){
        $isForeignKeyUsed = DB::table('users')->where('job_id', $request->id)->exists();
        //dd($isForeignKeyUsed);  
        if( $isForeignKeyUsed ){
            return redirect()->back()->with("success",'عفوا هذه الوظيفه مرتبطه بموظفين');

        }else{
            $type= job::find($request->id);
            $type->delete();
            return redirect()->back()->with("success",' تم المسح');

        }
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
