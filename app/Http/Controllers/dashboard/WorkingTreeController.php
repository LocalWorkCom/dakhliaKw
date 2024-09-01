<?php

namespace App\Http\Controllers\dashboard;

use App\DataTables\IoTelegramDataTable;
use App\Http\Controllers\Controller;
use App\Models\InspectorMission;
use App\Models\WorkingTime;
use App\Models\WorkingTree;
use App\Models\WorkingTreeTime;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class WorkingTreeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('workingTree.index');
    }

    public function getWorkingTrees()
    {
        $WorkingTrees = WorkingTree::orderBy('created_at', 'desc')
            ->get();

        return DataTables::of($WorkingTrees)
            ->rawColumns(['action'])
            ->make(true);
    }
    public function create()
    {
        $WorkingTimes = WorkingTime::all();
        return view('workingTree.add', compact('WorkingTimes'));
    }
    public function store(Request $request)
    {
        // Define the base validation rules
        $rules = [
            'name' => 'required|string',
            'working_days_num' => 'required|integer|min:1',
        ];

        $messages = [
            'name.required' => 'يجب ادخال اسم الادارة',
            'name.string' => 'يجب ادخال اسم الادارة',
            'working_days_num.required' => 'يجب ادخال عدد ايام العمل',
            'working_days_num.integer' => 'يجب ادخال رقم في عدد ايام العمل',
        ];

        // Dynamically add rules for holiday checkboxes and working times
        for ($i = 1; $i <= $request->working_days_num; $i++) {
            $holidayCheckbox = "holiday_checkbox" . $i;
            $holidayPeriod = "period" . $i;

            if ($request->has($holidayCheckbox) && $request->input($holidayCheckbox) === 'on') {
                // If holiday checkbox is checked, working time ID should be null
                $rules[$holidayPeriod] = 'nullable|exists:working_times,id';  // Allow null or existing working time
            } else {
                // If holiday checkbox is not checked, working time ID is required
                $rules[$holidayPeriod] = 'required|exists:working_times,id';
            }

            // Add custom messages for the working time ID validation
            $messages[$holidayPeriod . '.required'] = 'يجب اختيار وقت العمل للمدة ' . $i;
            $messages[$holidayPeriod . '.exists'] = 'وقت العمل المحدد غير موجود.';
        }
        // Check dynamic rules
        // $working_days_num = $request->input('working_days_num');
        // for ($i = 1; $i <= $working_days_num; $i++) {
        //     $rules["period{$i}"] = 'nullable|exists:working_times,id'; // Only required if not checked
        //     if ($request->input("holiday_checkbox{$i}") != 'on') {
        //         $rules["period{$i}"] = 'required|exists:working_times,id'; // Required if not holiday
        //     }
        // }

        // Validate the request data
        $validatedData = Validator::make($request->all(), $rules, $messages);

        if ($validatedData->fails()) {
            session()->flash('errors', $validatedData->errors());
            return redirect()->back()->withInput();
        }

        $holiday_days_num = 0;

        $WorkingTree = new WorkingTree;
        $WorkingTree->name = $request->name;
        $WorkingTree->working_days_num = $request->working_days_num;
        $WorkingTree->holiday_days_num = $holiday_days_num;
        $WorkingTree->created_by = auth()->id();
        $WorkingTree->created_departement = auth()->user()->department_id;
        $WorkingTree->save();

        for ($i = 1; $i <= $request->working_days_num; $i++) {
            $holidayCheckbox = "holiday_checkbox" . $i;
            $holidayPeriod = "period" . $i;

            // Create WorkingTreeTime entry
            $WorkingTreeTime = new WorkingTreeTime;
            $WorkingTreeTime->working_tree_id = $WorkingTree->id;
            $WorkingTreeTime->day_num = $i;
            $WorkingTreeTime->created_by = auth()->id();
            $WorkingTreeTime->created_departement = auth()->user()->department_id;

            // Check if holiday checkbox is checked
            if ($request->has($holidayCheckbox) && $request->input($holidayCheckbox) === 'on') {
                $WorkingTreeTime->is_holiday = 1;
                $holiday_days_num++;
                $WorkingTreeTime->working_time_id = null; // Set working time ID to null for holidays
            } else {
                $WorkingTreeTime->is_holiday = 0;
                $WorkingTreeTime->working_time_id = $request->input($holidayPeriod); // Set the period if available
            }

            $WorkingTreeTime->save();
        }

        $WorkingTree->holiday_days_num = $holiday_days_num;

        $WorkingTree->working_days_num = $request->working_days_num - $holiday_days_num;
        $WorkingTree->save();

        session()->flash('success', 'تم الحفظ بنجاح.');

        return redirect()->route('working_trees.list');
    }

    public function edit($id)
    {
        $workingTimes = WorkingTime::all();
        $workingTree = WorkingTree::with('workingTreeTimes')->where('id', $id)->first();
        return view('workingTree.edit', compact('workingTimes', 'workingTree'));
    }

    public function update(Request $request, $id)
    {
        $rules = [
            'name' => 'required|string',
            'working_days_num' => 'required|integer|min:1',
        ];

        $messages = [
            'name.required' => 'يجب ادخال اسم الادارة',
            'name.string' => 'يجب ادخال اسم الادارة',
            'working_days_num.required' => 'يجب ادخال عدد ايام العمل',
            'working_days_num.integer' => 'يجب ادخال رقم في عدد ايام العمل',
        ];

        // Dynamically add validation rules for working days periods
        for ($i = 1; $i <= $request->working_days_num; $i++) {
            $holidayCheckbox = "holiday_checkbox" . $i;
            $holidayPeriod = "period" . $i;

            $rules[$holidayPeriod] = $request->has($holidayCheckbox) && $request->input($holidayCheckbox) === 'on'
                ? 'nullable|exists:working_times,id'
                : 'required|exists:working_times,id';

            $messages[$holidayPeriod . '.required'] = 'يجب اختيار وقت العمل للمدة ' . $i;
            $messages[$holidayPeriod . '.exists'] = 'وقت العمل المحدد غير موجود.';
        }

        $validatedData = Validator::make($request->all(), $rules, $messages);

        if ($validatedData->fails()) {
            session()->flash('errors', $validatedData->errors());
            return redirect()->back()->withInput();
        }

        $holiday_days_num = 0;
        $WorkingTree = WorkingTree::find($id);
        $old_work_days = $WorkingTree->working_days_num;
        $old_holiday_days = $WorkingTree->holiday_days_num;
        $total_old_days = $old_holiday_days + $old_work_days;
        $WorkingTree->name = $request->name;
        $WorkingTree->working_days_num = $request->working_days_num;
        $WorkingTree->holiday_days_num = $holiday_days_num;
        $WorkingTree->save();

        // Delete old WorkingTreeTime entries that are no longer needed
        WorkingTreeTime::where('working_tree_id', $id)
            ->where('day_num', '>', $request->working_days_num)
            ->delete();

        // Process each working day
        for ($i = 1; $i <= $request->working_days_num; $i++) {
            $holidayCheckbox = "holiday_checkbox" . $i;
            $holidayPeriod = "period" . $i;
            $workingTimeId = $request->input($holidayPeriod);
            $isHoliday = $request->has($holidayCheckbox) && $request->input($holidayCheckbox) === 'on';


            // Update or create WorkingTreeTime entries
            $workingTreeTime = WorkingTreeTime::updateOrCreate(
                [
                    'working_tree_id' => $id,
                    'day_num' => $i,
                ],
                [
                    'working_time_id' => $isHoliday ? null : $workingTimeId,
                    'is_holiday' => $isHoliday ? 1 : 0,
                    'created_by' => auth()->id(),
                    'created_departement' => auth()->user()->department_id,
                ]
            );

            if ($isHoliday) {
                $holiday_days_num++;
            }

        }

        // Update the holiday_days_num field of the WorkingTree
        $WorkingTree->holiday_days_num = $holiday_days_num;
        $WorkingTree->working_days_num = $request->working_days_num - $holiday_days_num;
        $WorkingTree->save();
        $found = false;
        $second = false;
        $total_new_days = $request->working_days_num;
        if ($total_old_days < $total_new_days) {
            $diff = $total_new_days - $total_old_days;
            $temp_diff = $diff;
            $missions = InspectorMission::where('date', '>=', today())
                ->where('working_tree_id', $id)
                ->orderBy('inspector_id')
                ->orderBy('date')
                ->get();

            foreach ($missions as $mission) {
                if ($diff == 0) {
                    $total_old_days = 1;
                    $total_new_days = $request->working_days_num;
                    $diff = $total_new_days - $total_old_days + 1;
                    $second = true;
                }
                if ($mission->day_number == $total_old_days && !$second) {
                    $found = true; // Set the flag when the first matching mission is found
                    continue; // Skip this mission as we need to start updating from the next one
                }
                if ($found && $diff > 0) {
                    if (!$second) {

                        $total_old_days++;
                    }
                    if ($total_old_days > $total_new_days) {
                        break;
                    }
                    $work_tree_time = WorkingTreeTime::where('working_tree_id', $id)
                        ->where('day_num', '=', $total_old_days)
                        ->first();
                    // $day_number = $work_tree_time->day_num;
                    if ($work_tree_time) {
                       
                        // Update the mission with the corresponding working time data
                        $mission->update([
                            'day_off' => $work_tree_time->is_holiday,
                            'day_number' => $work_tree_time->day_num,
                            'working_time_id' => $work_tree_time->is_holiday ? null : $work_tree_time->working_time_id,
                        ]);
                    }

                    $diff--; // Decrease the remaining difference
                    if ($second) {
                        $total_old_days++;
                    }
                }
            }
        }
        //  else {

        //     $missions = InspectorMission::where('date', '>=', today())
        //         ->where('working_tree_id', $id)
        //         ->orderBy('inspector_id')
        //         ->orderBy('date')
        //         ->get();

        //     foreach ($missions as $mission) {
        //         if ($mission->day_number == $total_new_days) {
        //             $found = true; // Set the flag when the first matching mission is found
        //             $total_new_days = 1;
        //             continue; // Skip this mission as we need to start updating from the next one
        //         }
        //         if($mission->day_number > $total_new_days){
        //             $total_new_days = 1;
        //         }
        //         if ($found) {

        //             $work_tree_time = WorkingTreeTime::where('working_tree_id', $id)
        //                 ->where('day_num', '=', $total_new_days)
        //                 ->first();
        //             if ($work_tree_time) {
                   
        //                 // Update the mission with the corresponding working time data
        //                 $mission->update([
        //                     'day_off' => $work_tree_time->is_holiday,
        //                     'day_number' => $work_tree_time->day_num,
        //                     'working_time_id' => $work_tree_time->is_holiday ? null : $work_tree_time->working_time_id,
        //                 ]);
        //             }

        //             $total_new_days++;
        //         }
        //     }
        // }

        session()->flash('success', 'تم التعديل بنجاح.');

        return redirect()->route('working_trees.list');
    }

    public function show($id)
    {
        $WorkingTree = WorkingTree::find($id);


        return view('workingTree.show', compact('WorkingTree'));
    }
}
