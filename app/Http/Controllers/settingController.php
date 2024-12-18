<?php

namespace App\Http\Controllers;

use App\DataTables\gradeDataTable;
use App\DataTables\jobDataTable;
use App\DataTables\VacationDataTable;
use App\DataTables\vacationTypeDataTable;
use App\Http\Controllers\Controller;
use App\Models\Government;
use App\Models\grade;
use App\Models\Grouppoint;
use App\Models\Groups;
use App\Models\InspectorMission;
use App\Models\job;
use App\Models\Setting;
use App\Models\VacationType;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class settingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }


    //START JOB
    //show JOB
    public function indexjob()
    {
        return view("jobs.index");
    }
    //create JOB
    public function createjob()
    {
        return view("jobs.add");
    }

    //get data for JOB
    public function getAllJob()
    {
        $data = job::orderBy('updated_at', 'desc')->orderBy('created_at', 'desc')->get();

        return DataTables::of($data)->addColumn('action', function ($row) {
            $name = "'$row->name'";
            $edit_permission = null;
            $delete_permission = null;
            if (Auth::user()->hasPermission('edit job')) {
                $edit_permission = '<a class="btn btn-sm"  style="background-color: #F7AF15;"  onclick="openedit(' . $row->id . ',' . $name . ')">  <i class="fa fa-edit"></i> تعديل </a>';
            }
            if (Auth::user()->hasPermission('delete job')) {
                $delete_permission = ' <a class="btn  btn-sm" style="background-color: #C91D1D;"   onclick="opendelete(' . $row->id . ')"> <i class="fa-solid fa-trash"></i> حذف</a>';
            }
            $uploadButton = $edit_permission . $delete_permission;
            return $uploadButton;
        })
            ->rawColumns(['action'])
            ->make(true);
    }
    //add JOB
    public function addJob(Request $request)
    {
        $rules = [
            'nameadd' => 'required|string',
        ];

        $messages = [
            'nameadd.required' => 'يجب ادخال الوظيفه ',
        ];

        $validatedData = Validator::make($request->all(), $rules, $messages);
        if ($validatedData->fails()) {
            return response()->json(['success' => false, 'message' => $validatedData->errors()]);
        }
        $requestinput = $request->except('_token');
        $job = new job();
        $job->name = $request->nameadd;
        $job->save();
        $message = "تم اضافه الوظيفه";
        return redirect()->route('job.index', compact('message'));
        //return redirect()->back()->with(compact('activeTab','message'));
    }
    //show JOB
    public function showjob($id)
    {
        $data = job::findOrFail($id);
        return view("jobs.show", compact("data"));
    }
    //edit JOB
    public function editjob($id)
    {
        $data = job::findOrFail($id);
        return view("jobs.edit", compact("data"));
    }
    //update JOB
    public function updateJob(Request $request)
    {
        $job = job::find($request->id);

        if (!$job) {
            return response()->json(['error' => 'Grade not found'], 404);
        }
        $job->name = $request->name;
        $job->save();
        $message = '';
        return redirect()->route('job.index', compact('message'));
        // return redirect()->back()->with(compact('activeTab'));

    }

    //delete JOB
    public function deletejob(Request $request)
    {
        $type = job::find($request->id);
        if (!$type) {
            return redirect()->route('job.index')->with(['message' => 'يوجد خطا الرجاء المحاولة مرة اخرى']);
        }

        $users = $type->users()->exists();
        if ($users) {
            return redirect()->route('job.index')->with(['message' => 'لا يمكن حذف هذه الوظيفة يوجد موظفين لها']);
        }

        $type->delete();
        return redirect()->route('job.index')->with(['message' => 'تم حذف الوظيفه']);
    }
    //END JOB

    //START GRAD
    //show GRAD
    public function indexgrads()
    {
        return view("grads.index");
    }
    //create GRAD
    public function creategrads()
    {
        return view("grads.add");
    }

    //get data for GRAD
    public function getAllgrads()
    {
        $data = grade::orderBy('updated_at', 'desc')->orderBy('created_at', 'desc')->get();

        return DataTables::of($data)->addColumn('action', function ($row) {
            $name = "'$row->name'";
            $edit_permission = null;
            $delete_permission = null;
            if (Auth::user()->hasPermission('edit grade')) {
                $edit_permission = '<a class="btn btn-sm"  style="background-color: #F7AF15;"  onclick="openedit(' . $row->id . ',' . $name . ',' . $row->type . ')">  <i class="fa fa-edit"></i> تعديل </a>';
            }
            if (Auth::user()->hasPermission('delete grade')) {
                $delete_permission = ' <a class="btn  btn-sm" style="background-color: #C91D1D;"   onclick="opendelete(' . $row->id . ')"> <i class="fa-solid fa-trash"></i> حذف</a>';
            }
            $uploadButton = $edit_permission . $delete_permission;
            return $uploadButton;
        })
            ->addColumn('type', function ($row) {
                if ($row->type == 0) $mode = 'ظابط';
                elseif ($row->type == 1) $mode = 'صف ظابط';
                else $mode = 'فرد';
                return $mode;
            })
            ->rawColumns(['action'])
            ->make(true);
    }
    //add GRAD
    public function addgrads(Request $request)
{
    $rules = [
        'nameadd' => ['required', 'string', 'min:1', 'regex:/\S+/'], // Ensures non-whitespace input
        'typeadd' => 'required', // Validates typeadd to ensure a proper selection
    ];

    $messages = [
        'nameadd.required' => 'الاسم مطلوب.',
        'nameadd.regex' => 'الاسم لا يمكن أن يحتوي على مسافات فقط.',
        'typeadd.required' => 'نوع الرتبة مطلوب.',
    ];

    $validatedData = Validator::make($request->all(), $rules, $messages);

    if ($validatedData->fails()) {
        return response()->json(['success' => false, 'message' => $validatedData->errors()]);
    }

    // Save the grade
    $grade = new Grade();
    $grade->name = $request->nameadd;
    $grade->type = $request->typeadd;
    $grade->save();

    return redirect()->route('grads.index')->with('message', 'تم اضافه الرتبه');
}

    //show GRAD
    public function showgrads($id)
    {
        $data = grade::findOrFail($id);
        return view("grads.show", compact("data"));
    }
    //edit GRAD
    public function editgrads($id)
    {
        $data = grade::findOrFail($id);
        return view("grads.edit", compact("data"));
    }
    //update GRAD
    public function updategrads(Request $request)
    {
        $grade = grade::find($request->id);

        if (!$grade) {
            return response()->json(['error' => 'عفوا هذه الرتبه غير موجوده'], 404);
        }
        $grade->name = $request->name;
        $grade->type = $request->typeedit;
        $grade->save();
        $message = 'تم تعديل الرتبه';
        return redirect()->route('grads.index', compact('message'));
        // return redirect()->back()->with(compact('activeTab'));

    }

    //delete GRAD
    public function deletegrads(Request $request)
    {
        try{
            $type = grade::find($request->id);
            if (!$type) {
                return redirect()->route('grads.index')->with(['message' => 'يوجد خطا الرجاء المحاولة مرة اخرى']);
            }

            $users = $type->users()->exists();
            if ($users) {
                return redirect()->route('grads.index')->with(['message' => 'لا يمكن حذف هذه الرتبه يوجد موظفين لها']);
            }

            $absenceEmployees = $type->absenceEmployees()->exists();
            if ($absenceEmployees) {
                return redirect()->route('grads.index')->with(['message' => 'لا يمكن حذف هذه الرتبه يوجد غياب لها']);
            }

            $attendanceEmployees = $type->attendanceEmployees()->exists();
            if ($attendanceEmployees) {
                return redirect()->route('grads.index')->with(['message' => 'لا يمكن حذف هذه الرتبه يوجد حضور لها']);
            }

            $violations = $type->violations()->exists();
            if ($violations) {
                return redirect()->route('grads.index')->with(['message' => 'لا يمكن حذف هذه الرتبه يوجد جزاءات لها']);
            }

            $type->delete();
            return redirect()->route('grads.index')->with(['message' => 'تم حذف الرتبه']);
        } catch (\Exception $e) {
            return redirect()->route('grads.index')->with(['message' => 'يوجد خطا الرجاء المحاولة مرة اخرى']);
        }
    }
    //END GRAD

    //START VACATION TYPE
    //show JOB
    public function indexvacationType()
    {

        return view("vacationType.index");
    }
    //create JOB
    public function createvacationType()
    {
        return view("vacationType.add");
    }

    //get data for JOB
    public function getAllvacationType()
    {

        $data = VacationType::orderBy('updated_at', 'desc')->orderBy('created_at', 'desc')->get();


        return DataTables::of($data)->addColumn('action', function ($row) {
            $hiddenIds = [1, 2, 3, 4];
            $name = "'$row->name'";
            $edit_permission = null;
            $delete_permission = null;
            if (Auth::user()->hasPermission('edit VacationType')) {
                $edit_permission = '<a class="btn btn-sm"  style="background-color: #F7AF15;"  onclick="openedit(' . $row->id . ',' . $name . ')">  <i class="fa fa-edit"></i> تعديل </a>';
            }
            if (Auth::user()->hasPermission('delete VacationType')) {
                if (!in_array($row->id, $hiddenIds)) {
                    $delete_permission = ' <a class="btn  btn-sm" style="background-color: #C91D1D;"   onclick="opendelete(' . $row->id . ')"> <i class="fa-solid fa-trash"></i> حذف</a>';
                }
            }
            return $edit_permission . $delete_permission;
        })
            ->addColumn('flag', function ($row) {
                return ($row->flag) ? 'نعم' : 'لا';
            })
            ->rawColumns(['action'])
            ->make(true);
    }
    //add JOB
    public function addvacationType(Request $request)
    {
        $rules = [
            'nameadd' => 'required|string',
        ];

        $messages = [
            'nameadd.required' => 'يجب ادخال نوع الأجازه ',
        ];

        $validatedData = Validator::make($request->all(), $rules, $messages);
        if ($validatedData->fails()) {
            return response()->json(['success' => false, 'message' => $validatedData->errors()]);
        }
        $requestinput = $request->except('_token');
        $job = new VacationType();
        $job->name = $request->nameadd;
        $job->flag = (isset($request->flag) && $request->flag) ? 1 : 0;
        $job->save();

        $message = "تم اضافة نوع الأجازه";
        return redirect()->route('vacationType.index', compact('message'));
        //return redirect()->back()->with(compact('activeTab','message'));
    }
    //show JOB
    public function showvacationType($id)
    {
        $data = VacationType::findOrFail($id);
        return view("vacationType.show", compact("data"));
    }
    //edit JOB
    public function editvacationType(Request $request)
    {
        $data = VacationType::findOrFail($request->id);
        return view("vacationType.edit", compact("data"));
    }
    //update JOB
    public function updatevacationType(Request $request)
    {
        $job = VacationType::find($request->id);

        if (!$job) {
            return response()->json(['error' => 'هذه الأجازه غير موجوده'], 404);
        }
        $job->name = $request->name;
        $job->flag = $request->flag;

        $job->save();
        $message = 'تم تعديل نوع الأجازه';
        return redirect()->route('vacationType.index', compact('message'));
        // return redirect()->back()->with(compact('activeTab'));

    }

    //delete JOB
    public function deletevacationType(Request $request)
    {
        $type = VacationType::find($request->id);
        if (!$type) {
            return redirect()->back()->with(['message' => 'يوجد خطا الرجاء المحاولة مرة اخرى']);
        }

        $employeeVacations = $type->employeeVacations()->exists();
        if ($employeeVacations) {
            return redirect()->back()->with(['message' => 'لا يمكن حذف هذه نوع الاجازه يوجد موظفين لها']);
        }
        $type->delete();
        return redirect()->route('vacationType.index')->with(['message' => 'تم حذف نوع الاجازه']);
    }
    //END VACATION TYPE



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $yesterday = '2024-08-13';
        $today = '2024-08-14';
        $allGovernments = Government::pluck('id')->toArray();
        foreach ($allGovernments as $government) {
            $allAvailablePoints = Grouppoint::where('government_id', $government)->pluck('id')->toArray();
            $allGroupsForGovernment = Groups::where('government_id', $government)->select('id', 'points_inspector')->get();

            foreach ($allGroupsForGovernment as $group) {

                $groupTeams = InspectorMission::where('group_id', $group->id)
                    ->select('group_team_id', 'ids_group_point')->whereDate('date', $yesterday)
                    ->distinct('group_team_id')
                    ->get();

                foreach ($groupTeams as $groupTeam) {
                    $pointPerTeam = $group->points_inspector;
                    if (!empty($allAvailablePoints)) {

                        // Get random keys from the available points
                        $randomKeys = array_rand($allAvailablePoints, $pointPerTeam);

                        if ($pointPerTeam == 1) {

                            $randomKeys = [$randomKeys];
                        } else {
                            $randomKeys = array_rand($allAvailablePoints, $pointPerTeam);
                        }

                        // Map the random keys to their corresponding values in the $allAvailablePoints array
                        $pointTeam = array_map(function ($key) use ($allAvailablePoints) {
                            return $allAvailablePoints[$key];
                        }, $randomKeys);

                        $allAvailableteam = array_diff($pointTeam, $groupTeam->ids_group_point);

                        //dd(implode(', ', $pointTeam) . '-old-' . implode(', ', $groupTeam->ids_group_point) . '-diferent-' . implode(', ', $allAvailablePoint));
                        if (count($allAvailableteam) == count($pointTeam)) {

                            //dd($group->id . "  /   ".$groupTeam->group_team_id);
                            $upatedMissions = InspectorMission::where('group_id', $group->id)->where('group_team_id', $groupTeam->group_team_id)->where('date', $today)->pluck('id')->toArray();
                            foreach ($upatedMissions as $upatedMission) {
                                $upated = InspectorMission::find($upatedMission);
                                if ($upated) {

                                    // Update the ids_group_point field
                                    $upated->ids_group_point = $pointTeam;

                                    // Save the updated record
                                    $upated->save();
                                }
                                $allAvailablePoints = array_diff($allAvailablePoints, $pointTeam);
                                // dd($allAvailablePoints);
                            }
                            // dd('upatedMissions');
                        } else {

                            // dd('7' . "  / " . implode(', ', $allAvailablePoints));
                            if (count($allAvailablePoints) <= 1) {

                                $pointTeam = [];
                                break;
                            } else {
                                // Get random keys from the available points
                                $randomKeys = array_rand($allAvailablePoints, $pointPerTeam);
                                //dd('7 ^' . "  / " . implode(', ', $randomKeys));
                                if ($pointPerTeam == 1) {
                                    // If only one key is selected, convert it to an array
                                    $randomKeys = [$randomKeys];
                                }

                                // Map the random keys to their corresponding values in the $allAvailablePoints array
                                $pointTeam = array_map(function ($key) use ($allAvailablePoints) {
                                    return $allAvailablePoints[$key];
                                }, $randomKeys);
                                $allAvailableteam = array_diff($pointTeam, $groupTeam->ids_group_point);
                            }
                        }
                    } else {
                        // dd('k');
                        $upatedMissions = InspectorMission::where('group_id', $group->id)->where('group_team_id', $groupTeam->group_team_id)->where('date', $today)->pluck('id')->toArray();
                        foreach ($upatedMissions as $upatedMission) {
                            $upated = InspectorMission::find($upatedMission);
                            if ($upated) {
                                // Update the ids_group_point field
                                $upated->ids_group_point = [];

                                // Save the updated record
                                $upated->save();
                            }
                        }
                    }
                }
            }
        }
    }
    public function allSettings()
    {
        return view('setting.index');
    }
    public function getSettings()
    {
        $data = Setting::all();

        return DataTables::of($data)
            ->rawColumns(['action'])
            ->make(true);
    }


    public function CreateSetting(Request $request)
    {
        $messages = [
            'key.required' => 'الاسم مطلوب ولا يمكن تركه فارغاً.',
            'value.required' => 'القيمة مطلوبة',
            'key.unique' => 'الاسم موجود بالفعل'

        ];

        $validatedData = Validator::make($request->all(), [
            'key' => 'required|unique:settings,key',
            'value' => 'required',

        ], $messages);

        // dd($validatedData);
        // Handle validation failure
        if ($validatedData->fails()) {
            // session()->flash('errors', $validatedData->errors());
            return redirect()->back()->withErrors($validatedData)->withInput()->with('showModal', true);
        }
        $Setting = new Setting();
        $Setting->key = $request->key;
        $Setting->value = $request->value;
        $Setting->save();
        session()->flash('success', 'تم اضافه اعداد بنجاح.');

        return redirect()->route('settings.index');
    }
    public function UpdateSetting(Request $request)
    {
        //dd($request);
        $messages = [
            'key.required' => 'الاسم مطلوب ولا يمكن تركه فارغاً.',
            'value.required' => 'القيمة مطلوبة',

        ];

        $validatedData = Validator::make($request->all(), [
            'key' => 'required',
            'value' => 'required',

        ], $messages);

        // dd($validatedData);
        // Handle validation failure
        if ($validatedData->fails()) {
            // session()->flash('errors', $validatedData->errors());
            return redirect()->back()->withErrors($validatedData)->withInput()->with('editModal', true);
        }
        $Setting  = Setting::find($request->id_edit);
        $Setting->key = $request->key;
        $Setting->value = $request->value;
        $Setting->save();

        session()->flash('success', 'تم تعديل اعداد بنجاح.');

        return redirect()->route('settings.index');
    }
    public function deleteSetting(Request $request)
    {

        $setting = Setting::find($request->id);
        $setting->delete();

        session()->flash('success', 'تم حذف الاعداد بنجاح.');

        return redirect()->route('settings.index');
    }
}
