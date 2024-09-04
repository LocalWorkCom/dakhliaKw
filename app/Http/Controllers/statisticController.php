<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Grouppoint;
use App\Models\Inspector;
use App\Models\InspectorMission;
use App\Models\Point;
use App\Models\Violation;
use App\Models\ViolationTypes;
use Carbon\Carbon;
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

        $results = null;
        return view('statistics.index', compact('inspectors', 'points', 'violations', 'results'));
    }

    public function getFilteredData(Request $request)
    {
        if (Auth::user()->rule->name == "localworkadmin" || Auth::user()->rule->name == "superadmin") {
            $inspectors = Inspector::all();
            $points = Grouppoint::where('deleted', 0)->get();
            $violations = ViolationTypes::all();
        } else {
            $inspectors = Inspector::where('department_id', Auth::user()->department_id)->get();
            $points = collect();
        }

        $date = $request->input('date');
        $pointId = $request->input('point');
        $violationTypeId = $request->input('violation');
        $inspectorId = $request->input('inspector');
        $userId = Inspector::where('id', $inspectorId)->value('user_id');
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
        if (isset($date) && $date != '-1') {
            $missions = InspectorMission::whereDate('date', $date);
            $violation = Violation::whereDate('created_at', $date);
        } else {
            $missions = InspectorMission::whereBetween('date', [$startOfMonth, $endOfMonth]);
            $violation = Violation::whereBetween('created_at', [$startOfMonth, $endOfMonth]);
        }
        if ($pointId && $pointId != '-1') {
            $group = Grouppoint::whereJsonContains('points_ids', $pointId)->select('id','points_ids');
            
            $missions->whereJsonContains('ids_group_point', $group);
            $violation->where('point_id', $pointId);
        } else {
            // Calculate pointCount without filtering by pointId
            $group = Grouppoint::whereJsonContains('points_ids', $pointId)->value('id');
            $missions->whereJsonContains('ids_group_point', $group);
        }
        if (isset($inspectorId) && $inspectorId != '-1') {
            $missions->where('inspector_id', $inspectorId);
            $violation->where('user_id', $userId);
        }
       
        
        $inspectorCount = $missions->distinct('inspector_id')->count('inspector_id');
        $violationCount = $violation->count();
        $pointCount = $missions->distinct('ids_group_point')->count('ids_group_point');
        
        // Prepare results array
        $results = [
            'date' => isset($date) && $date != '-1' ? $date : 'This Month',
            'violationCount' => $violationCount,
            'inspectorCount' => $inspectorCount,
            'pointCount' => $pointCount,
        ];

        // Pass data to the view
        return view('statistics.index', compact('inspectors', 'points', 'violations', 'results'));
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
