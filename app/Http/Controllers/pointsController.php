<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Government;
use App\Models\Grouppoint;
use App\Models\Point;
use App\Models\Region;
use App\Models\Sector;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;


class pointsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     */
    public function getGovernorates($sector)
    {
        // Fetch governorates based on the selected sector
        $sectorData = Sector::where('id', $sector)->first(['governments_IDs']);
        if (!$sectorData) {
            return response()->json([]);
        }

        // Extract the governorate IDs
        $governorateIds = $sectorData->governments_IDs;

        // Fetch the governorates based on the IDs
        $governorates = Government::whereIn('id', $governorateIds)->get();
        return response()->json($governorates);
    }

    public function getRegions($governorate)
    {
        // Fetch regions based on the selected governorate
        $regions = Region::where('government_id', $governorate)->get();
        return response()->json($regions);
    }
    public function getAllPoints($governorate)
    {
        // Fetch regions based on the selected governorate
        $regions = Point::where('government_id', $governorate)->get();
        return response()->json($regions);
    }
//     public function getAllPoints2($governorate, $points)
// {
//     // Fetch selected points
//     $selectedPoints = Point::whereIn('id', $points)->get();
//     $governmentIds = $selectedPoints->pluck('government_id')->unique();

//     // Fetch all points for the same government
//     $allPoints = Point::whereIn('government_id', $governmentIds)->get();

//     // Get all points in the grouppoint table
//     $pointsInGroup = Grouppoint::whereIn('government_id', $governmentIds)
//         ->pluck('point_ids') // Assuming this is a serialized array or JSON
//         ->flatten() // Flatten the array
//         ->unique(); // Get unique point IDs

//     // Filter out points that are already in the grouppoint table
//     $availablePoints = $allPoints->filter(function($point) use ($pointsInGroup) {
//         return !$pointsInGroup->contains($point->id);
//     });

//     return response()->json($availablePoints);
// }

    public function index()
    {
        return view("points.index");
    }
    public function getpoints()
    {
        $data = Point::with(['sector', 'government', 'region'])->get();

        return DataTables::of($data)
            ->addColumn('sector_name', function ($row) {
                return $row->sector ? $row->sector->name : '';
            })
            ->addColumn('government_name', function ($row) {
                return $row->government ? $row->government->name : '';
            })
            ->addColumn('region_name', function ($row) {
                return $row->region ? $row->region->name : '';
            })
            ->addColumn('group_name', function ($row) {
                // Fetch the group related to the government
                $group = Grouppoint::where('government_id', $row->government->id)->first();
            // //dd($row->government->id);
            //     // Check if the group exists and has the points_ids field
            //     if ($group && $group->points_ids) {
            //         // Decode the JSON field into a PHP array
            //         //$pointsIds = json_decode($group->points_ids, true);
            
            //         // Check if the point ID exists in the group
            //         if (in_array($row->id, $group->points_ids)) {
            //             $btn = '
            //             <a class="btn btn-sm" style="background-color: #F7AF15;" href="' . route('grouppoints.edit', $group->id) . '"><i class="fa fa-edit"></i> ' . $group->name . ' </a>';
            //         } else {
            //             $btn = '<p> لايوجد مجموعه</p>';
            //         }
            //     } else {
            //         $btn = '<p> لايوجد مجموعه</p>';
            //     }
            //dd($group->flag);
            
            if($group->flag !=0){
                $btn = ' <a class="btn btn-sm" style="background-color: #F7AF15;" href="' . route('grouppoints.edit', $group->id) . '"><i class="fa fa-edit"></i> ' . $group->name . ' </a>';
                
            }else{
                      $btn = '<p> لايوجد مجموعه</p>';
            }
                return $btn;
            })
            ->addColumn('from', function ($row) {
                // Ensure $row->from is in 'H:i:s' format before using createFromFormat
                $time = $row->from ? Carbon::createFromFormat('H:i:s', $row->from)->format('h:i A') : '';
                return str_replace(['AM', 'PM'], ['ص', 'م'], $time);
            })
            ->addColumn('to', function ($row) {
                // Ensure $row->to is in 'H:i:s' format before using createFromFormat
                $time = $row->to ? Carbon::createFromFormat('H:i:s', $row->to)->format('h:i A') : '';
                return str_replace(['AM', 'PM'], ['ص', 'م'], $time);
            })
            ->addColumn('action', function ($row) {
                $edit_permission = '<a class="btn btn-sm" style="background-color: #F7AF15;" href="' . route('points.edit', $row->id) . '"><i class="fa fa-edit"></i> تعديل</a>';
                $show_permission = '<a class="btn btn-sm" style="background-color: #274373;" href="' . route('points.show', $row->id) . '"><i class="fa fa-eye"></i> عرض</a>';
                return $show_permission . ' ' . $edit_permission;
            })
            ->rawColumns(['action', 'group_name'])
            ->make(true);
        
    }

    public function create()
    {
        return view("points.create");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //dd($request->all());
        $rules = [
            'name' => [
                'required',
                'string',
                Rule::unique('points', 'name')->ignore($request->id),
            ],
            'sector_id' => 'required|exists:sectors,id',
            'governorate' => 'required|exists:governments,id',
            'region' => 'required|exists:regions,id',
            'map_link' => 'nullable|string',
            'Lat' => 'nullable|string',
            'long' => 'nullable|string',
            'from' => 'required|date_format:H:i|before_or_equal:to',
            'to' => 'required|date_format:H:i|after_or_equal:from',
            'note' => 'nullable|string',
        ];

        // Define custom messages
        $messages = [
            'name.required' => 'يجب ادخال اسم نقطه',
            'name.unique' => 'يجب إدخال اسم نقطة مختلف',

            'name.string' => 'يجب ان لا يحتوى اسم النقطه على رموز',
            'governorate.required' => 'يجب اختيار محافظه واحده على الاقل',
            'sector_id.required' => '',
            'region.required' => '',
            'from.required' => 'يجب ادخال تاريخ البداية',
            'to.required' => 'يجب ادخال تاريخ النهاية',
            'from.date_format' => 'تاريخ البداية يجب أن يكون بصيغة صحيحة (HH:MM)',
            'to.date_format' => 'تاريخ النهاية يجب أن يكون بصيغة صحيحة (HH:MM)',
            'from.before_or_equal' => 'تاريخ البداية يجب ان يكون قبل أو يساوي تاريخ النهاية',
            'to.after_or_equal' => 'تاريخ النهاية يجب أن يكون بعد أو يساوي تاريخ البداية',
        ];

        // Validate the request
        $validatedData = Validator::make($request->all(), $rules, $messages);

        if ($validatedData->fails()) {
            return redirect()->back()->withErrors($validatedData)->withInput();
        }

        // Proceed with storing the data
        $point = new Point();
        $point->name = $request->name;
        $point->government_id = $request->governorate;
        $point->region_id = $request->region;
        $point->sector_id = $request->sector_id;
        $point->google_map = $request->map_link;
        $point->lat = $request->Lat;
        $point->long = $request->long;
        $point->note = $request->note;
        $point->from = $request->from;
        $point->to = $request->to;
        $point->save();
        $pointgroups = new Grouppoint();
        $pointgroups->name = $point->name;
        $pointgroups->points_ids = [json_encode($point->id)];
        $pointgroups->government_id  = $request->governorate;
        $pointgroups->flag  = 0;

        $pointgroups->save();
        return redirect()->route('points.index')->with('message', 'تم أضافه نقطه');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data = Point::findOrFail($id);

        // Format 'from' and 'to' fields
        $data->formatted_from = Carbon::createFromFormat('H:i:s', $data->from)->format('h:i A');
        $data->formatted_to = Carbon::createFromFormat('H:i:s', $data->to)->format('h:i A');
    
        // Replace AM/PM with ص/م
        $data->formatted_from = str_replace(['AM', 'PM'], ['ص', 'م'], $data->formatted_from);
        $data->formatted_to = str_replace(['AM', 'PM'], ['ص', 'م'], $data->formatted_to);
    
        return view('points.showdetails', compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {

        $data = Point::findOrFail($id);

        return view('points.edit', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        //dd($request->all());
        $rules = [
            'name' => 'required|string',
            'sector_id' => 'required|exists:sectors,id',
            'governorate' => 'required|exists:governments,id',
            'region' => 'required|exists:regions,id',
            'map_link' => 'nullable|string',
            'lat' => 'nullable|string',
            'long' => 'nullable|string',
            'from' => 'required|date_format:H:i|before_or_equal:to',
            'to' => 'required|date_format:H:i|after_or_equal:from',
            'note' => 'nullable|string',
        ];

        // Define custom messages
        $messages = [
            'name.required' => 'يجب ادخال اسم نقطه',
            'name.string' => 'يجب ان لا يحتوى اسم النقطه على رموز',
            'governorate.required' => 'يجب اختيار محافظه واحده على الاقل',
            'sector_id.required' => '',
            'region.required' => '',
            'from.required' => 'يجب ادخال تاريخ البداية',
            'to.required' => 'يجب ادخال تاريخ النهاية',
            'from.date_format' => 'تاريخ البداية يجب أن يكون بصيغة صحيحة (HH:MM)',
            'to.date_format' => 'تاريخ النهاية يجب أن يكون بصيغة صحيحة (HH:MM)',
            'from.before_or_equal' => 'تاريخ البداية يجب ان يكون قبل أو يساوي تاريخ النهاية',
            'to.after_or_equal' => 'تاريخ النهاية يجب أن يكون بعد أو يساوي تاريخ البداية',
        ];

        // Validate the request
        $validatedData = Validator::make($request->all(), $rules, $messages);

        if ($validatedData->fails()) {
            //dd($validatedData->messages());
            return redirect()->back()->withErrors($validatedData)->withInput();
        }

        // Proceed with storing the data
        $point = Point::find($request->id);
        $point->name = $request->name;
        $point->government_id = $request->governorate;
        $point->region_id = $request->region;
        $point->sector_id = $request->sector_id;
        $point->google_map = $request->map_link;
        $point->lat = $request->Lat;
        $point->long = $request->long;
        $point->note = $request->note;
        $point->from = $request->from;
        $point->to = $request->to;
        $point->save();

        return redirect()->route('points.index')->with('message', 'تم تعديل نقطه');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
