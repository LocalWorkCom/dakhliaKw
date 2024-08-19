<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Government;
use App\Models\Grouppoint;
use App\Models\Point;
use App\Models\PointDays;
use App\Models\Region;
use App\Models\Sector;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
    public function index()
    {
        return view("points.index");
    }
    public function getpoints()
    {
        $data = Point::with(['sector', 'government', 'region'])->orderBy('created_at','desc')->get();

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
                $groups = Grouppoint::where('government_id', $row->government->id)->get();

                $btn = '<p>لايوجد مجموعه</p>'; // Default message

                foreach ($groups as $group) {
                    if ($group && $group->points_ids) {
                        $pointsIds = $group->points_ids; // Decode if points_ids is a JSON string

                        if (in_array($row->id, $pointsIds) && $group->flag == 1) {
                            $btn = '<a class="btn btn-sm" style="background-color: #F7AF15;" href="' . route('grouppoints.edit', $group->id) . '"><i class="fa fa-edit"></i> ' . $group->name . ' </a>';
                            break; // Exit loop once a matching group is found
                        }
                    }
                }

                return $btn;
            })
            ->addColumn('work_type', function ($row) {
                return $row->work_type == 0 ? 'دوام 24 ساعه' : ' دوام جزئى';
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
            'time_type' => 'required',
            'day_name' => 'required|array',
            'note' => 'nullable|string',
        ];

        // Define custom messages
        $messages = [
            'name.required' => 'يجب ادخال اسم نقطه',
            'name.unique' => 'يجب إدخال اسم نقطة مختلف',
            'name.string' => 'يجب ان لا يحتوى اسم النقطه على رموز',
            'governorate.required' => 'يجب اختيار محافظه واحده على الاقل',
            'sector_id.required' => 'يجب اختيار قطاع الخاصه بالنقطه',
            'region.required' => 'يجب اختيار المنطقه الخاصه بالنقطه',
            'time_type.required' => 'يجب ادخال نظام العمل',
            'day_name.required' => 'يجب اختيار الايام الخاصه بنظام العمل',
            'day_name.required' => 'يجب اختيار الايام الخاصه بنظام العمل',

        ];

        // Validate the request
        $validatedData = Validator::make($request->all(), $rules, $messages);

        if ($validatedData->fails()) {
            return redirect()->back()->withErrors($validatedData)->withInput();
        }
        DB::transaction(function () use ($request) {
            $dayNames = $request->input('day_name');
            $fromTimes = $request->input('from');
            $toTimes = $request->input('to');

            // Storing the point data
            $point = new Point();
            $point->name = $request->name;
            $point->government_id = $request->governorate;
            $point->region_id = $request->region;
            $point->sector_id = $request->sector_id;
            $point->google_map = $request->map_link;
            $point->lat = $request->Lat;
            $point->long = $request->long;
            $point->work_type = $request->time_type;
            $point->days_work = $request->time_type == 0 ? $dayNames : null;
            $point->created_by = auth()->id(); // Use auth() helper
            $point->note = $request->note;
            $point->save();

            // Creating a group point record
            $pointgroups = new Grouppoint();
            $pointgroups->name = $point->name;
            $pointgroups->points_ids = [json_encode($point->id)]; // Store as JSON array
            $pointgroups->government_id = $request->governorate;
            $pointgroups->sector_id  = $request->sector_id;
            $pointgroups->flag = 0;
            $pointgroups->save();

            // If work type is part-time, create related PointDays records
            if ($request->time_type == 1 && count($dayNames) === count($fromTimes) && count($fromTimes) === count($toTimes)) {
                foreach ($dayNames as $index => $dayName) {
                    PointDays::create([
                        'name' => $dayName,
                        'from' => $fromTimes[$index],
                        'to' => $toTimes[$index],
                        'point_id' => $point->id,
                        'created_by' => auth()->id(),
                    ]);
                }
            }
        });

        return redirect()->route('points.index')->with('message', 'تم أضافه نقطه');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data = Point::findOrFail($id);
        if ($data->work_type == 1) {
            $days = PointDays::where('point_id', $data->id)->get();
        } else {
            $days = null;
        }
        return view('points.showdetails', compact('data', 'days'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {

        $data = Point::findOrFail($id);
        if ($data->work_type == 1) {
            $days = PointDays::where('point_id', $data->id)->get();
        } else {
            $days = null;
        }

        return view('points.edit', compact('data', 'days'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
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
            'time_type' => 'required',
            'day_name' => 'required|array',
            'note' => 'nullable|string',
        ];

        // Define custom messages
        $messages = [
            'name.required' => 'يجب ادخال اسم نقطه',
            'name.unique' => 'يجب إدخال اسم نقطة مختلف',
            'name.string' => 'يجب ان لا يحتوى اسم النقطه على رموز',
            'governorate.required' => 'يجب اختيار محافظه واحده على الاقل',
            'sector_id.required' => 'يجب اختيار قطاع الخاصه بالنقطه',
            'region.required' => 'يجب اختيار المنطقه الخاصه بالنقطه',
            'time_type.required' => 'يجب ادخال نظام العمل',
            'day_name.required' => 'يجب اختيار الايام الخاصه بنظام العمل',
            'day_name.required' => 'يجب اختيار الايام الخاصه بنظام العمل',

        ];


        // Validate the request
        $validatedData = Validator::make($request->all(), $rules, $messages);

        if ($validatedData->fails()) {
            //dd($validatedData->messages());
            return redirect()->back()->withErrors($validatedData)->withInput();
        }
//dd($request->day_name);
        DB::transaction(function () use ($request) {
            $dayNames =$request->day_name;
            $fromTimes = $request->input('from');
            $toTimes = $request->input('to');
            $pointId = $request->input('id');

            $point = Point::find($pointId);
            $point->name = $request->name;
            $point->government_id = $request->governorate;
            $point->region_id = $request->region;
            $point->sector_id = $request->sector_id;
            $point->google_map = $request->map_link;
            $point->lat = $request->Lat;
            $point->long = $request->long;
            $point->work_type = $request->time_type;
            $point->days_work = $request->time_type == 0 ? json_encode($dayNames , true) : null; 
            $point->created_by = auth()->id(); 
            $point->note = $request->note;
            $point->save();

            // Update or create PointDays records
            if ($request->time_type == 1 && count($dayNames) === count($fromTimes) && count($fromTimes) === count($toTimes)) {
                // Delete existing PointDays records for the point
                PointDays::where('point_id', $point->id)->delete();

                // Create new PointDays records
                foreach ($dayNames as $index => $dayName) {
                    PointDays::create([
                        'name' => $dayName,
                        'from' => $fromTimes[$index],
                        'to' => $toTimes[$index],
                        'point_id' => $point->id,
                        'created_by' => auth()->id(),
                    ]);
                }
            } else {
                PointDays::where('point_id', $point->id)->delete();
            }
        });

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
