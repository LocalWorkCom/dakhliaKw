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

        $searchResults = null;
        return view('statistics.index', compact('inspectors', 'points', 'violations', 'searchResults'));
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
        $pointId = $request->input('points');
        $violationTypeId = $request->input('violation');
        $inspectorId = $request->input('inspector');
        $userId = Inspector::where('id', $inspectorId)->value('user_id');
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
        $allPoints = Point::query();


        $allPoints = Point::query()->when($pointId && $pointId != -1, function ($query) use ($pointId) {
            return $query->where('id', $pointId);
        })->orderBy('id')->pluck('id')->toArray();

        $searchResults = collect();
      
        foreach ($allPoints as $point) {
            $point_id = '' . $point . '';
            $groupId = Grouppoint::whereJsonContains('points_ids', $point_id)->where('deleted', 0)->value('id');

            $group = (string) $groupId; // Ensure $groupId is a string and in the correct format

            $missionsQuery = InspectorMission::whereJsonContains('ids_group_point', $group);
                   
            if ($date) {
                $missionsQuery->whereDate('date', $date);
            } elseif ($request->input('all_date') == 'on') {
                $missionsQuery->whereBetween('date', [$startOfMonth, $endOfMonth]);
            }

            if ($inspectorId && $inspectorId != -1) {
              
                $missionsQuery->where('inspector_id', $inspectorId);
            
            }
            dd( $missionsQuery->distinct('inspector_id')->count('inspector_id'));
            $inspectorCount = $inspectorId && $inspectorId != -1 $missionsQuery->distinct('inspector_id')->count('inspector_id');
            $violationDate = $missionsQuery->orderBy('date', 'desc')->value('date');
            $formattedDate = $date ? Carbon::parse($date)->format('d-m-Y') : ($violationDate ? Carbon::parse($violationDate)->format('d-m-Y') : 'No date available');

            if ($inspectorCount > 0) {
                $searchResults->push([
                    'point_name' => Point::find($point)->name ?? 'Unknown',
                    'inspector_count' => $inspectorCount,
                    'date' => $formattedDate
                ]);
            }
        }
        dd($searchResults );
        return view('statistics.index', compact('inspectors', 'points', 'violations', 'searchResults'))->render();
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
