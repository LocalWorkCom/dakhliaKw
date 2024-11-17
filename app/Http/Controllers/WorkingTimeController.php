<?php

namespace App\Http\Controllers;

use App\Models\WorkingTime;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\StoreWorkingTimeRequest;
use App\Http\Requests\UpdateWorkingTimeRequest;
use DateTime;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class WorkingTimeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //

        return view('working_time.index');
    }

    public function getWorkingTime()
    {
        $data = WorkingTime::all();
        // dd($data);

        return DataTables::of($data)->addColumn('action', function ($row) {
       
            $delete_permission = '';

            //return '<button class="btn btn-primary btn-sm">Edit</button>';

            $show_permission = '<a class="btn btn-sm" style="background-color: #274373;" onclick="openViewModal(' . $row->id . ', \'' . $row->name . '\')"><i class="fa fa-eye"></i> عرض</a>';
            $edit_permission = '<a class="btn btn-sm" style="background-color: #274373;" onclick="openedit(' . $row->id . ', \'' . $row->name . '\', \'' . $row->start_time . '\', \'' . $row->end_time . '\', \'' . $row->color . '\')"><i class="fa fa-edit"></i> تعديل</a>';

            if (Auth::user()->hasPermission('delete WorkingTime')) {
                $delete_permission = '<a class="btn btn-sm"  style="background-color: #C91D1D;"  onclick="opendelete(' . $row->id . ')">  <i class="fa fa-edit"></i> حذف </a>';
            }

            $uploadButton = $edit_permission . $show_permission . $delete_permission;
            return $uploadButton;
            
        })
            ->rawColumns(['action'])
            ->make(true);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
         //dd($request);

        $messages = [
            'name.required' => 'الاسم  مطلوب ولا يمكن تركه فارغاً.',
            'start_time.required' => 'بداية فترة العمل   مطلوب ولا يمكن تركه فارغاً.',
            'end_time.required' => 'نهاية فترة العمل   مطلوب ولا يمكن تركه فارغاً.',
            'color.unique' => 'اللون المحدد موجود بالفعل، يرجى اختيار لون آخر.',

        ];
        $validatedData = Validator::make($request->all(), [
            'name' => 'required',
            'start_time' => 'required',
            'end_time' => 'required',
            'color' => 'required|unique:working_times,color',
        ], $messages);

        // Handle validation failure
        if ($validatedData->fails()) {
            return redirect()->back()->withErrors($validatedData)->withInput();
        }
        try {
            $startTime = DateTime::createFromFormat('h:i A', $request->start_time)->format('H:i');
            $endTime = DateTime::createFromFormat('h:i A', $request->end_time)->format('H:i');
            $WorkingTime = new WorkingTime();
            $WorkingTime->name = $request->name;
            $WorkingTime->start_time = $startTime;
            $WorkingTime->end_time = $endTime;
            // dd($WorkingTime);
            // Generate a random color that is not in the database
            /*do {
                $color = sprintf('#%06X', mt_rand(0, 0xFFFFFF));
                $existColors = ['#000000ab', '#ffffff', '#d6d6d6', '#fdfdfdc2', '#c9f5f9', '#4edfd0ba'];
            } while (WorkingTime::where('color', $color)->whereNotIn('color', $existColors)->exists());*/

            $WorkingTime->color = $request->color;
            $WorkingTime->save();
            // Dynamically create model instance based on the model class string
            return view('working_time.index')->with('success', 'تم أنشاء فترت العمل بنجاح');
        } catch (\Exception $e) {
            return response()->json($e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $workingTime = WorkingTime::find($id);
        // dd($workingTime);
        if ($workingTime) {
            return response()->json(['success' => true, 'data' => $workingTime]);
        } else {
            return response()->json(['success' => false, 'message' => 'Record not found'], 404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {

        $workingTime = WorkingTime::find($id);
        if ($workingTime) {
            return response()->json(['success' => true, 'data' => $workingTime]);
        } else {
            return response()->json(['success' => false, 'message' => 'Record not found'], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $workingTime = WorkingTime::find($request->id_edit);

        // Check if the working time record exists
        if (!$workingTime) {
            return redirect()->back()->withErrors('No working time found with the provided ID.');
        }
        $messages = [
            'name_edit.required' => 'الاسم مطلوب ولا يمكن تركه فارغاً.',
            'start_time_edit.required' => 'بداية فترة العمل مطلوبة ولا يمكن تركها فارغة.',
            'end_time_edit.required' => 'نهاية فترة العمل مطلوبة ولا يمكن تركها فارغة.',
            'color_edit.unique' => 'اللون المحدد موجود بالفعل، يرجى اختيار لون آخر.',
        ];

        $validatedData = Validator::make($request->all(), [
            'name_edit' => 'required',
            'start_time_edit' => 'required',
            'end_time_edit' => 'required',
            'color_edit' => [
                'required',
                Rule::unique('working_times', 'color')->ignore($workingTime->id), // Ignore current record's color
            ],        ], $messages);

        if ($validatedData->fails()) {
            return redirect()->back()->withErrors($validatedData)->withInput();
        }

        try {
            // Handle both 12-hour and 24-hour formats
            $startTimeInput = $request->start_time_edit;
            $endTimeInput = $request->end_time_edit;

            // Try to parse the start time
            $startTime = DateTime::createFromFormat('h:i A', $startTimeInput);
            if ($startTime === false) {
                // If it fails, try 24-hour format
                $startTime = DateTime::createFromFormat('H:i', $startTimeInput);
            }

            // Try to parse the end time
            $endTime = DateTime::createFromFormat('h:i A', $endTimeInput);
            if ($endTime === false) {
                // If it fails, try 24-hour format
                $endTime = DateTime::createFromFormat('H:i', $endTimeInput);
            }

            // Check if parsing was successful
            if ($startTime === false || $endTime === false) {
                return redirect()->back()->withErrors('Invalid time format. Please use "h:i A" (e.g., "02:30 PM") or "H:i" (e.g., "14:30").')->withInput();
            }

            // Format times to 24-hour format
            $startTimeFormatted = $startTime->format('H:i');
            $endTimeFormatted = $endTime->format('H:i');

            // Update the WorkingTime item
            $WorkingTimeitem = WorkingTime::findOrFail($request->id_edit);
            $WorkingTimeitem->name = $request->name_edit;
            $WorkingTimeitem->start_time = $startTimeFormatted;
            $WorkingTimeitem->end_time = $endTimeFormatted;
            $WorkingTimeitem->color = $request->color_edit;
            $WorkingTimeitem->save();

            return redirect()->route('working_time.index')->with('success', 'تم التعديل بنجاح');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors($e->getMessage());
        }
    }



    // public function update(Request $request)
    // {
    //     // dd($request);
    //     $messages = [
    //         'name_edit.required' => 'الاسم  مطلوب ولا يمكن تركه فارغاً.',
    //         'start_time_edit.required' => 'بداية فترة العمل   مطلوب ولا يمكن تركه فارغاً.',
    //         'end_time_edit.required' => 'نهاية فترة العمل   مطلوب ولا يمكن تركه فارغاً.',
    //     ];
    //     $validatedData = Validator::make($request->all(), [
    //         'name_edit' => 'required',
    //         'start_time_edit' => 'required',
    //         'end_time_edit' => 'required',
    //     ], $messages);
    //     // Handle validation failure
    //     if ($validatedData->fails()) {
    //         return redirect()->back()->withErrors($validatedData)->withInput();
    //     }
    //     try {
    //         $WorkingTimeitem = WorkingTime::findOrFail($request->id_edit);
    //         $WorkingTimeitem->name = $request->name_edit;
    //         $WorkingTimeitem->start_time = $request->start_time_edit;
    //         $WorkingTimeitem->end_time = $request->end_time_edit;
    //         $WorkingTimeitem->save();
    //         // Dynamically create model instance based on the model class string
    //         return view('working_time.index')->with('success', 'Permission created successfully.');
    //     } catch (\Exception $e) {
    //         return response()->json($e->getMessage());
    //     }
    // }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(WorkingTime $workingTime)
    {
        //
    }

    public function delete(Request $request)
    {        
        $type = WorkingTime::find($request->id);
        if (!$type) {
            return redirect()->route('working_time.index')->with('reject','يوجد خطا الرجاء المحاولة مرة اخرى');
        }

        $workingTreeTimes = $type->workingTreeTimes()->exists();
        if ($workingTreeTimes) {
            return redirect()->route('working_time.index')->with('reject','لا يمكن حذف هذه فترة العمل يوجد نظام عمل لها');
        }

        $inspectorMissions = $type->inspectorMissions()->exists();
        if ($inspectorMissions) {
            return redirect()->route('working_time.index')->with('reject','لا يمكن حذف هذه فترة العمل يوجد جدول لها');
        }

        $type->delete();
        return redirect()->route('working_time.index')->with('success', 'تم حذف فترة العمل');
    }
}
