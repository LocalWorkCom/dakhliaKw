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
        // Fetch necessary data based on user role
        [$inspectors, $points, $violations] = $this->fetchStatisticsData();

        $results = null;
        return view('statistics.index', compact('inspectors', 'points', 'violations', 'results'));
    }

    public function getFilteredData(Request $request)
    {
        // Fetch necessary data based on user role
        [$inspectors, $points, $violations] = $this->fetchStatisticsData();

        // Get filter inputs from the request
        $date = $request->input('date');
        $pointId = $request->input('point');
        $violationTypeId = $request->input('violation');
        $inspectorId = $request->input('inspector');

        // Default to current month if no date is selected
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        // Initialize queries for missions and violations
        $missionsQuery = InspectorMission::query();
        $violationsQuery = Violation::where('status', 1); // Ensure only active violations are considered

        // Handle date filtering
        $missionsQuery->when($date && $date != '-1', function ($query) use ($date) {
            return $query->whereDate('date', $date);
        }, function ($query) use ($startOfMonth, $endOfMonth) {
            return $query->whereBetween('date', [$startOfMonth, $endOfMonth]);
        });

        $violationsQuery->when($date && $date != '-1', function ($query) use ($date) {
            return $query->whereDate('created_at', $date);
        }, function ($query) use ($startOfMonth, $endOfMonth) {
            return $query->whereBetween('created_at', [$startOfMonth, $endOfMonth]);
        });

        // Apply filters based on user inputs
        if ($violationTypeId && $violationTypeId != '-1') {
            $violationsQuery->whereRaw("FIND_IN_SET(?, violation_type)", [$violationTypeId]);
        }

        if ($pointId && $pointId != '-1') {
            // Ensure $pointId is treated as an array
            $pointIds = is_array($pointId) ? $pointId : [$pointId];

            // Retrieve actual point IDs from Grouppoint
            $grouppointIds = Grouppoint::whereIn('id', $pointIds)
                ->where('deleted', 0)
                ->pluck('points_ids')
                ->flatten()
                ->toArray();

            $missionsQuery->whereIn('ids_group_point', $grouppointIds);
            $violationsQuery->whereIn('point_id', $pointIds);
        }

        if ($inspectorId && $inspectorId != '-1') {
            $missionsQuery->where('inspector_id', $inspectorId);
            $userId = Inspector::where('id', $inspectorId)->value('user_id');
            if ($userId) {
                $violationsQuery->where('user_id', $userId);
            }
        }

        // Get counts based on the filtered results
        $inspectorCount = $missionsQuery->distinct('inspector_id')->count('inspector_id');
        $pointCount = $missionsQuery->distinct('ids_group_point')->count('ids_group_point');
        $violationCount = $violationsQuery->count();

        // Prepare the results for the view
        $results = [
            'date' => $date && $date != '-1' ? $date : 'الشهر الحالى',
            'violationCount' => $violationCount,
            'inspectorCount' => $inspectorCount,
            'pointCount' => $pointCount,
        ];

        // Return view with the filtered results
        return view('statistics.index', compact('inspectors', 'points', 'violations', 'results'));
    }

    // Private method to fetch statistics data based on user role
    private function fetchStatisticsData()
    {
        // Check user role and fetch data accordingly
        if (Auth::user()->rule->name == "localworkadmin" || Auth::user()->rule->name == "superadmin") {
            $inspectors = Inspector::all();
            $points = Point::all(); // Use all() to fetch all points
            $violations = ViolationTypes::all();
        } else {
            $userDepartmentId = Auth::user()->department_id;

            $inspectors = Inspector::where('flag', 0)
            ->whereHas('user', function ($query) use ($userDepartmentId) {
                $query->where('department_id', $userDepartmentId);
            })->get();
            $points = collect(); // No points for non-admin users
            $violations = ViolationTypes::all(); // Still fetch all violation types
        }

        // Return the fetched data
        return [$inspectors, $points, $violations];
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
