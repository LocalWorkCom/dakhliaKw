<?php

namespace App\Http\Controllers;

use App\DataTables\outgoingsDataTable;
use App\Http\Controllers\Controller;
use App\Models\ExternalDepartment;
use App\Models\outgoing_files;
use App\Models\outgoings;
use App\Models\User;
use Illuminate\Http\Request;
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
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
       
        $users=User::all();
        $departments=ExternalDepartment::all();
        return view('outgoing.add', compact('users','departments'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function storeDepartment(Request $request){
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'desc' => 'nullable|string',
            'phone' => 'nullable|string',
        ]);
    
        $department = ExternalDepartment::create($validatedData);
    
        return response()->json([
            'success' => true,
            'id' => $department->id,
            'name' => $department->name,
        ]);
    }
    public function store(Request $request)
    {
        //dd($request->all());
        // Define validation rules
        $rules = [
            'name' => 'required|string|max:255',
            'num' => 'required|integer',
            'note' => 'nullable|string',
            'person_to' => 'nullable|exists:users,id',
            'active' => 'required|boolean',
            'department' => 'nullable|exists:external_departements,id',
            'file.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf',
        ];

        // // Define custom messages
        $messages = [
            'name.required' => 'The name field is required.',
            'name.string' => 'The name must be a string.',
            'name.max' => 'The name may not be greater than 255 characters.',
            'num.required' => 'The number field is required.',
            'num.integer' => 'The number must be an integer.',
            'person_to.exists' => 'The selected person does not exist.',
            'active.required' => 'The active field is required.',
            'active.boolean' => 'The active field must be true or false.',
            'department.exists' => 'The selected department does not exist.',
            'file.*.file' => 'Each file must be a valid file.',
            'file.*.mimes' => 'Each file must be a file of type: jpg, jpeg, png, pdf',
        ];

        // // Validate the request
        $request->validate($rules, $messages);
        
        $export = new outgoings();
        $export->name = $request->name;
        $export->num = $request->num;
        $export->note = $request->note;
        $export->person_to = $request->person_to  ?  $request->person_to :null;
        $export->created_by = 1;//auth auth()->id
        $export->active = $request->active;
        $export->updated_by = 1;//auth auth()->id
        $export->department_id = $request->department;
        $export->save(); 
        $files=new outgoing_files();
        $files->outgoing_id = $export->id;
        $files->created_by=1;//auth auth()->id
        $files->updated_by=1;//auth auth()->id
        $files->active =1;
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
      
        return redirect()->back()->with('success','');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data=outgoings::with(['personTo', 'createdBy', 'updatedBy'])->findOrFail($id);
        $users=User::all();
        $is_file = outgoing_files::where('outgoing_id', $id)->exists();
        return view('outgoing.show', compact('data','users','is_file'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $data=outgoings::with(['personTo', 'createdBy', 'updatedBy'])->findOrFail($id);
        $users=User::all();
        $is_file = outgoing_files::where('outgoing_id', $id)->exists();
        return view('outgoing.edit', compact('data','users','is_file'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
       //dd($request->all());
        // Define validation rules
        $rules = [
            'name' => 'required|string|max:255',
            'num' => 'required|integer',
            'note' => 'nullable|string',
            'person_to' => 'nullable|exists:users,id',
            'active' => 'required|boolean',
            'department' => 'nullable|exists:external_departements,id',
            'file.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf',
        ];

        // // Define custom messages
        $messages = [
            'name.required' => 'The name field is required.',
            'name.string' => 'The name must be a string.',
            'name.max' => 'The name may not be greater than 255 characters.',
            'num.required' => 'The number field is required.',
            'num.integer' => 'The number must be an integer.',
            'person_to.exists' => 'The selected person does not exist.',
            'active.required' => 'The active field is required.',
            'active.boolean' => 'The active field must be true or false.',
            'department.exists' => 'The selected department does not exist.',
            'file.*.file' => 'Each file must be a valid file.',
            'file.*.mimes' => 'Each file must be a file of type: jpg, jpeg, png, pdf',
        ];

        // // Validate the request
        $request->validate($rules, $messages);
        if( !empty($request->department) ){
            $department = new ExternalDepartment();
            $department->name = $request->name_depart;
            $department->description = $request->description_depart;
            $department->phone = $request->phone_depart;
            $department->created_by  =1;//auth auth()->id
            $department->updated_by  = 1;//auth auth()->id
            $department->save();
        }
            
        $export = new outgoings();
        $export->name = $request->name;
        $export->num = $request->num;
        $export->note = $request->note;
        $export->person_to = $request->person_to  ?  $request->person_to :null;
        $export->created_by = auth()->id;//
        $export->active = $request->active;
        $export->updated_by = auth()->id;//auth auth()->id
        $export->department_id = null;
        $export->save(); 
        $files=new outgoing_files();
        $files->outgoing_id = $export->id;
        $files->created_by=auth()->id;//auth auth()->id
        $files->updated_by=auth()->id;//auth auth()->id
        $files->active =1;
        $files->save(); 

        $file_model = outgoing_files::find($files->id);
        if( $request->hasFile('files') ){
         
            if (function_exists('UploadFiles')) {
                 //  dd('file yes');
                foreach ($request->file('files') as $file) {
                   // UploadFiles('files/export', 'real_name','file_name', $file_model, $file);
                }
            }
        }
      
        return redirect()->back()->with('success','');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
