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
        $type[] = array('id' => '1', 'name' => 'السلوك الانضباطى');
        $type[] = array('id' => '2', 'name' => 'مباني');
        $all = ViolationTypes::whereJsonContains('type_id', '79')
            ->orWhereJsonContains('type_id', '80')->count();
        $behavior = ViolationTypes::whereJsonContains('type_id',  '79')->count();
        $buildings = ViolationTypes::whereJsonContains('type_id', '80')->count();
        //  $type=json_encode($type);
        // dd($type);
        return view("ViolationTypes.index", compact('type', 'all', 'behavior', 'buildings'));
    }

    public function getviolations()
    {
        // Start with the query, don't call get() immediately
        $data = ViolationTypes::whereJsonContains('type_id', '79')
            ->orWhereJsonContains('type_id', '80');

        // Apply the filters only if necessary
        $filter = request('filter');

        if ($filter == 'behavior') {
            $data->whereJsonContains('type_id',  '79');
        } elseif ($filter == 'buildings') {
            $data->whereJsonContains('type_id', '80');
        }

        // Fetch the filtered data
        $data = $data->get();

        foreach ($data as $item) {
            $item->type_names = departements::whereIn('id', $item->type_id)
                ->pluck('name')->implode(', ');
        }

        // Return the DataTables response
        return DataTables::of($data)
            ->addColumn('action', function ($row) {
                $name = "$row->name";
                $typesJson = json_encode($row->type_id); // Ensure this is an array

                $edit_permission = '<a class="btn btn-sm" style="background-color: #F7AF15;" onclick="openedit(' . $row->id . ', \'' . $name . '\', \'' . htmlspecialchars($typesJson, ENT_QUOTES, 'UTF-8') . '\')"><i class="fa fa-edit"></i> تعديل</a>';
                return  $edit_permission;
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
        $item->type_id     = $request->types;
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

        // dd($request);
        $request->validate([
            'nameedit' => 'required|string|max:255',
            'types' => 'array'
        ]);

        $ViolationTypes = ViolationTypes::find($request->id);
        $ViolationTypes->name = $request->nameedit;
        $ViolationTypes->type_id = $request->types;
        $ViolationTypes->save();

        return redirect()->route('violations.index')->with('success', 'تم تعديل المخالفه بنجاح');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
