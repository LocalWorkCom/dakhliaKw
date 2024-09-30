<?php

namespace App\Listeners;

use Carbon\Carbon;
use App\Events\MissionCreated;
use App\Models\Inspector;
use App\Models\InspectorMission;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;
use App\Notifications\MissionAssignedNotification;
use Illuminate\Support\Facades\DB;

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
           // dd($event->mission->inspector_id);
            $inspector_ids = explode(',', $team->inspector_ids);
            // dd($team->inspector_ids);
            $flag = false;
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
                    $flag = false;
                }
            }
            // dd($flag);
            $inspector_ids = explode(',', $team->inspector_ids);
            // Send notification to the team
            // Notification::send($inspector_ids, new MissionAssignedNotification($event->mission));
            foreach ($inspector_ids as $inspector) {
                $token = getTokenDevice($inspector);
                $user_id = Inspector::find($inspector)->user_id;
                DB::table('notifications')->insert([
                    'user_id' => $user_id,
                    'mission_id' => $event->mission->id,
                    'title' => 'new mission.',
                    'message' => 'A new mission has been assigned to your team.',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                send_push_notification($event->mission->id, $token, 'new mission.', 'A new mission has been assigned to your team.');

              
            }

            return $flag;
        } else {

         //   dd($event->mission);
            $query = InspectorMission::where('date', $event->mission->date)
                ->where('inspector_id', $event->mission->inspector_id)
                ->first();
           // dd($query);
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

            // Send notification to the team
            $token = getTokenDevice($event->mission->inspector_id);
            //dd($token);
            $user_id = Inspector::find($event->mission->inspector_id)->user_id;
            $check= DB::table('notifications')->where('user_id', $user_id)->where('mission_id',$event->mission->id)->get();
            if(count($check)==0)
            {
            DB::table('notifications')->insert([
                'user_id' => $user_id,
                'mission_id' => $event->mission->id,
                'title' => 'امر خدمة جديد.',
                'message' => 'لقد تم اسناد أمر خدمة جديد لك',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
          //  print_r(now());
            send_push_notification($event->mission->id, $token, 'أمر خدمة جديد', 'لقد تم اسماد أمر خدمة جديد لك.');
           }
         

         
            return $flag;
        }
    }
}
