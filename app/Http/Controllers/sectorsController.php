<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Sector;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

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
        $data =  Sector::orderBy('updated_at','desc')->orderBy('created_at','desc')->get();

        return DataTables::of($data)->addColumn('action', function ($row) {
            $name = "'$row->name'";
            $edit_permission=null;
            $region_permission=null;
            if(Auth::user()->hasPermission('edit Sector')){
                $edit_permission = '<a class="btn btn-sm"  style="background-color: #F7AF15;"  onclick="openedit('.$row->id.','.$name.')">  <i class="fa fa-edit"></i> تعديل </a>';
            }
            // if(Auth::user()->hasPermission('view Region')){
            //     $region_permission = '<a class="btn btn-sm"  style="background-color: #b77a48;"  href="'.route('regions.index',['id' => $row->id ]).'"> <i class="fa-solid fa-mountain-sun"></i> مناطق </a>';
            // }
            return $edit_permission ;

            // <a class="btn btn-primary btn-sm" href=' . route('government.show', $row->id) . '>التفاصيل</a>
        })
        ->rawColumns(['action'])
        ->make(true);
    }
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
