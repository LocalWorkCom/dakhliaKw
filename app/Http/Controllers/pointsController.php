<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Point;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class pointsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view("points.index");
    }
    public function getpoints()
    {
        $data = Point::orderBy('updated_at','desc')->orderBy('created_at','desc')->get();

        return DataTables::of($data)->addColumn('action', function ($row) {
            $name = "'$row->name'";
            $edit_permission=null;
            if(Auth::user()->hasPermission('edit Point')){
                $edit_permission = '<a class="btn btn-sm"  style="background-color: #F7AF15;"  onclick="openedit('.$row->id.','.$name.')">  <i class="fa fa-edit"></i> تعديل </a>';
            }
            return $edit_permission;
        })
        ->rawColumns(['action'])
        ->make(true);
    }
    public function create()
    {
        return view("points.create");
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
