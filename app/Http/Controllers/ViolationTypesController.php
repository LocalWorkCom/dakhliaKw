<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\departements;
use App\Models\Violation;
use App\Models\ViolationTypes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class ViolationTypesController extends Controller
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
        /*   $type=array();
        $type[0]['id']='1';
        $type[0]['name']='السلوك الانظباطي';

        $type[1]['id']='2';
        $type[1]['name']='مباني';  */



        $all = ViolationTypes::where('type_id',  '<>', '0')->count();
        // $behavior = ViolationTypes::whereJsonContains('type_id',  '79')->count();
        // $buildings = ViolationTypes::whereJsonContains('type_id', '80')->count();
        //  $type=json_encode($type);

        return view("ViolationTypes.index", compact('all'));
    }

    public function getviolations()
    {
        // Query for violations excluding those with type_id = 0
        $data = ViolationTypes::where('type_id', '<>', '0');

        $filter = request('filter'); // Get filter from request

        if ($filter && $filter != 'all') {
            // Apply filter if provided (assuming type_id is a JSON array)
            $data = $data->whereJsonContains('type_id', $filter);
        }
      //  dd($filter);
        $data = $data->get(); // Fetch the data

        foreach ($data as $item) {
            // Decode the JSON type_id field and fetch corresponding department names
            $typeIds = json_decode($item->type_id, true); // Ensure type_id is parsed as an array
            $item->type_names = Departements::whereIn('id', $typeIds)
                ->pluck('name')
                ->implode(', '); // Join names with commas
        }

        // Return the DataTables response
        return DataTables::of($data)->addColumn('action', function ($row) {
            $name = addslashes($row->name); // Escape the name for safety
            $typesJson = json_encode($row->type_id);
            $edit_permission = null;
            $delete_permission = null;
            if(Auth::user()->hasPermission('edit ViolationTypes')) {
                $edit_permission = "<a class='btn btn-sm' style='background-color: #F7AF15;' onclick='openedit(" . $row->id . ", \"" . $name . "\", " . $typesJson . ")'> <i class='fa fa-edit'></i> تعديل</a>";
            }

            if(Auth::user()->hasPermission('delete ViolationTypes')) {
                $delete_permission = '<a class="btn btn-sm"  style="background-color: #C91D1D;"  onclick="opendelete(' . $row->id . ')">  <i class="fa fa-edit"></i> حذف </a>';
            }

            $uploadButton = $edit_permission . $delete_permission;
            return $uploadButton;

            // $edit_permission = "<a class='btn btn-sm' style='background-color: #F7AF15;' onclick='openedit(" . $row->id . ", \"" . $name . "\", " . $typesJson . ")'> <i class='fa fa-edit'></i> تعديل</a>";
            // return $edit_permission;
        })
        ->addColumn('type_name', function ($row) {
            return $row->type_names;
        })
        ->rawColumns(['action', 'type_name'])
        ->make(true);

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $rules = [
            'nameadd' => 'required|string',
            'types' => 'required|array|exists:departements,id',
        ];

        // // Define custom messages
        $messages = [
            'nameadd.required' => 'يجب ادخال اسم القطاع',
            'nameadd.string' => 'يجب ان لا يحتوى اسم القطاع على رموز',
            'types.required' => 'يجب اختيار قسم واحد على الاقل'
        ];

        // // Validate the request
        $validatedData = Validator::make($request->all(), $rules, $messages);
        // // Validate the request
        // $request->validate($rules, $messages);
        if ($validatedData->fails()) {
            return redirect()->back()->withErrors($validatedData)->withInput();
        }
        $item = new ViolationTypes();
        $item->name = $request->nameadd;
        $item->type_id     = json_encode($request->types);
        $item->created_by = Auth::id();
        $item->updated_by = Auth::id();
        $item->save();
        return redirect()->route('violations.index')->with('message', 'تم أضافه مخالفه جديد');
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
    public function update(Request $request)
    {

         //dd($request);
        $request->validate([
            'nameedit' => 'required|string|max:255',
            'types' => 'array'
        ]);

        $ViolationTypes = ViolationTypes::find($request->id);
        $ViolationTypes->name = $request->nameedit;
        $ViolationTypes->type_id = json_encode($request->types);
        $ViolationTypes->save();

        return redirect()->route('violations.index')->with('success', 'تم تعديل المخالفه بنجاح');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $type = WorkingTime::find($request->id);
        if (!$type) {
            return redirect()->route('working_time.index')->with('reject','يوجد خطا الرجاء المحاولة مرة اخرى');
        }

        $workingTreeTimes = $type->workingTreeTimes()->exists();
        if ($workingTreeTimes) {
            return redirect()->route('working_time.index')->with('reject','لا يمكن حذف هذه فترة العمل يوجد نظام عمل لها');
        }

        $inspectorMissions = $type->inspectorMissions()->exists();
        if ($inspectorMissions) {
            return redirect()->route('working_time.index')->with('reject','لا يمكن حذف هذه فترة العمل يوجد جدول لها');
        }

        $type->delete();
        return redirect()->route('working_time.index')->with('success', 'تم حذف فترة العمل');
    }
}
