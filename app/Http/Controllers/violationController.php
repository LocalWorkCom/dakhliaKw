<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\departements;
use App\Models\Violation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class violationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view("violations.index");
    }

    public function getviolations()
    {
        $data = Violation::all();

        foreach ($data as $item) {
            $item->type_names = departements::whereIn('id', $item->type_id)->pluck('name')->implode(', ');
        }

        return DataTables::of($data)
            ->addColumn('action', function ($row) {
                $name = "'$row->name'";
                $typesJson = json_encode($row->type_id); // Ensure this is an array
$edit_permission = '<a class="btn btn-sm" style="background-color: #F7AF15;" onclick="openedit(' . $row->id . ', \'' . $name. '\', \'' . htmlspecialchars($typesJson, ENT_QUOTES, 'UTF-8') . '\')"><i class="fa fa-edit"></i> تعديل</a>';

                // $edit_permission = null;
                // $show_permission = null ;
                // if (Auth::user()->hasPermission('edit item')) {
                    // $edit_permission = '<a class="btn btn-sm" style="background-color: #F7AF15;" onclick="openedit(' . $row->id . ', \'' . addslashes($row->name) . '\', ' . htmlspecialchars(json_encode($row->type_id), ENT_QUOTES, 'UTF-8') . ')"><i class="fa fa-edit"></i> تعديل</a>';
                    // }
                // if (Auth::user()->hasPermission('view item')) {
                $show_permission = '<a class="btn btn-sm" style="background-color: #274373;"  href=' . route('violations.show', $row->id) . '> <i class="fa fa-eye"></i>عرض</a>';
                // }
                return $show_permission . ' ' . $edit_permission;
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
        //dd($request->all());
        $rules = [
            'name' => 'required|string',
            'types' => 'required|array|exists:departements,id',
        ];

        // // Define custom messages
        $messages = [
            'name.required' => 'يجب ادخال اسم القطاع',
            'name.string' => 'يجب ان لا يحتوى اسم القطاع على رموز',
            'types.required' => 'يجب اختيار قسم واحد على الاقل'
        ];

        // // Validate the request
        $validatedData = Validator::make($request->all(), $rules, $messages);
        // // Validate the request
        // $request->validate($rules, $messages);
        if ($validatedData->fails()) {
            return redirect()->back()->withErrors($validatedData)->withInput();
        }
        $item = new Violation();
        $item->name = $request->name;
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
      
    $request->validate([
        'name' => 'required|string|max:255',
        'types' => 'array'
    ]);

    $violation = Violation::find($request->id);
    $violation->name = $request->name;
    $violation->type_id = $request->types;
    $violation->save();

        return redirect()->route('violations.index')->with('success', 'Violation updated successfully.');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
