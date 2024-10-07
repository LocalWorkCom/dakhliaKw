<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\DB;
use App\Models\Inspector;
use App\Models\instantmission;
use Illuminate\Console\Command;
use App\Models\InspectorMission;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use App\Notifications\MissionAssignedNotification;

class inspector_notify extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:inspector_notify';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send notifications for Inspector Missions';

    /**
     * Execute the console command.
     */

    // public function handle()
    // {
    //     $today = date('Y-m-d');

    //     // Fetch all Inspectors
    //     $inspectors = Inspector::all();

    //     foreach ($inspectors as $inspector) {
    //         // Check if inspector has ids_instant_mission for today and day_off is 0
    //         $mission = InspectorMission::where('inspector_id', $inspector->id)
    //             ->whereDate('date', $today)
    //             ->where('day_off', 0)
    //             ->whereNotNull('ids_instant_mission')
    //             ->first();

    //         // Send notification only if no team members have a day off

    //         if ($mission) {

    //             // Check if ids_instant_mission is an array or JSON string
    //             $instantMissionIds = is_string($mission->ids_instant_mission)
    //                 ? json_decode($mission->ids_instant_mission, true)
    //                 : $mission->ids_instant_mission;

    //             if (is_array($instantMissionIds)) {
    //                 foreach ($instantMissionIds as $instantMissionId) {
    //                     $instantMission = instantmission::find($instantMissionId);
    //                     if ($mission->day_off == 0) {
    //                         $inspectorId[] = $inspector->id;
    //                         // Send notification
    //                         Notification::send($inspectorId, new MissionAssignedNotification($instantMission));
    //                     }

    //                     // Log details before inserting
    //                     Log::info('Inserting notification', [
    //                         'user_id' => $inspector->id,
    //                         'mission_id' => $instantMissionId
    //                     ]);

    //                     // Insert into notifications table
    //                     DB::table('notifications')->insert([
    //                         'user_id' => $inspector->id,
    //                         'mission_id' => $instantMissionId,
    //                         'message' => 'A new mission has been assigned to your team.',
    //                         'created_at' => now(),
    //                         'updated_at' => now(),
    //                     ]);
    //                 }
    //             }
    //         }
    //     }
    // }
    public function handle()
    {
        $today = date('Y-m-d');

        // Fetch all Inspectors
        $inspectors = Inspector::all();

        foreach ($inspectors as $inspector) {
            // Check if inspector has ids_instant_mission for today and day_off is 0
            $mission = InspectorMission::where('inspector_id', $inspector->id)
                ->whereDate('date', $today)
                ->where('day_off', 0)
                ->whereNotNull('ids_instant_mission')
                ->first();
              //  dd($mission);
            // Send notification only if no team members have a day off
            if ($mission) {
                // Check if ids_instant_mission is an array or JSON string
                $instantMissionIds = is_string($mission->ids_instant_mission)
                    ? json_decode($mission->ids_instant_mission, true)
                    : $mission->ids_instant_mission;

                if (is_array($instantMissionIds)) {
                    foreach ($instantMissionIds as $instantMissionId) {
                        $token = getTokenDevice($inspector->id);
                        if ($mission->day_off == 0) {
                            // Send notification to the Inspector object
                           send_push_notification($instantMissionId,$token,'new mission.','A new mission has been assigned to your team.');

                            // Log details before inserting
                            Log::info('Inserting notification', [
                                'user_id' => $inspector->user_id,
                                'mission_id' => $instantMissionId
                            ]);

                            // Insert into notifications table
                            DB::table('notifications')->insert([
                                'user_id' => $inspector->user_id,
                                'mission_id' => $instantMissionId,
                                'title' => 'new mission.',
                                'message' => 'A new mission has been assigned to your team.',
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        }
                    }
                }
            }
        }
    }
}
