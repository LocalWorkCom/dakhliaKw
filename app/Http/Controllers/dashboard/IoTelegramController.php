<?php

namespace App\Http\Controllers\dashboard;

use App\DataTables\IoTelegramDataTable;
use App\Http\Controllers\Controller;
use App\Models\departements;
use App\Models\ExternalDepartment;
use App\Models\io_files;
use App\Models\iotelegrams;
use App\Models\Postman;
use App\Models\User;
use Illuminate\Http\Request;

class IoTelegramController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(IoTelegramDataTable $dataTable)
    {
        return $dataTable->render('iotelegram.index');
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

        $iotelegram = new iotelegrams();
        $iotelegram->type = $request->type;
        $iotelegram->from_departement = $request->from_departement;
        $iotelegram->representive_id = $request->representive_id;
        $iotelegram->date = $request->date;
        $iotelegram->recieved_by = $request->recieved_by;
        $iotelegram->files_num = $request->files_num;
        $iotelegram->created_by = auth()->id();
        // $iotelegram->created_departement = auth()->user()->departement_id;
        $iotelegram->save();
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                // You can modify the UploadFiles function call according to your needs
                $io_file = new io_files();
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

        return redirect()->back();
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        //
        $iotelegram = iotelegrams::find($id);
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
        $iotelegram = iotelegrams::find($id);

        return view('iotelegram.edit', compact('representives', 'departments', 'recieves', 'iotelegram', 'external_departments'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {

        $iotelegram =  iotelegrams::find($id);
        $iotelegram->type = $request->type;
        $iotelegram->from_departement = $request->from_departement;
        $iotelegram->representive_id = $request->representive_id;
        $iotelegram->date = $request->date;
        $iotelegram->recieved_by = $request->recieved_by;
        $iotelegram->files_num = $request->files_num;
        $iotelegram->created_by = auth()->id;
        $iotelegram->save();
        session()->flash('success', 'تم التعديل بنجاح.');

        return redirect()->back();
    }
    public function files($id)
    {
        $io_files = new io_files();
    }

    //postman
    public function addPostmanAjax(Request $request)
    {
        $Postman = new Postman();
        $Postman->name = $request->name;
        $Postman->phone1 = $request->phone1;
        $Postman->phone2 = $request->phone2;
        $Postman->department_id = $request->modal_department_id;

        $Postman->national_id = $request->national_id;
        $Postman->save();
        return true;
    }
    //postman
    public function addExternalDepartmentAjax(Request $request)
    {

        $ExternalDepartment = new ExternalDepartment();
        $ExternalDepartment->name = $request->name;
        $ExternalDepartment->description = $request->desc;
        $ExternalDepartment->phone = $request->phone;
        $ExternalDepartment->save();
        return true;
    }
    public function getExternalDepartments()
    {
        $ExternalDepartments = ExternalDepartment::all();
        return $ExternalDepartments;
    }
    public function getDepartments()
    {
        $Departments = departements::all();
        return $Departments;
    }
    public function getPostmanAjax()
    {
        $postmans = Postman::all();
        return $postmans;
    }
}
