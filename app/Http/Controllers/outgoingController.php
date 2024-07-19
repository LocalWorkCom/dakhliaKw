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
    public function index(outgoingsDataTable $dataTable)
    {
       
        return $dataTable->render('outgoing.viewAll');
       //return view("outgoing.viewAll");
   
    }
    public function uploadFiles($id){
        dd($id);
    }
    public function showFiles($id){
        dd($id);
    }
    public function getExternalUsersAjax()
    {
        $users = exportuser::all();
        return $users;
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
            'note' => 'nullable|string',
            'person_to' => 'nullable|exists:export_users,id',
            'active' => 'required|boolean',
            'department_id' => 'nullable|exists:external_departements,id',
            'files.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf',
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
            'files.*.file' => 'Each file must be a valid file.',
            'files.*.mimes' => 'Each file must be a file of type: jpg, jpeg, png, pdf',
        ];

        // // Validate the request
        $request->validate($rules, $messages);
        //dd( $request->validate($rules, $messages));
        $export = new outgoings();
        $export->name = $request->nameex;
        $export->num = $request->num;
        $export->note = $request->note;
        $export->person_to = $request->person_to  ?  $request->person_to :null;
        $export->created_by = auth()->id();//auth auth()->id
        $export->active = $request->active;
        $export->updated_by = auth()->id();//auth auth()->id
        $export->department_id = $request->from_departement;
        $export->save(); 
        $files=new outgoing_files();
        $files->outgoing_id = $export->id;
        $files->created_by=auth()->id();//auth auth()->id
        $files->updated_by=auth()->id();//auth auth()->id
        $files->active =0;
        $files->save(); 

        $file_model = outgoing_files::find($files->id);
        if( $request->hasFile('files') ){
         
            if (function_exists('UploadFiles')) {
                 //  dd('file yes');
                foreach ($request->file('files') as $file) {
                    UploadFiles('files/export', 'real_name','file_name', $file_model, $file);
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
        $is_file = outgoing_files::where('outgoing_id', $id)->exists();
       
        $departments=ExternalDepartment::all();

        return view('outgoing.show', compact('data','users','is_file','departments'));
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
        return view('outgoing.edit', compact('data','users','is_file','departments'));
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
        
        $export = outgoings::findOrFail( $id );
        $export->name = $request->nameex;
        $export->num = $request->num;
        $export->note = $request->note;
        $export->person_to = $request->person_to  ?  $request->person_to :null;
        $export->created_by = auth()->id();//auth auth()->id
        $export->active = $request->active;
        $export->updated_by = auth()->id();//auth auth()->id
        $export->department_id = $request->department_id;
        $export->created_department =  auth()->;

        $export->save(); 
        // $files=outgoing_files::where('outgoing_id',$id)->get();
        // if(count($files) > 0){
        //     foreach($files as $filedb){
        //         if(!(in_array($request->file, $filedb))){
        //             $filedb->active=1;
        //         }
        //     }
        // $files->outgoing_id = $id;
        // $files->created_by=auth()->id();//auth auth()->id
        // $files->updated_by=auth()->id();//auth auth()->id
        // $files->save(); 
        // $file_model = outgoing_files::find($files->id);
        // }
       
        // if( $request->hasFile('files') ){
         
        //     if (function_exists('UploadFiles')) {
        //          //  dd('file yes');
        //         foreach ($request->file('files') as $file) {
        //            // UploadFiles('files/export', 'real_name','file_name', $file_model, $file);
        //         }
        //     }
        // }
      
        return redirect()->route('Export.index')->with('status', 'تم الاضافه بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
