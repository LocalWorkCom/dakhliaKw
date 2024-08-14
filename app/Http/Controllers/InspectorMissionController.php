<?php

namespace App\Http\Controllers;

use App\Models\Grouppoint;
use Carbon\Carbon;

use Illuminate\Http\Request;
use App\Models\InspectorMission;

class InspectorMissionController extends Controller
{
    public function getMissionsByInspector($inspectorId)
    {
        $today = Carbon::today()->format('Y-m-d');
        // Retrieve the missions for the specific inspector
        $missions = InspectorMission::whereDate('date', $today)->where('inspector_id', $inspectorId)->get();
       
        // Return the missions as a JSON response
        return response()->json($missions);
    }
}
