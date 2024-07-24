<?php

namespace App\Http\Controllers;

use App\DataTables\gradeDataTable;
use App\DataTables\jobDataTable;
use App\DataTables\VacationDataTable;
use App\DataTables\vacationTypeDataTable;
use App\Http\Controllers\Controller;
use App\Models\Government;
use App\Models\grade;
use App\Models\job;
use App\Models\VacationType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class settingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    //START government
    //show governments
    public function indexgovernment()
    {
        // $activeTab = $request->query('activeTab', 1); // Default to 1 if not present
        // $message = $request->query('message', '');
    return view("governments.index");
    }
    //create governments
    public function creategovernment()
    {
        return view("governments.add");
    }
     
    //get data for governments
    public function getAllgovernment()
    {
        $data = Government::orderBy('created_at','desc')->get();
       
        return DataTables::of($data)->addColumn('action', function ($row) {
            $name = "'$row->name'";
            $editButton = '<a class="btn btn-primary btn-sm" onclick="openedit(' . $row->id . ', '.$name.')">تعديل</a>';
            return $editButton;
            
            // <a class="btn btn-primary btn-sm" href=' . route('government.show', $row->id) . '>التفاصيل</a>
        })
        ->rawColumns(['action'])
        ->make(true);
    }
    //add government
    public function addgovernment(Request $request){
        $requestinput=$request->except('_token');
        $job = new Government();
        $job->name=$request->nameadd;
        $job->save();
        $message="تم اضافه الوظيفه";
        return redirect()->route('government.all',compact('message'));
        //return redirect()->back()->with(compact('activeTab','message'));
    }
    //show government
    public function showgovernment($id)
    {
        $data = Government::findOrFail($id);
        return view("governments.show" ,compact("data"));
    }
    //edit governments
    public function editgovernment($id)
    {
        $data = Government::findOrFail($id);
        return view("governments.edit" ,compact("data"));
    }
     //update governments
     public function updategovernment(Request $request)
     {
        $gover = Government::find($request->id);
         
        if (!$gover) {
            return response()->json(['error' => 'هذه الماحفظه غير موجوده'], 404);
        }
        $gover->name=$request->name;
        $gover->save();
    
        $message='';
        return redirect()->route('government.all',compact('message'));
     }
    
    //END government

//START JOB
    //show JOB
    public function indexjob()
    {
        // $activeTab = $request->query('activeTab', 1); // Default to 1 if not present
        // $message = $request->query('message', '');
    return view("jobs.index");
    }
    //create JOB
    public function createjob()
    {
        return view("jobs.add");
    }
     
    //get data for JOB
    public function getAllJob()
    {
        $data = job::orderBy('created_at','desc')->get();
       
        return DataTables::of($data)->addColumn('action', function ($row) {
            $name = "'$row->name'";
            $editButton = '<a class="btn btn-primary btn-sm" onclick="openedit(' . $row->id . ', '.$name.')">تعديل</a>';
            $deleteButton = '<a class="btn btn-primary btn-sm" onclick="opendelete(' . $row->id . ')">حذف</a>';

            return $editButton . ' ' . $deleteButton;
            // <a class="btn btn-primary btn-sm" href=' . route('job.show', $row->id) . '>التفاصيل</a>

        })
        ->rawColumns(['action'])
        ->make(true);
    }
    //add JOB
    public function addJob(Request $request){
        $requestinput=$request->except('_token');
        $job = new job();
          $job->name=$request->nameadd;
          $job->save();
        $message="تم اضافه الوظيفه";
        return redirect()->route('job.index',compact('message'));
        //return redirect()->back()->with(compact('activeTab','message'));
    }
    //show JOB
    public function showjob($id)
    {
        $data = job::findOrFail($id);
        return view("jobs.show" ,compact("data"));
    }
    //edit JOB
    public function editjob($id)
    {
        $data = job::findOrFail($id);
        return view("jobs.edit" ,compact("data"));
    }
     //update JOB
     public function updateJob(Request $request ){
        $job = job::find($request->id);
        
        if (!$job) {
            return response()->json(['error' => 'Grade not found'], 404);
        }
        $job->name=$request->name;
        $job->save();
        $message='';
        return redirect()->route('job.index',compact('message'));
       // return redirect()->back()->with(compact('activeTab'));
        
    }
    
    //delete JOB
    public function deletejob(Request $request )
    {

        $isForeignKeyUsed = DB::table('users')->where('job_id', $request->id)->exists();
        //dd($isForeignKeyUsed);  
        if( $isForeignKeyUsed ){
            return redirect()->route('job.index')->with(['message' => 'لا يمكن حذف هذه الوظيفه يوجد موظفين لها']);
        }else{
            $type= job::find($request->id);
            $type->delete();
            return redirect()->route('job.index')->with(['message' => 'تم حذف الوظيفه']);

        }
       
    }
    //END JOB

    //START GRAD
    //show GRAD
    public function indexgrads()
    {
        // $activeTab = $request->query('activeTab', 1); // Default to 1 if not present
        // $message = $request->query('message', '');
    return view("grads.index");
    }
    //create GRAD
    public function creategrads()
    {
        return view("grads.add");
    }
     
    //get data for GRAD
    public function getAllgrads()
    {
        $data = grade::orderBy('created_at','desc')->get();
       
        return DataTables::of($data)->addColumn('action', function ($row) {
            $name = "'$row->name'";
            $editButton = '<a class="btn btn-primary btn-sm" onclick="openedit(' . $row->id . ', '.$name.')">تعديل</a>';
            $deleteButton = '<a class="btn btn-primary btn-sm" onclick="opendelete(' . $row->id . ')">حذف</a>';

            return $editButton . ' ' . $deleteButton;
            // <a class="btn btn-primary btn-sm" href=' . route('grads.show', $row->id) . '>التفاصيل</a>

        })
        ->rawColumns(['action'])
        ->make(true);
    }
    //add GRAD
    public function addgrads(Request $request){
        $requestinput=$request->except('_token');
        $job = new grade();
        $job->name=$request->nameadd;
        $job->save();
        $message="تم اضافه الوظيفه";
        return redirect()->route('grads.index',compact('message'));
        //return redirect()->back()->with(compact('activeTab','message'));
    }
    //show GRAD
    public function showgrads($id)
    {
        $data = grade::findOrFail($id);
        return view("grads.show" ,compact("data"));
    }
    //edit GRAD
    public function editgrads($id)
    {
        $data = grade::findOrFail($id);
        return view("grads.edit" ,compact("data"));
    }
     //update GRAD
     public function updategrads(Request $request ){
        $job = grade::find($request->id);
        
        if (!$job) {
            return response()->json(['error' => 'Grade not found'], 404);
        }
        $job->name=$request->name;
        $job->save();
        $message='';
        return redirect()->route('grads.index',compact('message'));
       // return redirect()->back()->with(compact('activeTab'));
        
    }
    
    //delete GRAD
    public function deletegrads(Request $request )
    {

        $isForeignKeyUsed = DB::table('users')->where('grade_id', $request->id)->exists();
        //dd($isForeignKeyUsed);  
        if( $isForeignKeyUsed ){
            return redirect()->route('grads.index')->with(['message' => 'لا يمكن حذف هذه الرتبه يوجد موظفين لها']);
        }else{
            $type= grade::find($request->id);
            $type->delete();
            return redirect()->route('grads.index')->with(['message' => 'تم حذف الرتبه']);

        }
       
    }
    //END GRAD

    //START VACATION TYPE
      //show JOB
      public function indexvacationType()
      {
          // $activeTab = $request->query('activeTab', 1); // Default to 1 if not present
          // $message = $request->query('message', '');
      return view("vacationType.index");
      }
      //create JOB
      public function createvacationType()
      {
          return view("vacationType.add");
      }
       
      //get data for JOB
      public function getAllvacationType()
      {
          $data = VacationType::orderBy('created_at','desc')->get();
         

          return DataTables::of($data)->addColumn('action', function ($row) {
            $hiddenIds = [1, 2, 3, 4];
            $name = "'$row->name'";
            $editButton = '<a class="btn btn-primary btn-sm" onclick="openedit(' . $row->id . ', '.$name.')">تعديل</a>';
            if (!in_array($row->id, $hiddenIds)) {
                $deleteButton = '<a class="btn btn-primary btn-sm" onclick="opendelete(' . $row->id . ')">حذف</a>';
                return $editButton . ' ' . $deleteButton;
            }else{
                return $editButton;
            }
            // href="' . route('vacationType.edit', $row->id) . '" 

            //   <a class="btn btn-primary btn-sm" href=' . route('vacationType.show', $row->id) . '>التفاصيل</a>

          })
          ->rawColumns(['action'])
          ->make(true);
      }
      //add JOB
      public function addvacationType(Request $request){
          $requestinput=$request->except('_token');
          //dd($request->nameadd);
          $job = new VacationType();
          $job->name=$request->nameadd;
          $job->save();
         
          $message="تم اضافة الوظيفه";
          return redirect()->route('vacationType.index',compact('message'));
          //return redirect()->back()->with(compact('activeTab','message'));
      }
      //show JOB
      public function showvacationType($id)
      {
          $data = VacationType::findOrFail($id);
          return view("vacationType.show" ,compact("data"));
      }
      //edit JOB
      public function editvacationType(Request $request)
      {
          $data = VacationType::findOrFail($request->id);
          return view("vacationType.edit" ,compact("data"));
      }
       //update JOB
       public function updatevacationType(Request $request ){
          $job = VacationType::find($request->id);
          
          if (!$job) {
              return response()->json(['error' => 'Grade not found'], 404);
          }
          $job->name=$request->name;
          $job->save();
          $message='تم تعديل الاسم';
          return redirect()->route('vacationType.index',compact('message'));
         // return redirect()->back()->with(compact('activeTab'));
          
      }
      
      //delete JOB
      public function deletevacationType(Request $request )
      {
  
        $isForeignKeyUsed = DB::table('employee_vacations')->where('vacation_type_id', $request->id)->exists();
          //dd($isForeignKeyUsed);  
          if( $isForeignKeyUsed ){
              return redirect()->route('vacationType.index')->with(['message' => 'لا يمكن حذف هذه نوع الاجازه يوجد موظفين لها']);
          }else{
              $type= VacationType::find($request->id);
              $type->delete();
              return redirect()->route('vacationType.index')->with(['message' => 'تم حذف نوع الاجازه']);
  
          }
         
      }
    //END VACATION TYPE








    public function getAllGrade()
    {
        $data = grade::get();
       
        return DataTables::of($data)->addColumn('action', function ($row) {
            return '<button class="btn btn-primary btn-sm">Edit</button>';
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


    public function addgrade(Request $request){
        $requestinput=$request->except('_token');
        $grade = grade::create($requestinput);
        $activeTab=1;
        $message="تم اضافة رتبه عسكريه جديده";
        return redirect()->route('setting.index',compact('activeTab','message'));

        //return redirect()->back()->with(compact('activeTab','message'));
    }

    public function addVacation(Request $request){
        $request=$request->except('_token');
        $vacation = VacationType::create($request);
        $activeTab=3;
        $message="تم اضافة نوع اجازه جديد";
        return redirect()->route('setting.index',compact('activeTab','message'));
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

    // deletefunction
    // public function deletegovernment(Request $request ){
    //     $isForeignKeyUsed = DB::table('users')->where('grade_id', $request->id)->exists();
    //     //dd($isForeignKeyUsed);  
    //     if( $isForeignKeyUsed ){
    //         $message='';

    //     }else{
    //         $type= grade::find($request->id);
    //         $type->delete();
    //         $message='';
    
    //     }
    //     $activeTab =1;
    //     return redirect()->route('setting.index',compact('activeTab','message'));
    //     //return view("setting.view",compact('activeTab','message'));
    // }
    
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