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
        // Extract filter parameters
        $date = $request->input('date');
        $all_date = $request->input('all_date');
        $points = $request->input('points');
        $violation = $request->input('violation');
        $inspectors = $request->input('inspectors');
    
        // Query logic here (example)
        $query = InspectorMission::query();
    
        if (!$all_date && $date) {
            $query->where('date', $date);
        }
    
        if ($points && $points != -1) {
            $query->where('point_id', $points);
        }
    
        if ($violation && $violation != -1) {
            $query->whereHas('violations', function ($q) use ($violation) {
                $q->where('violation_type', $violation);
            });
        }
    
        if ($inspectors && $inspectors != -1) {
            $query->where('inspector_id', $inspectors);
        }
    
        $inspector_num = $query->count();
        $violation_num = $query->whereHas('violations')->count();
    
        $results = [
            'date' => $date,
            'points' => $points,  // Adjust as necessary
            'violations' => $violation_num,
            'inspectors' => $inspector_num,
        ];
    
        return response()->json($results);
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
