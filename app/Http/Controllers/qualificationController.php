<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Qualifications;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class qualificationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view("qualifications.index");
    }

    public function getqualification(){
        $data = Qualifications::orderBy('updated_at','desc')->orderBy('created_at','desc')->get();

        return DataTables::of($data)
        ->rawColumns(['action'])
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
