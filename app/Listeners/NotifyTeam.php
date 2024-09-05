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
      
        if ($event->mission->inspector_id == null) {
            $inspector_ids = explode(',', $team->inspector_ids);
            // dd($team->inspector_ids);
            $flag= false;
            foreach ($inspector_ids as $item) {
                
                $query = InspectorMission::where('date', $event->mission->date)
                ->where('inspector_id', $item)
                ->first();

                if ($query) {
                    // Ensure ids_instant_mission is not null
    
                    // dd($existingIdsArray);
                    if (!empty($query->ids_instant_mission)) {
    
                        // Decode the JSON string to a PHP array
                        $array = $query->ids_instant_mission;
                        $existingIdsArray = $query->ids_instant_mission;
                        $array[] = json_encode($mission->id);
                        // Check if the mission ID is already in the array to avoid duplicates
                        if (!in_array($mission->id, $existingIdsArray)) {
                            $stringArray = array_map('strval', $array);
                            $jsonString = json_encode($stringArray);
                            $query->ids_instant_mission = json_decode($jsonString);
                        }
                    } else {
                        $existingIdsArray = $query->ids_instant_mission ? json_decode($query->ids_instant_mission, true) : [];
                        // Check if the mission ID is already in the array to avoid duplicates
                        if (!in_array($mission->id, $existingIdsArray)) {
                            $existingIdsArray[] = $mission->id;
                            $stringArray = array_map('strval', $existingIdsArray);
                            // Convert the updated array to JSON
                            $jsonString = json_encode($stringArray);
                            // Encode the array back to JSON and save it
                            $query->ids_instant_mission = json_decode($jsonString);
                        }
                    }
    
                    // Save the updated record
                    $query->save();
                    // dd($query);
                    $flag = true;
                } else {
                    $flag= false;
                }

               
                
               
                
            }
            // dd($flag);
            return $flag; 
        } else {
        

            $query = InspectorMission::where('date', $event->mission->date)
                ->where('inspector_id', $event->mission->inspector_id)
                ->first();
           
            if ($query) {
                // Ensure ids_instant_mission is not null

                // dd($existingIdsArray);
                if (!empty($query->ids_instant_mission)) {

                    // Decode the JSON string to a PHP array
                    $array = $query->ids_instant_mission;
                  //  dd($array);
                    $existingIdsArray = $query->ids_instant_mission;
                    $array[] = json_encode($mission->id);
                    // Check if the mission ID is already in the array to avoid duplicates
                    if (!in_array($mission->id, $existingIdsArray)) {
                        $stringArray = array_map('strval', $array);
                        $jsonString = json_encode($stringArray);
                        $query->ids_instant_mission = json_decode($jsonString);
                    }
                } else {
                    $existingIdsArray = $query->ids_instant_mission ? json_decode($query->ids_instant_mission, true) : [];
                    

                    // Check if the mission ID is already in the array to avoid duplicates
                    if (!in_array($mission->id, $existingIdsArray)) {
                        $existingIdsArray[] = $mission->id;
                        $stringArray = array_map('strval', $existingIdsArray);
                        // Convert the updated array to JSON
                        $jsonString = json_encode($stringArray);
                     
                        // Encode the array back to JSON and save it
                        $query->ids_instant_mission = json_decode($jsonString);
                    }
                }
             
                // Save the updated record
                $query->save();
                $flag = true;
            } else {
                $flag = false;
            }

            return $flag;
        }


        // }


        // Send notification to the team
        // Notification::send($team->members, new MissionAssignedNotification($event->mission));
    }
}
