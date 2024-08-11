<?php

namespace App\Console\Commands;

use App\Models\Groups;
use App\Models\Inspector;
use Illuminate\Console\Command;

class inspector_points extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:inspector_points';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get inspector team and group after this get the last points visited in previous day and select another points to visit that must be uniqe in same group';

    /**
     * Execute the console command.
     */
    public function handle()
    {
    //    Inspector::query()->update(['type' => 'Buildings']);
    $groups_points = Groups::select('id','points_inspector')->get();
    

    }
}
