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
        $today = Carbon::today()->format('Y-m-d');
        $inspector_ids = explode(',', $team->inspector_ids);

        // dd( $inspector_ids);
        foreach ($inspector_ids as $item) {
            // Find the InspectorMission record for the given date and inspector_id
            $query = InspectorMission::where('date', $today)
                ->where('inspector_id', $item)
                ->first();

            if ($query) {
                // Check if ids_instant_mission is not null or empty
                if (!empty($query->ids_instant_mission)) {
                    // Append the new mission ID to the existing ones
                    $existingIds = $query->ids_instant_mission;
                    $query->ids_instant_mission = $existingIds . ',' . $mission->id;
                } else {
                    $query->ids_instant_mission = $mission->id;
                }
                $query->save();
            } 
            else {
               return "this id don't exist in table InspectorMission ";
            }
        }

        return "true";
        // Send notification to the team
        // Notification::send($team->members, new MissionAssignedNotification($event->mission));
    }
}
