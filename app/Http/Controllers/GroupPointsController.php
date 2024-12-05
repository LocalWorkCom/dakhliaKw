<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Government;
use App\Models\Grouppoint;
use App\Models\Point;
use App\Models\Sector;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


class GroupPointsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('grouppoints.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // print_r($request->all());
        $rules = [
            'name' => 'required|string',
            'governorate' => 'required|exists:governments,id',
            // 'pointsIDs' => 'required|array|exists:points,id',

        ];

        // // Define custom messages
        $messages = [
            'name.required' => 'يجب ادخال اسم القطاع',
            'name.string' => 'يجب ان لا يحتوى اسم القطاع على رموز',
            'pointsIDs.required' => 'يجب اختيار نقطه واحده على الاقل',
            // 'pointsIDs.exists' => ''

        ];

        // // Validate the request
        $validatedData = Validator::make($request->all(), $rules, $messages);
        //dd($validatedData->fails());
        // // Validate the request
        // $request->validate($rules, $messages);
        if ($validatedData->fails()) {

            return redirect()->back()->withErrors($validatedData)->withInput();
        }
        $sector_id = Sector::whereJsonContains('governments_IDs', $request->governorate)->value('id');
        $points_IDs = Grouppoint::whereIn('id', $request->pointsIDs)->pluck('points_ids')
            ->flatMap(function ($item) {
                // Decode only if $item is a JSON string
                return is_string($item) ? json_decode($item, true) : $item;
            })
            ->filter() // Remove any null values (in case json_decode fails)
            ->toArray();
        //  dd($points_IDs);

        $points = new Grouppoint();
        $points->name = $request->name;
        $points->points_ids = $points_IDs;
        $points->government_id  = $request->governorate;
        $points->sector_id  = $sector_id;
        $points->flag  = 1;

        $points->save();
        $pointsIDs = is_array($request->pointsIDs) ? $request->pointsIDs : json_decode($request->pointsIDs, true);
        $deleted = Grouppoint::where('flag', 0)
            ->whereIn('id', $pointsIDs)->get();
        //dd($deleted);
        // Optional: Perform actions with the retrieved records
        foreach ($deleted as $record) {
            // Example action: Delete the record
            $record->deleted = 1;
            $record->save();
        }
        return redirect()->route('points.index')->with('message', 'تم أضافه مجموعه');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $data = Grouppoint::findOrFail($id);
        $selected = is_string($data->points_ids) ? json_decode($data->points_ids, true) : (array) $data->points_ids;

        // Get government_id
        $government = $data->government_id;

        // Get available points_ids
        $available = Grouppoint::where('government_id', $government)
            ->where('flag', 0)
            ->where('deleted', 0)
            ->pluck('points_ids')
            ->flatMap(function ($json) {
                // Check if the item is a string before decoding
                return is_string($json) ? json_decode($json, true) : (array) $json; // Decode each JSON string or cast to array
            })
            ->toArray();

        // Merge selected and available arrays
        $merged = array_merge($selected, $available);

        // Remove duplicates (optional)
        $merged = array_unique($merged);

        // If needed, encode back to JSON
        $mergedJson = array_values($merged); // This also re-indexes the array
        //dd($mergedJson);
        $points = Point::whereIn('id', $mergedJson)->get();

        // Now you can use $mergedJson for your further logic or saving back to the database

        return view('grouppoints.edit', [
            'data' => $data,
            'selectedPoints' => $points
        ]);

        return view('grouppoints.edit', [
            'data' => $data,
            'selectedPoints' => $points
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

        $rules = [
            'name' => 'required|string',
            'pointsIDs' => 'required|array|exists:points,id',

        ];

        // // Define custom messages
        $messages = [
            'name.required' => 'يجب ادخال اسم القطاع',
            'name.string' => 'يجب ان لا يحتوى اسم القطاع على رموز',
            'pointsIDs.required' => 'يجب اختيار نقطه واحده على الاقل',
            'pointsIDs.exists' => ''

        ];
        // // Validate the request
        $validatedData = Validator::make($request->all(), $rules, $messages);
        // // Validate the request
        if ($validatedData->fails()) {
            return redirect()->back()->withErrors($validatedData)->withInput();
        }
        // dd($id);
        // Find the Grouppoint being updated
        // Get the Grouppoint being updated
        $points = Grouppoint::find($request->id);
        // Ensure $points->points_ids is an array
        $oldPointsIDs = is_array($points->points_ids) ? $points->points_ids : json_decode($points->points_ids, true);

        // Ensure $request->pointsIDs is an array
        $newPointsIDs = is_array($request->pointsIDs) ? $request->pointsIDs : json_decode($request->pointsIDs, true);

        // Update the Grouppoint fields
        $points->name = $request->name;
        $points->points_ids = $newPointsIDs; // Ensure the points_ids are stored as JSON
        $points->government_id = $request->government_id;
        $points->flag = 1;
        $points->deleted = 0;
        $points->save();

        // Find removed and newly added IDs
        $removedPointsIDs = array_diff($oldPointsIDs, $newPointsIDs); // IDs removed from the group
        $newAddedIDs = array_diff($newPointsIDs, $oldPointsIDs); // IDs newly added to the group

        // Handle newly added IDs: Update `deleted` to 1
        if (!empty($newAddedIDs)) {
            Grouppoint::where(function ($query) use ($newAddedIDs) {
                foreach ($newAddedIDs as $id) {
                    $query->orWhereRaw('JSON_CONTAINS(points_ids, ?) = 1', [json_encode($id)]);
                }
            })->where('flag', 0)->update(['deleted' => 1]);
        }

        // Handle removed IDs: Update `deleted` to 1 and `flag` to 0
        if (!empty($removedPointsIDs)) {
            foreach ($removedPointsIDs as $removedID) {
                // $pointNew = Point::find($removedID);
                // if ($pointNew) { // Check if the Point exists
                $grouppoint = Grouppoint::where('points_ids', 'like', '%"' . $removedID . '"%')
                ->where('flag', 0)->first();
               // dd($removedPointsIDs ,$removedID,$grouppoint );
                //dd($grouppoint , $removedID );
                $grouppoint->deleted = 0;
                $grouppoint->save();
                // if ($grouppoint) { // Ensure the Grouppoint exists
                // $grouppoint->update([
                //     'deleted' => 0,
                //     'flag' => 0,
                // ]);
                // }
                // }
            }
        }


        return redirect()->route('points.index')->with('message', 'تم تعديل مجموعه ');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
