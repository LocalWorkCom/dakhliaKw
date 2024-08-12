<?php

namespace App\Listeners;

use Carbon\Carbon;
use App\Events\MissionCreated;
use App\Models\InspectorMission;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyTeam
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(MissionCreated $event)
    {
        $mission = $event->mission;
        $team = $event->mission->groupTeam;
       
        // dd($today);
        $inspector_ids = explode(',', $team->inspector_ids);

        // dd($inspector_ids);
        $flag = false;
        foreach ($inspector_ids as $item) {
            // Find the InspectorMission record for the given date and inspector_id
            $today = Carbon::today()->format('Y-m-d');
            $query = InspectorMission::whereDate('date', $today)
                ->where('inspector_id', $item)
                ->first();
                // dd($query);
                if ($query) {
                    // Ensure ids_instant_mission is not null
                    if (!empty($query->ids_instant_mission)) {
                        $existingIdsArray = explode(',', $query->ids_instant_mission);
        
                        // Check if the mission ID is already in the array to avoid duplicates
                        if (!in_array($mission->id, $existingIdsArray)) {
                            $existingIdsArray[] = $mission->id;
                            $query->ids_instant_mission = implode(',', $existingIdsArray);
                        }

                    } else {
                        // If ids_instant_mission is empty or null, just assign the new mission ID
                        $query->ids_instant_mission = $mission->id;
                    }
        
                    // Save the updated record
                    $query->save();
                    $flag = true;
                } 
                 else {
                    $flag = false;
                }
                 
        }

        return $flag;
        // Send notification to the team
        // Notification::send($team->members, new MissionAssignedNotification($event->mission));
    }
}
