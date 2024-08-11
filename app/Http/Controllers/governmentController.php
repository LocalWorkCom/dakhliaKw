<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Groups;
use App\Models\GroupTeam;
use App\Models\Inspector;
use Illuminate\Http\Request;

class governmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $groups_points = Groups::select('id','points_inspector')->get();

        foreach($groups_points as $group_points){
            $Inspectors = Inspector::where('group_id',$group_points->id)->pluck('id')->toArray();

            // $groups_points = GroupTeam::whereRaw('find_in_set(?, inspector_ids)', [$Inspector])->first();

        }
        return view('grouppoints.create1',['groups_points' => $Inspectors]);
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
