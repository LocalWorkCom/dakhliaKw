<?php

namespace App\Http\Controllers;

use App\DataTables\outgoingsDataTable;
use App\Http\Controllers\Controller;
use App\Models\exportuser;
use App\Models\ExternalDepartment;
use App\Models\outgoing_files;
use App\Models\outgoings;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;


class outgoingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(outgoingsDataTable $dataTable, Request $request)
    {
       
        $status = $request->get('status', 'active'); // Default to 'active' if not provided
        return $dataTable->with('status', $status)->render('outgoing.viewAll');
       //return view("outgoing.viewAll");
   
    }
    public function getExternalUsersAjax()
    {
        $users = exportuser::all();
        return $users;
    }
    public function addToArchive($id){
        $export = outgoings::find($id);
        $export->active=1;
        $export->save();
        return redirect()->back()->with('success','تم الأضافه الى الارشيف');
    }
    public function showArchive(outgoingsDataTable $dataTable, Request $request){
        $status = $request->get('status', 'inactive'); // Default to 'active' if not provided
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
            'active' => 'required|boolean',
            'department_id' => 'nullable|exists:external_departements,id',
            'files.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf',
        ];

        // // Define custom messages
        $messages = [
            'nameex.required' => 'يجب ادخال عنوان الصادر',
            'num.required' => 'يجب ادخال رقم الصادر',
            'num.integer' => 'يجب ان يكون رقم الصادر ارقام فقط',
            'note.required' =>'يجب ادخال الملاحظات الخاصه بالصادر',
            'person_to.exists' => 'هذا المستخدم ليس متاح ',
            'active.required' => 'يجب ادخال حاله الصادر',
            'department_id.exists' => 'هذا القسم غير متاح',
            'files.*.file' => 'Each file must be a valid file.',
            'files.*.mimes' => 'يجب رفع ملفات من نوع pdf او jpg او png او jepeg',
        ];

        // // Validate the request
        $request->validate($rules, $messages);
        //dd( $request->validate($rules, $messages));
        $user = User::find(auth()->id());
        $export = new outgoings();
        $export->name = $request->nameex;
        $export->num = $request->num;
        $export->note = $request->note;
        $export->date = $request->date;
        $export->person_to = $request->person_to  ?  $request->person_to :null;
        $export->created_department = $user->department_id ;
        $export->created_by = $user->id;//auth auth()->id
        $export->active = $request->active;
        $export->updated_by = $user->id;//auth auth()->id
        $export->department_id = $request->from_departement ?  $request->from_departement :null;
        $export->save(); 
        
        if( $request->hasFile('files') ){
         
            if (function_exists('UploadFiles')) {
                  //dd($request->file('files'));
                foreach ($request->file('files') as $file) {
                    $files=new outgoing_files();
                    $files->outgoing_id = $export->id;
                    $files->created_by=auth()->id();//auth auth()->id
                    $files->updated_by=auth()->id();//auth auth()->id
                    $files->file_type=$file->getClientOriginalExtension();
                    $files->active =0;
                    $files->save(); 

                    $file_model = outgoing_files::find($files->id);
                    UploadFiles('files/export','file_name', 'real_name', $file_model, $file);
                }
            }
        }
      
        return redirect()->route('Export.index')->with('status', 'تم الاضافه بنجاح');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
    
        $data=outgoings::with(['personTo', 'createdBy', 'updatedBy'])->findOrFail($id);
        $users=User::all();
        $is_file = outgoing_files::where('outgoing_id', $id)->get();
       
        $departments=ExternalDepartment::all();
        //dd(view('outgoing.show'));
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
       //dd($request->all());
        // Define validation rules
        $rules = [
            'nameex' => 'required|string',
            'num' => 'required|integer',
            'note' => 'nullable|string',
            'person_to' => 'nullable|exists:users,id',
            'active' => 'required|boolean',
            'department_id' => 'nullable|exists:external_departements,id',
        ];

        // // Define custom messages
        $messages = [
            'nameex.required' => 'The name field is required.',
            'nameex.string' => 'The name must be a string.',
            'num.required' => 'The number field is required.',
            'num.integer' => 'The number must be an integer.',
            'person_to.exists' => 'The selected person does not exist.',
            'active.required' => 'The active field is required.',
            'active.boolean' => 'The active field must be true or false.',
            'department_id.exists' => 'The selected department does not exist.',
        ];

        // // Validate the request
        $request->validate($rules, $messages);
        $user=User::findOrFail(auth()->id());
        $export = outgoings::findOrFail( $id );
        $export->name = $request->nameex;
        $export->num = $request->num;
        $export->note = $request->note;
        $export->date = $request->date;
        $export->person_to = $request->person_to  ?  $request->person_to :null;
        $export->created_by = auth()->id();//auth auth()->id
        $export->active = $request->active;
        $export->updated_by = auth()->id();//auth auth()->id
        $export->department_id = $request->department_id;
        $export->created_department =  $user->department_id;

        $export->save(); 
        if( $request->hasFile('files') ){
         
            if (function_exists('UploadFiles')) {
                  //dd($request->file('files'));
                foreach ($request->file('files') as $file) {
                    $files=new outgoing_files();
                    $files->outgoing_id = $export->id;
                    $files->created_by=auth()->id();//auth auth()->id
                    $files->updated_by=auth()->id();//auth auth()->id
                    $files->active =0;
                    $files->file_type=$file->getClientOriginalExtension();
                    $files->save(); 

                    $file_model = outgoing_files::find($files->id);
                    UploadFiles('files/export','file_name', 'real_name', $file_model, $file);
                }
            }
        }
      
        return redirect()->route('Export.index')->with('status', 'تم الاضافه بنجاح');
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
