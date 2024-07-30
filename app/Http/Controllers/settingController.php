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
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class settingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
  

//START JOB
    //show JOB
    public function indexjob()
    {
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
        $data = job::orderBy('updated_at','desc')->orderBy('created_at','desc')->get();

        return DataTables::of($data)->addColumn('action', function ($row) {
            $name = "'$row->name'";
            if(Auth::user()->hasPermission('edit job')){
                $edit_permission = '<a class="btn btn-sm"  style="background-color: #F7AF15;"  onclick="openedit('.$row->id.','.$name.')">  <i class="fa fa-edit"></i> تعديل </a>';
                
            }
            if(Auth::user()->hasPermission('delete job')){
                $delete_permission = ' <a class="btn  btn-sm" style="background-color: #C91D1D;"   onclick="opendelete('.$row->id.')"> <i class="fa-solid fa-trash"></i> حذف</a>';
                
            }
            $uploadButton = $edit_permission . $delete_permission;
            return $uploadButton;

        })
        ->rawColumns(['action'])
        ->make(true);
    }
    //add JOB
    public function addJob(Request $request){
        $rules = [
            'nameadd' => 'required|string',
        ];

        $messages = [
            'nameadd.required' => 'يجب ادخال الوظيفه ',
        ];

        $validatedData = Validator::make($request->all(), $rules, $messages);
        if ($validatedData->fails()) {
            return response()->json(['success' => false, 'message' => $validatedData->errors()]);
        }
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
        $data = grade::orderBy('updated_at','desc')->orderBy('created_at','desc')->get();

        return DataTables::of($data)->addColumn('action', function ($row) {
            $name = "'$row->name'";
            if(Auth::user()->hasPermission('edit grade')){
                $edit_permission = '<a class="btn btn-sm"  style="background-color: #F7AF15;"  onclick="openedit('.$row->id.','.$name.')">  <i class="fa fa-edit"></i> تعديل </a>';
                
            }
            if(Auth::user()->hasPermission('delete grade')){
                $delete_permission = ' <a class="btn  btn-sm" style="background-color: #C91D1D;"   onclick="opendelete('.$row->id.')"> <i class="fa-solid fa-trash"></i> حذف</a>';
                
            }
            $uploadButton = $edit_permission . $delete_permission;
            return $uploadButton;
            // return '<a class="btn  btn-sm" style="background-color: #259240;" onclick="openedit('.$row->id.','.$name.')"> <i class="fa fa-edit"></i> </a>
            // <a class="btn  btn-sm" style="background-color: #C91D1D;"  onclick="opendelete('.$row->id.')"> <i class="fa-solid fa-trash"></i> </a>' ;
            // <a class="btn  btn-sm" href=' . route('grads.show', $row->id) . '>التفاصيل</a>

        })
        ->rawColumns(['action'])
        ->make(true);
    }
    //add GRAD
    public function addgrads(Request $request){
        $rules = [
            'nameadd' => 'required|string',
        ];

        $messages = [
            'nameadd.required' => 'يجب ادخال اسم الرتبه ',
        ];

        $validatedData = Validator::make($request->all(), $rules, $messages);
        if ($validatedData->fails()) {
            return response()->json(['success' => false, 'message' => $validatedData->errors()]);
        }
        $requestinput=$request->except('_token');
        $job = new grade();
        $job->name=$request->nameadd;
        $job->save();
        $message="تم اضافه الرتبه";
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
            return response()->json(['error' => 'عفوا هذه الرتبه غير موجوده'], 404);
        }
        $job->name=$request->name;
        $job->save();
        $message='تم تعديل الرتبه';
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
          
          $data = VacationType::orderBy('updated_at','desc')->orderBy('created_at','desc')->get();
         

          return DataTables::of($data)->addColumn('action', function ($row) {
            $hiddenIds = [1, 2, 3, 4];
            $name = "'$row->name'";
            $edit_permission =null;
            $delete_permission = null;
            if(Auth::user()->hasPermission('edit VacationType')){
                $edit_permission = '<a class="btn btn-sm"  style="background-color: #F7AF15;"  onclick="openedit('.$row->id.','.$name.')">  <i class="fa fa-edit"></i> تعديل </a>';
            }
            if(Auth::user()->hasPermission('delete VacationType')){
                if (!in_array($row->id, $hiddenIds)) {
                    $delete_permission = ' <a class="btn  btn-sm" style="background-color: #C91D1D;"   onclick="opendelete('.$row->id.')"> <i class="fa-solid fa-trash"></i> حذف</a>';               
                }
            }
            return $edit_permission . $delete_permission;
           
          })
          ->rawColumns(['action'])
          ->make(true);
      }
      //add JOB
      public function addvacationType(Request $request){
        $rules = [
            'nameadd' => 'required|string',
        ];

        $messages = [
            'nameadd.required' => 'يجب ادخال نوع الأجازه ',
        ];

        $validatedData = Validator::make($request->all(), $rules, $messages);
        if ($validatedData->fails()) {
            return response()->json(['success' => false, 'message' => $validatedData->errors()]);
        }
          $requestinput=$request->except('_token');
          //dd($request->nameadd);
          $job = new VacationType();
          $job->name=$request->nameadd;
          $job->save();
         
          $message="تم اضافة نوع الأجازه";
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
              return response()->json(['error' => 'هذه الأجازه غير موجوده'], 404);
          }
          $job->name=$request->name;
          $job->save();
          $message='تم تعديل نوع الأجازه';
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
