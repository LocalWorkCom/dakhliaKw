<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Grouppoint;
use App\Models\Inspector;
use App\Models\InspectorMission;
use App\Models\Violation;
use App\Models\ViolationTypes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class statisticController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (Auth::user()->rule->name == "localworkadmin" || Auth::user()->rule->name == "superadmin") {
            $inspectors = Inspector::all();
            $points = Grouppoint::where('deleted', 0)->get();
            $violations = ViolationTypes::all();
        } else {
            $inspectors = Inspector::where('department_id', Auth()->user()->department_id)->get();
        }
        return view('statistics.index', compact('inspectors', 'points', 'violations'));
    }
    public function getFilteredData(Request $request)
    {
        $date = $request->input('date');
        $points = $request->input('points');
        $violation = $request->input('violation');
        $inspectors = $request->input('inspectors');

        $violations = Violation::query();

        if ($date) {
            $violations->whereDate('created_at', $date);
        }

        if ($points && $points != -1) {
            $violations->with(['point'])->where('point_id', $points);
        }

        if ($violation && $violation != -1) {
            $violations->where('violation_type_id', $violation);
        }

        if ($inspectors && $inspectors != -1) {
            $violations->where('inspector_id', $inspectors);
        }

        $violationData = $violations->get();
   
        return redirect()->back()->with('');
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
