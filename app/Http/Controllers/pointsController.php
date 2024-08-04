<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Government;
use App\Models\Point;
use App\Models\Region;
use App\Models\Sector;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;


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
            ->addColumn('from', function ($row) {
                $time = Carbon::createFromFormat('H:i:s', $row->from)->format('h:i A');
                return str_replace(['AM', 'PM'], ['ص', 'م'], $time);
            })
            ->addColumn('to', function ($row) {
                $time = Carbon::createFromFormat('H:i:s', $row->to)->format('h:i A');
                return str_replace(['AM', 'PM'], ['ص', 'م'], $time);
            })
            ->addColumn('action', function ($row) {
                // $edit_permission = null;
                // $show_permission = null;
                // if (Auth::user()->hasPermission('edit Point')) {
                    $edit_permission = '<a class="btn btn-sm" style="background-color: #F7AF15;"  href=' . route('points.edit', $row->id) . '><i class="fa fa-edit"></i> تعديل</a>';
                // }
                // if (Auth::user()->hasPermission('view Point')) {
                    $show_permission = '<a class="btn btn-sm" style="background-color: #274373;"  href=' . route('points.show', $row->id) . '> <i class="fa fa-eye"></i>عرض</a>';
                // }
                return $show_permission . ' ' . $edit_permission;
            })
            ->rawColumns(['action'])
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
            'name' => 'required|string',
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
