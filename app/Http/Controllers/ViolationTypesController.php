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
        $type[] = array('id' => '1', 'name' => 'السلوك الانظباطي');
        $type[] = array('id' => '2', 'name' => 'مباني');
        $all = Violation::whereNotNull('flag')->count();
        $behavior = Violation::where('flag', 1)->count();
        $buildings = Violation::where('flag', 0)->count();
        //  $type=json_encode($type);
        // dd($type);
        return view("ViolationTypes.index", compact('type', 'all', 'behavior', 'buildings'));
    }

    public function getviolations(Request $request)
    {
        // Get the total number of violation types without filtering
        $totalRecords = ViolationTypes::where('type_id', '!=', '0')->count();

        // Apply filtering based on the 'flag' from the Violations
        $filter = $request->get('filter');
        $data = ViolationTypes::where('type_id', '!=', '0');

        if ($filter == 'behavior') {
            $data->whereHas('violations', function ($query) {
                $query->where('flag', 1);
            });
        } elseif ($filter == 'buildings') {
            $data->whereHas('violations', function ($query) {
                $query->where('flag', 0);
            });
        }

        // Get the filtered data count
        $filteredRecords = $data->count();

        // Fetch the paginated data for DataTables
        $data = $data->skip($request->start)
            ->take($request->length)
            ->get();

        // Add department names for each violation type
        foreach ($data as $item) {
            $item->type_names = departements::whereIn('id', $item->type_id)->pluck('name')->implode(', ');
        }

        return DataTables::of($data)
            ->setTotalRecords($totalRecords)
            ->setFilteredRecords($filteredRecords)
            ->addColumn('action', function ($row) {
                $name = "$row->name";
                $typesJson = json_encode($row->type_id);
                return '<a class="btn btn-sm" style="background-color: #F7AF15;" onclick="openedit(' . $row->id . ', \'' . $name . '\', \'' . htmlspecialchars($typesJson, ENT_QUOTES, 'UTF-8') . '\')"><i class="fa fa-edit"></i> تعديل</a>';
            })
            ->addColumn('type_name', function ($row) {
                return departements::whereIn('id', $row->type_id)->pluck('name')->implode(', ');
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
