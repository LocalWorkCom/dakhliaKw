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
    public function index(Request $request )
    {
        $activeTab = $request->query('activeTab', 1); // Default to 1 if not present
        $message = $request->query('message', '');
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
        $requestinput=$request->except('_token');
        $job = job::create($requestinput);
        $activeTab=2;
        $message="تم اضافه الوظيفه";
        return redirect()->route('setting.index',compact('activeTab','message'));
        //return redirect()->back()->with(compact('activeTab','message'));
    }

    public function addgrade(Request $request){
        $requestinput=$request->except('_token');
        $grade = grade::create($requestinput);
        $activeTab=1;
        $message="تم اضافه رتبه عسكريه جديده";
        return redirect()->route('setting.index',compact('activeTab','message'));

        //return redirect()->back()->with(compact('activeTab','message'));
    }

    public function addVacation(Request $request){
        $request=$request->except('_token');
        $vacation = VacationType::create($request);
        $activeTab=3;
        $message="تم اضافه نوع اجازه جديد";
        return redirect()->route('setting.index',compact('activeTab','message'));
    }
    
    public function editJob(Request $request ){
        $job = job::find($request->id);
        
        if (!$job) {
            return response()->json(['error' => 'Grade not found'], 404);
        }
        $job->name=$request->namegrade;
        $job->save();
        $activeTab =$request->tab;
        $message='';
        return redirect()->route('setting.index',compact('activeTab','message'));
       // return redirect()->back()->with(compact('activeTab'));
        
    }

    public function editgrade(Request $request ){
     
       $grade = Grade::find($request->id);
        
        if (!$grade) {
            return response()->json(['error' => 'Grade not found'], 404);
        }
        $grade->name=$request->namegrade;
        $grade->save();
        $activeTab =$request->tab;
        $message='';
        return redirect()->route('setting.index',compact('activeTab','message'));
    }

    public function editVacation(Request $request ){
        $type = VacationType::find($request->id);
        
        if (!$type) {
            return response()->json(['error' => 'Grade not found'], 404);
        }
        $type->name=$request->namegrade;
        $type->save();
    
        $activeTab =$request->tab;
        $message='';
        return redirect()->route('setting.index',compact('activeTab','message'));
    }
    public function deleteVacation(Request $request ){
        $isForeignKeyUsed = DB::table('employee_vacations')->where('vacation_type_id', $request->id)->exists();
        //dd($isForeignKeyUsed);  
        if( $isForeignKeyUsed ){
           
            $message='';
            

        }else{
            $type= VacationType::find($request->id);
            $type->delete();
            $message='';

        }
        $activeTab =3;
        return redirect()->route('setting.index',compact('activeTab','message'));
    }

    public function deletegrade(Request $request ){
        $isForeignKeyUsed = DB::table('users')->where('grade_id', $request->id)->exists();
        //dd($isForeignKeyUsed);  
        if( $isForeignKeyUsed ){
            $message='';

        }else{
            $type= grade::find($request->id);
            $type->delete();
            $message='';
    
        }
        $activeTab =1;
        return redirect()->route('setting.index',compact('activeTab','message'));
        //return view("setting.view",compact('activeTab','message'));
    }

    public function deletejob(Request $request ){
        $isForeignKeyUsed = DB::table('users')->where('job_id', $request->id)->exists();
        //dd($isForeignKeyUsed);  
        if( $isForeignKeyUsed ){
            $message='';

        }else{
            $type= job::find($request->id);
            $type->delete();
            $message='';

        }
        $activeTab =2;
        return redirect()->route('setting.index',compact('activeTab','message'));
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
