<?php

namespace App\Console\Commands;

use App\Models\Inspector;
use App\Models\EmployeeVacation;
use App\Models\InspectorMission;
use App\Models\Team; // Assuming you have a Team model
use App\Notifications\InspectorMissionNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

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
    public function handle()
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
                        $inspectorMissions = InspectorMission::where('inspector_id', $inspector->id)
                            ->whereDate('date', '=', $today)
                            ->get();

                        foreach ($inspectorMissions as $inspectorMission) {
                            // Update the InspectorMission record with the vacation ID
                            $inspectorMission->vacation_id = $EmployeeeVacation->id;
                            $inspectorMission->save();

                            // Check if the inspector's team members are not on a day off
                            $team = $inspector->team; // Assuming there is a relationship to the team
                            if ($team) {
                                // Fetch team members and check their day off status
                                $teamMembers = $team->members; // Assuming 'members' is the relationship method
                                $teamMembersWithDayOff = $teamMembers->whereHas('inspectorMissions', function ($query) use ($today) {
                                    $query->whereDate('date', $today)->where('day_off', 1);
                                });

                                // Send notification only if no team members have day off
                                if ($teamMembersWithDayOff->isEmpty()) {
                                    Notification::send($team, new InspectorMissionNotification($inspectorMission));
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}
