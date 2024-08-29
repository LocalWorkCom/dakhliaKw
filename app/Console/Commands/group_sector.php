<?php

namespace App\Console\Commands;

use App\Models\Groups;
use App\Models\Sector;
use Illuminate\Console\Command;

class group_sector extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:group_sector';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    //     public function handle()
    //     {
    //         //
    //         $Groups = Groups::all();
    //         foreach ($Groups as $Group) {
    //             // $old_order = $Group->sector->order;
    //             $currentSector = Sector::find($Group->sector_id);

    //             if ($currentSector) {
    //                 // Get the ID of the current sector
    //                 $currentSectorId = $currentSector->id;

    //                 // Retrieve the next sector in the sorted order (e.g., by ID)
    //                 $nextSector = Sector::where('id', '>', $currentSectorId)
    //                     ->orderBy('id', 'asc')
    //                     ->first();

    //                 if ($nextSector) {
    //                     $new_sector_id = $nextSector->id;
    //                     $Group->sector_id = $new_sector_id;
    //                     $Group->save();
    //                 }
    //             }
    //         }
    //     }
    public function handle()
    {
        // Retrieve all sectors, sorted by the desired order (e.g., by id or any other field)
        $sectors = Sector::orderBy('order', 'asc')->get();

        // Map sectors by ID for quick access
        $sectorMap = $sectors->keyBy('id');

        // Collect sector IDs in an array
        $sectorIds = $sectors->pluck('id')->toArray();

        // Iterate over all Groups
        $Groups = Groups::all();
        foreach ($Groups as $Group) {
            // Retrieve the current sector for the Group
            $currentSectorId = $Group->sector_id;

            // Find the current sector in the sorted list
            $currentSectorIndex = array_search($currentSectorId, $sectorIds);

            if ($currentSectorIndex !== false) {
                // Determine the index of the next sector
                $nextSectorIndex = ($currentSectorIndex + 1) % count($sectorIds);
                $nextSectorId = $sectorIds[$nextSectorIndex];

                // Update the Group's sector_id to the next sector
                $Group->sector_id = $nextSectorId;
                $Group->save();
            }
        }
    }
}
