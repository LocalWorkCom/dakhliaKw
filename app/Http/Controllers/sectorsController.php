<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Government;
use App\Models\Sector;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;


class sectorsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $governmentIds = Government::pluck('id')->toArray(); // Get all government IDs
        $sectors = Sector::all(); // Retrieve all sectors
        
        $sectorGovernmentIds = []; // Initialize an array to hold the sector government IDs
        
        foreach ($sectors as $sector) {
            // Merge the current sector's government IDs into the $sectorGovernmentIds array
            $sectorGovernmentIds = array_merge($sectorGovernmentIds, $sector->governments_IDs);
        }
            // dd($governmentIds);
        // Now $sectorGovernmentIds contains all the IDs from all sectors
        
        // Check if all sector government IDs exist in the government IDs list
        $allExist = !array_diff($governmentIds, $sectorGovernmentIds);
        
         //dd($allExist);
        return view("sectors.index", compact('allExist'));
    }

    public function getsectors()
    {
        $data = Sector::all();

        foreach ($data as $sector) {
            $sector->government_names = Government::whereIn('id', $sector->governments_IDs)->pluck('name')->implode(', ');
        }

        return DataTables::of($data)
            ->addColumn('action', function ($row) {
                // $edit_permission = null;
                // $show_permission = null ;
                // if (Auth::user()->hasPermission('edit Sector')) {
                $edit_permission = '<a class="btn btn-sm" style="background-color: #F7AF15;"  href=' . route('sectors.edit', $row->id) . '><i class="fa fa-edit"></i> تعديل</a>';
                // }
                // if (Auth::user()->hasPermission('view Sector')) {
                $show_permission = '<a class="btn btn-sm" style="background-color: #274373;"  href=' . route('sectors.show', $row->id) . '> <i class="fa fa-eye"></i>عرض</a>';
                // }
                return $show_permission . ' ' . $edit_permission;
            })
            ->addColumn('government_name', function ($row) {
                return $row->government_names;
            })
            ->rawColumns(['action'])
            ->make(true);
    }


    public function create()
    {
        $associatedGovernmentIds = Sector::query()
            ->pluck('governments_IDs')
            ->flatten()
            ->unique()
            ->toArray();
        //dd($associatedGovernmentIds);

        // if(isset($associatedGovernmentIds)){
        $unassociatedGovernments = Government::query()
            ->whereNotIn('id', $associatedGovernmentIds)
            ->get();
        //dd($unassociatedGovernments);
        // }
        return view('sectors.create', ['governments' => $unassociatedGovernments]);

        // return view('sectors.create',compact('governments'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //dd($request->all());
        $rules = [
            'name' => 'required|string',
            'governmentIDS' => 'required|array|exists:governments,id',
        ];

        // // Define custom messages
        $messages = [
            'name.required' => 'يجب ادخال اسم القطاع',
            'name.string' => 'يجب ان لا يحتوى اسم القطاع على رموز',
            'governmentIDS.required' => 'يجب اختيار محافظه واحده على الاقل'
        ];

        // // Validate the request
        $validatedData = Validator::make($request->all(), $rules, $messages);
        // // Validate the request
        // $request->validate($rules, $messages);
        if ($validatedData->fails()) {
            return redirect()->back()->withErrors($validatedData)->withInput();
        }
        $sector = new Sector();
        $sector->name = $request->name;
        $sector->governments_IDs = $request->governmentIDS;
        $sector->created_by = Auth::id();
        $sector->updated_by = Auth::id();
        $sector->save();
        return redirect()->route('sectors.index')->with('message', 'تم أضافه قطاع جديد');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data = Sector::find($id);
        $checkedGovernments = array_flip($data->governments_IDs);
        return view('sectors.showdetails', compact('data', 'checkedGovernments'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // Find the sector being edited
        $data = Sector::findOrFail($id);

        // Retrieve all government IDs associated with any sector except the current sector
        $associatedGovernmentIds = Sector::query()
            ->where('id', '!=', $data->id)
            ->pluck('governments_IDs')
            ->flatten()
            ->unique()
            ->toArray();

        // Retrieve governments not associated with any sector
        $unassociatedGovernments = Government::query()
            ->whereNotIn('id', $associatedGovernmentIds)
            ->get();

        // Retrieve governments associated with the current sector
        $currentSectorGovernments = Government::query()
            ->whereIn('id', $data->governments_IDs)
            ->get();

        // Merge the current sector's governments with the unassociated governments
        $governments = $currentSectorGovernments->merge($unassociatedGovernments);

        return view('sectors.edit', [
            'data' => $data,
            'governments' => $governments,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        //dd($request->all());
        $rules = [
            'name' => 'required|string',
            'governmentIDS' => 'required|array|exists:governments,id',
        ];

        // // Define custom messages
        $messages = [
            'name.required' => 'يجب ادخال اسم القطاع',
            'name.string' => 'يجب ان لا يحتوى اسم القطاع على رموز',
            'governmentIDS.required' => 'يجب اختيار محافظه واحده على الاقل'
        ];
        // // Validate the request
        $validatedData = Validator::make($request->all(), $rules, $messages);
        // // Validate the request
        if ($validatedData->fails()) {
            return redirect()->back()->withErrors($validatedData)->withInput();
        }
        $sector = Sector::find($request->id);
        $sector->name = $request->name;
        $sector->governments_IDs = $request->governmentIDS;
        // $sector->created_by = Auth::id();
        // $sector->updated_by = Auth::id();
        $sector->save();
        return redirect()->route('sectors.index')->with('message', 'تم تعديل القطاع ');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
