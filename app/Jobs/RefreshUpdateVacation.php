<?php

namespace App\Jobs;

use App\Models\EmployeeVacation;
use App\Models\Inspector;
use App\Models\InspectorMission;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RefreshUpdateVacation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $today = date('Y-m-d');

        $EmployeeVacations = EmployeeVacation::all();
        foreach ($EmployeeVacations as $EmployeeeVacation) {

            $expectedEndDate = ExpectedEndDate($EmployeeeVacation)[0];

            if ($EmployeeeVacation->status == 'Approved') {

                if ($EmployeeeVacation->start_date < $today && $expectedEndDate < $today && (!$EmployeeeVacation->end_date || $EmployeeeVacation->end_date > $today)) {
                    $EmployeeeVacation->is_exceeded = 1;
                    $EmployeeeVacation->save();
                    $inspector = Inspector::where('user_id', $EmployeeeVacation->employee_id)->first();

                    if ($inspector) {
                        // Fetch InspectorMission records for the found inspector ID
                        $inspectorMission = InspectorMission::where('inspector_id', $inspector->id)
                            ->whereDate('date', '=', $today)
                            ->first();

                        // Update the InspectorMission record with the vacation ID
                        $inspectorMission->ids_group_point = null;

                        $inspectorMission->vacation_id = $EmployeeeVacation->id;
                        $inspectorMission->save();
                        // $inspectors = InspectorMission::where('group_team_id', $inspectorMission->group_team_id)->where('vacation_id', null)->whereDate('date', '=', $today)->count();

                        // if ($inspectors < 2) {
                        //     $title = 'تنبيه من دوريات';
                        //     $message = 'هذه الدوريه أصبح بها مفتش واحد';

                        //     $users = User::where('rule_id', 2)->get();
                        //     foreach ($users as $user) {
                        //         send_push_notification(null, $user->fcm_token, $title, $message,null);
                        //         $notify = new Notification();
                        //         $notify->message = $message;
                        //         $notify->title = $title;
                        //         $notify->group_id = $inspectorMission->group_id;
                        //         $notify->team_id = $inspectorMission->group_team_id;
                        //         $notify->user_id =  $user->id;
                        //         $notify->status = 0;
                        //         $notify->save();
                        //     }
                        // }
                    }
                }
            }
        }
    }
}
