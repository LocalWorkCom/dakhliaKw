<?php

namespace App\Console;

use App\Console\Commands\inspector_mission;
use App\Console\Commands\inspector_points;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands =[
        inspector_mission::class,
        inspector_points::class,

    ];
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();
        $schedule->command('app:inspector_mission')->monthly();
        $schedule->command('app:inspector_points')->monthly();
        $schedule->command('app:group_sector')->monthly();
        $schedule->command('app:update_employee_vacation')->dailyAt('23:00');
        $schedule->command('app:employee_vacation')->dailyAt('23:01');
        //new
        $schedule->command('app:inspector_notify')->dailyAt('00:00');

    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
