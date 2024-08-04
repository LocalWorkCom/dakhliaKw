<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Sector;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;


class sectorsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view("sectors.index");
    }

    public function getsectors()
    {
        $data =  Sector::join('governments', 'sectors.government_id', '=', 'governments.id')->select('sectors.*', 'governments.name as government_name')->orderBy('updated_at','desc')->orderBy('created_at','desc')->get();

        return DataTables::of($data)->addColumn('action', function ($row) {
            $name = "'$row->name'";
            $edit_permission=null;
            $region_permission=null;
            if(Auth::user()->hasPermission('edit Sector')){
                $edit_permission = '<a class="btn btn-sm"  style="background-color: #F7AF15;" >  <i class="fa fa-edit"></i> تعديل </a>';
            }
            return $edit_permission ;
        })
        ->addColumn('government_name', function ($row) {
            return $row->government_name;
        })
        ->rawColumns(['action'])
        ->make(true);
    }
    public function create()
    {
        return view('sectors.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
       dd($request->all());
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
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
