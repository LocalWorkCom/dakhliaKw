<?php

namespace App\Http\Controllers;

use App\DataTables\outgoingsDataTable;
use App\Http\Controllers\Controller;
use App\Models\exportuser;
use App\Models\ExternalDepartment;
use App\Models\outgoing_files;
use App\Models\outgoings;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;


class outgoingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
       
        return view('outgoing.viewAll');
    }
    public function getExportActive()
    {
        $data = outgoings::with(['personTo', 'department_External'])
        ->where('outgoings.active', 0)
        ->orderBy('created_at','desc')
        ->select('outgoings.*');
       
        return DataTables::of($data)->addColumn('action', function ($row) {
            $fileCount = outgoing_files::where('outgoing_id', $row->id)->count();
            $is_file = $fileCount == 0;
           $uploadButton = $is_file 
               ? '<a class="btn btn-primary btn-sm" href=' . route('Export.edit', $row->id) . '>تعديل</a>'
               : '<a class="btn btn-primary btn-sm" onclick="opendelete('.$row->id.')"> اضف للأرشيف</a>';

            return '
                   <a class="btn btn-primary btn-sm" href=' . route('Export.show', $row->id) . '>عرض تفاصيل</a>
                    ' . $uploadButton ;
        })
        ->addColumn('person_to_username', function ($row) {
            return $row->personTo->name ?? 'لايوجد شخص صادر له'; // Assuming 'name' is the column in external_users
        })
        ->addColumn('department_External_name', function ($row) {
            return $row->department_External->name ?? 'لا يوجد قسم خارجى صادر له'; // Assuming 'name' is the column in external_users
        })
        ->rawColumns(['action'])
        ->make(true);
    }
    public function getExportInActive()
    {
       
        $data = outgoings::with(['personTo', 'department_External'])
        ->where('outgoings.active', 1)
        ->orderBy('created_at','desc')
        ->select('outgoings.*');
       
        return DataTables::of($data)->addColumn('action', function ($row) {
            return '
                   <a class="btn btn-primary btn-sm" href=' . route('Export.show', $row->id) . '">عرض تفاصيل</a>' ;
        })
        ->addColumn('person_to_username', function ($row) {
            return $row->personTo->name ?? 'لايوجد شخص صادر له'; // Assuming 'name' is the column in external_users
        })
        ->addColumn('department_External_name', function ($row) {
            return $row->department_External->name ?? 'لا يوجد قسم خارجى صادر له'; // Assuming 'name' is the column in external_users
        })
        ->rawColumns(['action'])
        ->make(true);
    }
    
    public function showFiles($id){
        
        return view('outgoing.showfile');
    }
    public function getExternalUsersAjax()
    {
        $users = exportuser::all();
        return $users;
    }
    public function addToArchive(Request $request){
        $export = outgoings::find($request->id);
        $export->active=1;
        $export->save();
        session()->flash('success', 'تم الاضافة الى الارشيف بنجاح.');
        return redirect()->back();
    }
    public function showArchive(outgoingsDataTable $dataTable, Request $request){
        $status = $request->get('status', 'inactive'); // Default to 'inactive' if 
        return $dataTable->with('status', $status)->render('outgoing.archiveall');

    }
    
    public function addUaersAjax(Request $request)
    {
        
        $user = new exportuser();
        $user->military_number = $request->military_number;
        $user->filenum = $request->filenum;
        $user->Civil_number = $request->Civil_number;
        $user->phone = $request->phone;
        $user->name = $request->name;
        $user->save();
        return true;
    }
    public function create()
    {
       
        $users=$this->getExternalUsersAjax();
        $departments=ExternalDepartment::all();
        return view('outgoing.add', compact('users','departments'));
    }

    public function store(Request $request)
    {
        //dd($request->all());
        // Define validation rules
        $rules = [
            'nameex' => 'required|string',
            'num' => 'required|integer',
            'note' => 'required|string',
            'person_to' => 'nullable|exists:export_users,id',
            'date' => 'required|date',
            'department_id' => 'nullable|exists:external_departements,id',
            'files.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf',
        ];

        // // Define custom messages
        $messages = [
            'nameex.required' => 'عفوا يجب ادخال اسم الصادر',
            'num.required' => 'عفوا يجب ادخال رقم الصادر',
            'note.required' => 'عفوا يجب ادخال ملاحظات الصادر',
            'num.integer' => 'عفوا يجب ان يحتوى رقم الصادر على ارقام فقط',
            'person_to.exists' => 'عفوا هذا المستخدم غير متاح',
            'files.*.mimes' => 'يجب ان تكون الملفات من نوع صور او pdfفقط ',
        ];
        $validatedData = Validator::make($request->all(), $rules, $messages);
        // // Validate the request
       // $request->validate($rules, $messages);
        if ($validatedData->fails()) {
            return redirect()->back()
                ->withErrors($validatedData)
                ->with('nameex', $request->name)
                ->with('note', $request->note)
                ->with('person_to', $request->person_to)
                ->with('date', $request->date)
                ->with('department_id', $request->department_id)
                ->with('files', $request->files)
                ->with('num', $request->num);
        }
        //dd( $request->validate($rules, $messages));
        if(auth()->id()){
            $user = User::find(auth()->id());
        $export = new outgoings();
        $export->name = $request->nameex;
        $export->num = $request->num;
        $export->note = $request->note;
        $export->date = $request->date;
        $export->person_to = $request->person_to  ?  $request->person_to :null;
        $export->created_by = $user->id;//auth auth()->id
        $export->created_department = $user->department_id;
        $export->active = $request->active;
        $export->updated_by = $user->id;//auth auth()->id
        $export->department_id = $request->from_departement;
        $export->save(); 
    

   if( $request->hasFile('files') ){
         
            //if (function_exists('UploadFiles')) {
                 //  dd('file yes');
                foreach ($request->file('files') as $file) {
                    $exfiles=new outgoing_files();
                    $exfiles->outgoing_id = $export->id;
                    $exfiles->created_by=auth()->id();//auth auth()->id
                    $exfiles->updated_by=auth()->id();//auth auth()->id
                    $exfiles->file_type = ($file->getClientOriginalExtension() == 'pdf')? 'pdf' : 'image';
                    $exfiles->active =0;
                    $exfiles->save();
                    $file_model = outgoing_files::find($files->id);
                    //UploadFiles($path, 'file_name', 'real_name', $io_file, $file);
                    UploadFiles('files/export','file_name',  'real_name',$exfiles, $file);
               // }
            }
        }     
        
        return redirect()->route('Export.index')->with('status', 'تم الاضافه بنجاح');
    }else{
        return redirect()->route('login');

    }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data=outgoings::with(['personTo', 'createdBy', 'updatedBy'])->findOrFail($id);
        $users=User::all();
        $is_file = outgoing_files::where('outgoing_id', $id)->get();
        //dd($is_file);
        $departments=ExternalDepartment::all();

        return view('outgoing.showdetail', compact('data','users','is_file','departments'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $data=outgoings::with(['personTo', 'createdBy', 'updatedBy'])->findOrFail($id);
        $users=User::all();
        $is_file = outgoing_files::where('outgoing_id', $id)->where('active',0)->get();
        $departments=ExternalDepartment::all();
        return view('outgoing.editexport', compact('data','users','is_file','departments'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
      
        // Define validation rules
        $rules = [
            'nameex' => 'required|string',
            'num' => 'required|integer',
            'note' => 'required|string',
            'person_to' => 'nullable|exists:export_users,id',
            'department_id' => 'nullable|exists:external_departements,id',
            'files.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf',
        ];

        // // Define custom messages
        $messages = [
            'nameex.required' => 'عفوا يجب ادخال اسم الصادر',
            'num.required' => 'عفوا يجب ادخال رقم الصادر',
            'note.required' => 'عفوا يجب ادخال ملاحظات الصادر',
            'num.integer' => 'عفوا يجب ان يحتوى رقم الصادر على ارقام فقط',
            'person_to.exists' => 'عفوا هذا المستخدم غير متاح',
            'files.*.mimes' => 'يجب ان تكون الملفات من نوع صور او pdfفقط ',
        ];

        // // Validate the request
        $request->validate($rules, $messages);
        //dd(auth()->id());
        if(auth()->id()){
        $user=User::findOrFail(auth()->id());
        
        $export = outgoings::findOrFail( $id );
        $export->name = $request->nameex;
        $export->num = $request->num;
        $export->note = $request->note;
        $export->date = $request->date;
        $export->person_to = $request->person_to  ?  $request->person_to :null;
        $export->created_by = $user->id;//auth auth()->id
        $export->active = $request->active;
        $export->updated_by = $user->id;//auth auth()->id
        $export->department_id = $request->department_id;
        $export->created_department =  $user->department_id;

        $export->save(); 
        
        if( $request->hasFile('files') ){
         
            if (function_exists('UploadFiles')) {
                 //  dd('file yes');
                foreach ($request->file('files') as $file) {
                    
                    $files=new outgoing_files();
                    $files->outgoing_id = $export->id;
                    $files->created_by=auth()->id();//auth auth()->id
                    $files->updated_by=auth()->id();//auth auth()->id
                    $files->file_type = ($file->getClientOriginalExtension() == 'pdf')? 'pdf' : 'image';
                    $files->active =0;
                    $files->save();
                    $file_model = outgoing_files::find($files->id);

                    UploadFiles('files/export','file_name', 'real_name', $file_model, $file);
                }
            }
        }
        return redirect()->route('Export.index')->with('status', 'تم الاضافة بنجاح');
    }else{
        return redirect()->route('login');

    }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * Test Upload
     */
    public function testUpload(Request $request)
    {
       // dd($request);
        $test=new outgoing_files();
        $test->outgoing_id=1;
        $test->active=1;
        $test->created_by=1;
        $test->created_at=now();
        $test->save();
        UploadFiles('files/test', 'file_name','real_name', $test, $request->file('files'));
        echo 'Uploaded';

    }
    /**
     * Download file
     */
    public function downlaodfile($id)
    {
        $file=outgoing_files::find($id);
       // $download=downloadFile($file->file_name,$file->real_name);
        $file_path = public_path($file->file_name);
        $file_name =basename($file->real_name);
    
        return response()->download($file_path, $file_name);
        //echo 'downloaded';
    }
}
