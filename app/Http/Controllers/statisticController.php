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
use Illuminate\Support\Facades\DB;

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
            $points = collect(); // Ensure $points is always defined
        }

        $violationData = null;
        return view('statistics.index', compact('inspectors', 'points', 'violations', 'violationData'));
    }

    public function getFilteredData(Request $request)
    {
        if (Auth::user()->rule->name == "localworkadmin" || Auth::user()->rule->name == "superadmin") {
            $inspectors = Inspector::all();
            $points = Grouppoint::where('deleted', 0)->get();
            $violations = ViolationTypes::all();
        } else {
            $inspectors = Inspector::where('department_id', Auth()->user()->department_id)->get();
            $points = collect();
        }

        $date = $request->input('date');
        $pointsId = $request->input('points');
        $violation_type = $request->input('violation');
        $inspector = $request->input('inspectors');

        $violation = Violation::query();
        $user_id=Inspector::where('id',$inspector)->value('user_id');
        if ($date) {
            $violation->whereDate('created_at', $date);
        }

        if ($pointsId && $pointsId != -1) {
            $violation->where('point_id', $pointsId);
        }

        if ($violation_type && $violation_type != -1) {
            $violationIds = explode(',', $violation_type);
            $violation->whereIn('violation_type', $violationIds);
        }

        if ($inspector && $inspector != -1) {
            $violation->where('user_id', $user_id);
        }
        $violationData = $violation
        ->select('point_id', 'violation_type', 'user_id', DB::raw('count(*) as count'))
        ->groupBy('point_id', 'violation_type', 'user_id')
        ->get();
        return view('statistics.index', compact('inspectors', 'points', 'violations', 'violationData'))->render();
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
