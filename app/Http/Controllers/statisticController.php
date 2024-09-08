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
            $violations = ViolationTypes::all();
        }

        $date = $request->input('date');
        $pointId = $request->input('point');
        $violationTypeId = $request->input('violation');
        $inspectorId = $request->input('inspector');

        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        if ($date && $date != '-1') {
            $missions = InspectorMission::whereDate('date', $date);
            $violationsQuery = Violation::whereDate('created_at', $date);
        } else {
            $missions = InspectorMission::whereBetween('date', [$startOfMonth, $endOfMonth]);
            $violationsQuery = Violation::whereBetween('created_at', [$startOfMonth, $endOfMonth]);
        }
        if ($violationTypeId && $violationTypeId != '-1') {
            
            $violationsQuery->whereRaw("FIND_IN_SET(?, violation_type)", [$violationTypeId]);
        }
        if ($pointId && $pointId != '-1') {
            $missions->whereJsonContains('ids_group_point', $pointId);
            $pointIds = Grouppoint::where('id', $pointId)
                ->where('deleted', 0)
                ->pluck('points_ids')
                ->flatten()
                ->toArray();

            $violationCountByPoints = $violationsQuery->whereIn('point_id', $pointIds)->count();
        } else {
            $violationCountByPoints = $violationsQuery->count();
        }

     

        if ($inspectorId && $inspectorId != '-1') {
            $missions->where('inspector_id', $inspectorId);
            $userId = Inspector::where('id', $inspectorId)->value('user_id');
            if ($userId) {
                $violationsQuery->where('user_id', $userId);
            }
        }

        $inspectorCount = $missions->get()->unique('inspector_id')->count();
        $pointCount = $missions->get()->unique('ids_group_point')->count();
        $violationCount = $violationCountByPoints;  

        $results = [
            'date' => $date && $date != '-1' ? $date : 'الشهر الحالى',
            'violationCount' => $violationCount,
            'inspectorCount' => $inspectorCount,
            'pointCount' => $pointCount,
        ];

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
