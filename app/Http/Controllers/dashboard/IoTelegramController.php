<?php

namespace App\Http\Controllers\dashboard;

use App\DataTables\IoTelegramDataTable;
use App\Http\Controllers\Controller;
use App\Models\departements;
use App\Models\ExternalDepartment;
use App\Models\Io_file;
use App\Models\Iotelegram;
use App\Models\Postman;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class IoTelegramController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('iotelegram.index');
    }
    public function archives()
    {
        return view('iotelegram.archive');
    }
    public function getArchives()
    {
        $IoTelegrams = Iotelegram::where('active', 0)->with('created_by', 'recieved_by', 'representive', 'updated_by', 'created_department', 'internal_department', 'external_department')
            ->orderBy('created_at', 'desc')
            ->get();

        foreach ($IoTelegrams as  $IoTelegram) {
            $IoTelegram['department'] = ($IoTelegram->type == 'in') ?
                $IoTelegram->internal_department->name :
                $IoTelegram->external_department->name;
            $IoTelegram['archives'] = CheckUploadIoFiles($IoTelegram->id);
            $IoTelegram['type'] = ($IoTelegram->type == 'in') ? 'داخلي' : 'خارجي';
        }
        return DataTables::of($IoTelegrams)
            ->rawColumns(['action'])
            ->make(true);
    }
    public function getIotelegrams()
    {
        $IoTelegrams = Iotelegram::with('created_by', 'recieved_by', 'representive', 'updated_by', 'created_department', 'internal_department', 'external_department')
            ->orderBy('created_at', 'desc')
            ->get();

        foreach ($IoTelegrams as  $IoTelegram) {
            $IoTelegram['department'] = ($IoTelegram->type == 'in') ?
                $IoTelegram->internal_department->name :
                $IoTelegram->external_department->name;
            $IoTelegram['archives'] = CheckUploadIoFiles($IoTelegram->id);
            $IoTelegram['type'] = ($IoTelegram->type == 'in') ? 'داخلي' : 'خارجي';
        }
        return DataTables::of($IoTelegrams)
            ->rawColumns(['action'])
            ->make(true);
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        $representives = Postman::all();
        $recieves = User::all();
        $departments = departements::all();
        $external_departments = ExternalDepartment::all();
        return view('iotelegram.add', compact('representives', 'departments', 'recieves', 'external_departments'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        if ($request->hasFile('files')) {
            $request->validate([
                'files.*' => 'mimes:jpeg,png,pdf|max:2048', // Adjust validation rules as needed
            ]);
        }

        $iotelegram = new Iotelegram();
        $iotelegram->type = $request->type;
        $iotelegram->from_departement = $request->from_departement;
        $iotelegram->representive_id = $request->representive_id;
        $iotelegram->date = $request->date;
        $iotelegram->recieved_by = $request->recieved_by;
        $iotelegram->created_by = auth()->id();
        $iotelegram->created_departement = auth()->user()->department_id;
        $iotelegram->save();
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                // You can modify the UploadFiles function call according to your needs
                $io_file = new Io_file();
                $io_file->iotelegram_id = $iotelegram->id;
                $io_file->created_by = auth()->id();
                $io_file->updated_by = auth()->id();
                $io_file->save();
                if ($iotelegram->type == 'in') {
                    $path = 'io_files/internal';
                } else {
                    $path = 'io_files/external';
                }
                if ($file->getClientOriginalExtension() == 'pdf') {
                    $io_file->file_type = 'pdf';
                } else {
                    $io_file->file_type = 'image';
                }
                UploadFiles($path, 'file_name', 'real_name', $io_file, $file);
            }
        }
        session()->flash('success', 'تم الحفظ بنجاح.');

        return redirect()->route('iotelegrams.list');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        //
        $iotelegram = Iotelegram::with('created_by', 'recieved_by', 'representive', 'updated_by', 'created_department', 'internal_department', 'external_department')->find($id);
        $representives = Postman::all();
        $recieves = User::all();
        $departments = departements::all();
        $external_departments = ExternalDepartment::all();

        return view('iotelegram.show', compact('iotelegram', 'representives', 'departments', 'recieves', 'external_departments'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        //
        $representives = Postman::all();
        $recieves = User::all();
        $departments = departements::all();
        $external_departments = ExternalDepartment::all();
        $iotelegram = Iotelegram::with('created_by', 'recieved_by', 'representive', 'updated_by', 'created_department', 'internal_department', 'external_department')->find($id);

        return view('iotelegram.edit', compact('representives', 'departments', 'recieves', 'iotelegram', 'external_departments'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {

        $iotelegram =  Iotelegram::find($id);
        $iotelegram->type = $request->type;
        $iotelegram->from_departement = $request->from_departement;
        $iotelegram->representive_id = $request->representive_id;
        $iotelegram->date = $request->date;
        $iotelegram->recieved_by = $request->recieved_by;
        $iotelegram->created_by = auth()->id();
        $iotelegram->created_departement = auth()->user()->department_id;

        $iotelegram->save();
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                // You can modify the UploadFiles function call according to your needs
                $io_file = new Io_file();
                $io_file->iotelegram_id = $iotelegram->id;
                $io_file->created_by = auth()->id();
                $io_file->updated_by = auth()->id();
                $io_file->save();
                if ($iotelegram->type == 'in') {
                    $path = 'io_files/internal';
                } else {
                    $path = 'io_files/external';
                }
                if ($file->getClientOriginalExtension() == 'pdf') {
                    $io_file->file_type = 'pdf';
                } else {
                    $io_file->file_type = 'image';
                }
                $io_file->save();

                UploadFiles($path, 'file_name', 'real_name', $io_file, $file);
            }
        }
        session()->flash('success', 'تم التعديل بنجاح.');

        return redirect()->route('iotelegrams.list');
    }

    //postman
    public function addPostmanAjax(Request $request)
    {
        $rules = [
            'name' => 'required',
            'phone1' => 'required|unique:postmans,phone1',
            'phone2' => 'unique:postmans,phone2',
            'national_id' => 'required|unique:postmans,national_id',
            'modal_department_id' => 'required',

        ];

        $messages = [
            'name.string' => 'يجب ان يكون الأسم حروف فقط',
            'name.required' => 'يجب ادخال اسم الشخص',

            'phone1.required' => 'يجب ادخال الهاتف',
            'phone1.integer' => 'يجب ان يكون الهاتف ارقام',
            'phone1.unique' => 'رقم الهاتف 1 موجود بالفعل',


            'phone2.required' => 'يجب ادخال الهاتف',
            'phone2.integer' => 'يجب ان يكون الهاتف ارقام',
            'phone1.unique' => 'رقم الهاتف 2 موجود بالفعل',


            'national_id.required' => 'يجب ادخال رقم الهوية',
            'national_id.integer' => 'يجب ان يكون رقم الهوية ارقام',
            'national_id.unique' => 'رقم الهوية موجود بالفعل',

            'modal_department_id.required' => 'يجب ادخال اسم الادارة'

        ];
        $validatedData = Validator::make($request->all(), $rules, $messages);

        if ($validatedData->fails()) {
            return response()->json(['success' => false, 'message' => $validatedData->errors()]);
        }
 

        $Postman = new Postman();
        $Postman->name = $request->name;
        $Postman->phone1 = $request->phone1;
        $Postman->phone2 = $request->phone2;
        $Postman->department_id = $request->modal_department_id;
        $Postman->national_id = $request->national_id;
        $Postman->save();

        return response()->json([
            'success' => true,
            'message' => 'Postman created successfully',
            'postman' => $Postman // Optionally return the created postman object
        ], 201); // HTTP 201 Created
    }


    //external department
    public function addExternalDepartmentAjax(Request $request)
    {
        $rules = [
            'desc' => 'nullable',
            'phone' => 'required|integer',
            'name' => 'required|string',
        ];

        $messages = [
            'name.string' => 'يجب ان يكون الأسم حروف فقط',
            'phone.required' => 'يجب ادخال الهاتف',
            'phone.integer' => 'يجب ان يكون الهاتف ارقام',
            'name.required' => 'يجب ادخال اسم الشخص',
        ];

        $validatedData = Validator::make($request->all(), $rules, $messages);

        if ($validatedData->fails()) {
            return response()->json(['success' => false, 'message' => $validatedData->errors()]);
        }

        $ExternalDepartment = new ExternalDepartment();
        $ExternalDepartment->name = $request->name;
        $ExternalDepartment->description = $request->desc;
        $ExternalDepartment->phone = $request->phone;
        $ExternalDepartment->save();
        return response()->json(['success' => true]);
    }
    //update ajax 
    public function getExternalDepartments()
    {
        $ExternalDepartments = ExternalDepartment::orderBy('created_at', 'desc')->get();
        return $ExternalDepartments;
    }

    public function getDepartments()
    {
        $Departments = departements::all();
        return $Departments;
    }
    //update ajax 

    public function getPostmanAjax()
    {
        $postmans = Postman::all();
        return $postmans;
    }
    public function downlaodfile($id)
    {
        $file = Io_file::find($id);
        // $download=downloadFile($file->file_name,$file->real_name);
        $file_path = public_path($file->file_name);
        $file_name = basename($file->real_name);

        return response()->download($file_path, $file_name);
        //echo 'downloaded';
    }
    public function AddArchive($id)
    {
        $iotelegram = Iotelegram::find($id);
        $iotelegram->active = 0;
        $iotelegram->save();

        session()->flash('success', 'تم الارشفة بنجاح.');

        return redirect()->route('iotelegrams.list');
    }
}
