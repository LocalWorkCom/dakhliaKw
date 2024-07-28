<?php

namespace App\Http\Controllers;

use App\DataTables\outgoingsDataTable;
use App\Http\Controllers\Controller;
use App\Models\exportuser;
use App\Models\ExternalDepartment;
use App\Models\outgoing_files;
use App\Models\outgoings;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;


class outgoingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    

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
               ? '<a class="btn  btn-sm"  style="background-color: #259240;" href=' . route('Export.edit', $row->id) . '> <i class="fa fa-edit"></i> </a>'
               : '<a class="btn  btn-sm" style="background-color:#c1920c;" onclick="opendelete('.$row->id.')">  <i class="fa-solid fa-file-arrow-up"></i> </a>';

            return '
                   <a class="btn  btn-sm" style="background-color: #375A97;" href=' . route('Export.show', $row->id) . '> <i class="fa fa-eye"></i> </a>
                    ' . $uploadButton ;
        })
        ->addColumn('person_to_username', function ($row) {
            return $row->personTo->name ?? 'لايوجد شخص صادر له'; // Assuming 'name' is the column in external_users
        })
        ->addColumn('department_External_name', function ($row) {
            return $row->department_External->name ?? 'لا يوجد قسم خارجى صادر له'; // Assuming 'name' is the column in external_users
        })
        ->addColumn('date', function ($row) {
            return $row->date ?? 'لا يوجد تاريخ'; // Assuming 'name' is the column in external_users
        })
        ->addColumn('note', function ($row) {
            return $row->note ?? 'لا يوجد ملاحظات'; // Assuming 'name' is the column in external_users
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
                   <a class="btn btn-primary btn-sm"  style="background-color: #375A97;" href=' . route('Export.show', $row->id) . '> <i class="fa fa-eye"></i></a>' ;
        })
        ->addColumn('person_to_username', function ($row) {
            return $row->personTo->name ?? 'لايوجد شخص صادر له'; // Assuming 'name' is the column in external_users
        })
        ->addColumn('department_External_name', function ($row) {
            return $row->department_External->name ?? 'لا يوجد قسم خارجى صادر له'; // Assuming 'name' is the column in external_users
        })
        ->addColumn('note', function ($row) {
            return $row->note ?? 'لا يوجد ملاحظات'; // Assuming 'name' is the column in external_users
        })
        ->addColumn('date', function ($row) {
            return $row->date ?? 'لا يوجد تاريخ'; // Assuming 'name' is the column in external_users
        })
        ->rawColumns(['action'])
        ->make(true);
    }
    
    public function getExternalUsersAjax()
    {
        $users = exportuser::orderBy('created_at','desc')->get();
        return $users;
    }
    public function addToArchive(Request $request){
        $export = outgoings::find($request->id);
        $export->active=1;
        $export->save();
        session()->flash('success', 'تم الاضافة الى الارشيف بنجاح.');
        return redirect()->back();
    }
    public function showArchive(){
        return view('outgoing.archiveall');
        // return $dataTable->with('status', $status)->render('outgoing.archiveall');

    }

    public function addUaersAjax(Request $request)
{
    $rules = [
        'military_number' => ['required', 'string', 'unique:export_users,military_number'],
        'filenum' => ['required', 'string', 'unique:export_users,filenum'],
        'Civil_number' => ['required', 'string', 'unique:export_users,Civil_number'],
        'phoneuser' => ['required', 'string', 'unique:export_users,phone', 'regex:/^01\d{9,11}$/'],
        'name' => 'required|string',
    ];

    $messages = [
        'military_number.required' => 'يجب ادخال الرقم العسكرى',
        'military_number.unique' => 'عفوا رقم العسكرى موجود من قبل',
        'filenum.unique' => 'عفوا رقم الملف موجود من قبل',
        'military_number.string' => 'يجب ان يكون الرقم العسكرى أرقام فقط',
        'filenum.string' => 'رقم الملف يجب ان يكون ارقام فقط',
        'filenum.required' => 'يجب ادخال رقم الملف',
        'Civil_number.required' => 'يجب ادخال رقم المدنى',
        'Civil_number.unique' => 'عفوا رقم المدنى موجود من قبل',
        'Civil_number.string' => 'يجب ان يكون رقم المدنى ارقام فقط',
        'phoneuser.required' => 'يجب ادخال الهاتف',
        'phoneuser.regex'=>'رقم الهاتف يجب ان يكون أرقام فقط',
        'phoneuser.unique' => 'هذا الرقم موجود من قبل',
        'name.required' => 'يجب ادخال اسم الشخص',
    ];

    $validatedData = Validator::make($request->all(), $rules, $messages);

    if ($validatedData->fails()) {
        return response()->json(['success' => false, 'message' => $validatedData->errors()]);
    }

    $user = new ExportUser();
    $user->military_number = $request->military_number;
    $user->filenum = $request->filenum;
    $user->Civil_number = $request->Civil_number;
    $user->phone = $request->phoneuser;
    $user->name = $request->name;
    $user->save();

    return response()->json(['success' => true]);
}
    public function create()
    {

        $users=$this->getExternalUsersAjax();
        $departments=ExternalDepartment::all();
        return view('outgoing.add', compact('users','departments'));
    }

    public function getTheLatestExport(){
        $lastRecord = outgoings::orderBy('id', 'desc')->first();
        if(isset($lastRecord)){
            $record=$lastRecord->num;
            $parts = explode('-', $record);

            // Get the last part which is '14'
            $counter = end($parts);
         
        }else{
            $counter = 0;
        }
        return ['counter' => $counter];
    }
    public function generateUniqueNumber($counter)
    {
        //static $counter = 0 ; // Static variable to keep track of the counter

        $today = Carbon::today();
        $formattedDate = $today->year . '-' . $today->month . '-' . $today->day;
        $counter++;  // Increment the counter
        $formattedNumber = $formattedDate . '.' . $counter;
    
        return ['formattedNumber' => $formattedNumber, 'counter' => $counter];
       
    }
    public function store(Request $request)
    {
      
        // Define validation rules
        $rules = [
            'nameex' => 'required|string',
            'num' => 'required|integer',
            'note' => 'nullable|string',
            'person_to' => 'nullable|exists:export_users,id',
            'date' => 'required|date',
            'department_id' => 'nullable|exists:external_departements,id',
            'files.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:20480',
        ];

        // // Define custom messages
        $messages = [
            'nameex.required' => 'عفوا يجب ادخال اسم الصادر',
            'num.required' => 'عفوا يجب ادخال رقم الصادر',
            'note.required' => 'عفوا يجب ادخال ملاحظات الصادر',
            'num.integer' => 'عفوا يجب ان يحتوى رقم الصادر على ارقام فقط',
            'person_to.exists' => 'عفوا هذا المستخدم غير متاح',
            'files.*.mimes' => 'يجب ان تكون الملفات من نوع صور او pdfفقط ',
            'files.*.file' => 'عفو يوجد مشكله فرفع هذاالملف',
            'files.*.max' => 'عفوا حجم الملفات اكبر من المسموح به',

        ];
        $validatedData = Validator::make($request->all(), $rules, $messages);
        // // Validate the request
        if ($validatedData->fails()) {
            return redirect()->back()->withErrors($validatedData)->withInput();
        }
        //dd( $request->validate($rules, $messages));
        // if(auth()->id()){
            $user = User::find(auth()->id());
        $export = new outgoings();
        $export->name = $request->nameex;
        $export->num = $request->num;
        $export->note = $request->note;
        $export->date = $request->date;
        $export->person_to = $request->person_to  ?  $request->person_to :null;
        $export->created_by = 1;//auth $user->id
        $export->created_department = 1;//$user->department_id
        $export->active = $request->active ? $request->active : 0;
        $export->updated_by = 1;//auth auth()->id
        $export->department_id = $request->from_departement;
        $export->save();


        if( $request->hasFile('files') ){

            if (function_exists('UploadFiles')) {
                 //  dd('file yes');
                foreach ($request->file('files') as $file) {
                    $files=new outgoing_files();
                    $files->outgoing_id = $export->id;
                    $files->created_by=1;//auth auth()->id
                    $files->updated_by=1;//auth auth()->id
                    $files->file_type = ($file->getClientOriginalExtension() == 'pdf')? 'pdf' : 'image';
                    $files->active =0;
                    $files->save();
                    $file_model = outgoing_files::find($files->id);

                    UploadFiles('files/export','file_name',  'real_name',$file_model, $file);
                }
            }
        }


        return redirect()->route('Export.index')->with('status', 'تم الاضافه بنجاح');
    // }else{
    //     return redirect()->route('login');

    // }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data=outgoings::with(['personTo', 'createdBy', 'updatedBy'])->findOrFail($id);
        $users=$this->getExternalUsersAjax();
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
        $users=$this->getExternalUsersAjax();
        $is_file = outgoing_files::where('outgoing_id', $id)->where('active',0)->get();
        $departments=ExternalDepartment::all();
        return view('outgoing.editexport', compact('data','users','is_file','departments'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        //dd($request->all());

        // Define validation rules
        $rules = [
            'nameex' => 'required|string',
            'num' => 'required|integer',
            'note' => 'nullable|string',
            'person_to' => 'nullable|exists:export_users,id',
            'date' => 'required|date',
            'department_id' => 'nullable|exists:external_departements,id',
            'files.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:20480',
        ];

        // // Define custom messages
        $messages = [
            'nameex.required' => 'عفوا يجب ادخال اسم الصادر',
            'num.required' => 'عفوا يجب ادخال رقم الصادر',
            'note.required' => 'عفوا يجب ادخال ملاحظات الصادر',
            'num.integer' => 'عفوا يجب ان يحتوى رقم الصادر على ارقام فقط',
            'person_to.exists' => 'عفوا هذا المستخدم غير متاح',
            'files.*.mimes' => 'يجب ان تكون الملفات من نوع صور او pdfفقط ',
            'files.*.file' => 'عفو يوجد مشكله فرفع هذاالملف',
            'files.*.max' => 'عفوا حجم الملفات اكبر من المسموح به',

        ];

        // // Validate the request
        $validatedData = Validator::make($request->all(), $rules, $messages);
        // // Validate the request
       // $request->validate($rules, $messages);
       if ($validatedData->fails()) {
        return redirect()->back()->withErrors($validatedData)->withInput();
    }
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
        $export->active = $request->active ? $request->active : $export->active;
        $export->updated_by = $user->id;//auth auth()->id
        $export->department_id = $request->from_departement;
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
        $file_path = public_path($file->file_name,);
        $file_name =basename($file->real_name,);

        return response()->download($file_path, $file_name);
        //echo 'downloaded';
    }
}