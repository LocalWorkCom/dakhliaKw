<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\User;
use App\Models\grade;
use App\Models\GroupTeam;
use App\Models\Inspector;
use App\Models\Violation;
use Illuminate\Http\Request;
use App\Models\instantmission;
use App\Models\ViolationTypes;
use App\Models\InspectorMission;
use App\Http\Controllers\Controller;
use App\Models\Point;
use App\Models\PointDays;
use App\Models\Setting;
use App\Models\WorkingTime;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


class ViolationController  extends Controller
{
    //
    public function todayIndex($today)
    {
        $daysOfWeek = [

            "الأحد",
            "الاثنين",
            "الثلاثاء",
            "الأربعاء",
            "الخميس",
            "الجمعة",
            "السبت",
        ];

        $todayDate = Carbon::parse($today);
        $dayWeek = $todayDate->locale('ar')->dayName;
        $index = array_search($dayWeek, $daysOfWeek);

        return $index !== false ? $index : null;
    }
    function isTimeAvailable($pointStart, $pointEnd)
    {
        $currentTime = Carbon::now()->format('H:i');
        // dd($pointStart, $pointEnd,$currentTime);
        // Convert the times to Carbon instances for easy comparison
        $start = Carbon::createFromTimeString($pointStart);
        $end = Carbon::createFromTimeString($pointEnd)->addMinutes(30);
        $current = Carbon::createFromTimeString($currentTime);

        return $current->between($start, $end);
    }
    public function get_Violation_type(Request $request)
    {
        // type_id : department_id
        $messages = [
            'type.required' => 'type required',
        ];
        $validatedData = Validator::make($request->all(), [
            'type' => 'required',
        ], $messages);

        if ($validatedData->fails()) {
            return $this->respondError('Validation Error.', $validatedData->errors(), 400);
        }

        $type = $request->type;

        $allViolationType = ViolationTypes::whereJsonContains('type_id', $type)->get();
        if ($allViolationType->isNotEmpty()) {

            $type = ViolationTypes::where('type_id', '0')->get();
            $grade = grade::where('type', 0)->get();
            if ($grade->isNotEmpty()) {


                $success['grade2'] = $grade->map(function ($item) {
                    return $item->only(['id', 'name']);
                });
            } else {
                $success['grade2'] = '';
            }
            $grade3 = grade::where('type', 2)->get();

            if ($grade->isNotEmpty()) {
                $success['grade3'] = $grade3->map(function ($item) {
                    return $item->only(['id', 'name']);
                });
            } else {
                $success['grade3'] = '';
            }
            if ($type->isNotEmpty()) {
                $success['type'] = $type->map(function ($item) {
                    return $item->only(['id', 'name']);
                });
            } else {
                $success['type'] = '';
            }

            $success['ViolationType'] = $allViolationType->map(function ($item) {
                return $item->only(['id', 'name']);
            });
            // dd( $success['grade2'],  $success['grade3'] ,$success['type'] , $success['ViolationType']);

            return $this->respondSuccess($success, 'Get Data successfully.');
        } else {
            return $this->respondError('type not found', ['error' => 'خطأ فى استرجاع البيانات'], 404);
        }

        //
    }

    public function add_Violation(Request $request)
    {

        $messages = [
            'type.required' => 'type required',
            'flag_instantmission.required' => 'flag instantmission required',
        ];
        $validatedData = Validator::make($request->all(), [
            'type' => 'required',
            'flag_instantmission' => 'required',
        ], $messages);

        if ($validatedData->fails()) {
            return $this->respondError('Validation Error.', $validatedData->errors(), 400);
        }
        // 1=> this violation of instant mission
        if ($request->flag_instantmission == "1") {
            $point_id = Null;
        } else {
            $point_id = $request->point_id;
        }
        /**
         * civil_military->1 فرد   (رتبه - رقم عسكرى - رقم ملف)
         * civil_military->2 ضابط  (رتبه - رقم ملف )
         * civil_military->3 مهنى  (رقم عسكرى - رقم ملف - رقم مدني  )
         * civil_military->1 مدني ( رقم مدني)
         */
        if ($request->civil_military == 1  || $request->civil_military == 3) {
            //عسكري
            $military_number = $request->military_number;
            $Civil_number = $request->Civil_number;
            $file_num = $request->file_num;
        } elseif ($request->civil_military == 2 || $request->civil_military == 4) {
            //ظابط ||مهنيين ||أفراد
            $military_number = null;
            $Civil_number = $request->Civil_number;
            $file_num = $request->file_num;
        }

        // 1 => مخالفة سلوك
        if ($request->type == "1") {
            // dd($request);
            $messages = [
                'name.required' => 'الاسم  مطلوب ولا يمكن تركه فارغاً.',
                'name.string' => 'الاسم  يجب أن يكون نصاً.',
                // 'military_number.required_if' => 'رقم العسكري مطلوب ولا يمكن تركه فارغاً.',
                // 'grade.required' => 'الرتبة  مطلوب ولا يمكن تركه فارغاً.',
                'image.required' => 'المرفقات مطلوبة',
                'violation_type.required' => 'نوع المخالفة مطلوب',
                // 'Civil_number.required' => 'رقم المدنى مطلوب ولا يمكن تركه فارغاً   .',
                'point_id.required_if' => 'رقم النقطة  مطلوب',
                'mission_id.required' => 'رقم المهمة  مطلوب',
            ];
            $validatedData = Validator::make($request->all(), [
                // 'military_number' => 'required',
                // 'military_number' => ['required_if:civil_military,military'],
                'name' => 'required|string',
                // 'grade' => ['required_if:civil_military,military'],
                'image' => 'nullable',
                ///'violation_type' => 'required',
                // 'Civil_number' => 'required',
                'point_id' => ['required_if:flag_instantmission,0'],
                'mission_id' => 'required',

            ], $messages);

            if ($validatedData->fails()) {
                return $this->respondError('Validation Error.', $validatedData->errors(), 400);
            }
            $today = Carbon::now()->toDateString();
            $index = $this->todayIndex($today);
            // $point=Point::with('pointDays')->where('id',$point_id)->first();
            $point = Point::find($point_id);
            if ($point && $point->work_type == 1) {
                $pointDay = $point->pointDays->where('name', $index)->first();
                $workTime = PointDays::where('point_id', $point_id)->where('name', $index)->first();
                $startTime = $workTime->from;
                $endtTime = $workTime->to;;
                $is_avilable = $this->isTimeAvailable($startTime, $endtTime);
                if (!$is_avilable) {
                    return $this->respondError('failed to save', ['error' => 'انتهت مواعيد عمل النقطه'], 404);
                }
            }
            $idsArray = array_map('intval', explode(',', $request->violation_type));
            $cleanedString = implode(",", $idsArray);

            $new = new Violation();
            $new->name = $request->name;
            $new->military_number = $military_number ?? null;
            $new->Civil_number = $Civil_number ?? null;
            $new->file_num = $file_num ?? null;
            $new->grade = $request->grade;
            $new->mission_id = $request->mission_id;
            $new->point_id = $point_id;
            $new->civil_type = $request->civil_military;
            $new->violation_type = $cleanedString;
            $new->flag_instantmission = $request->flag_instantmission;
            $new->description = $request->description ?? null;
            $new->flag = 1;
            $new->user_id = auth()->user()->id;
            // $new->user_id = 1;
            $new->save();

            if ($request->hasFile('image')) {
                $files = $request->file('image'); // Expecting an array of files
                $path = 'Api/images/violations'; // Directory path
                $model = Violation::find($new->id);

                UploadFilesIM($path, 'image', $model, $files);
            }
        } else {

            $messages = [
                'image.required' => 'المرفقات مطلوبة',
                'violation_type.required' => 'نوع المخالفة مطلوب',
                'point_id.required_if' => 'رقم النقطة  مطلوب',
                'mission_id.required' => 'رقم المهمة  مطلوب',
            ];
            $validatedData = Validator::make($request->all(), [
                'image' => 'required',
                // 'violation_type' => 'required',
                // 'point_id' => 'required',
                'point_id' => ['required_if:flag_instantmission,0'],
                'mission_id' => 'required',
            ], $messages);

            if ($validatedData->fails()) {
                return $this->respondError('Validation Error.', $validatedData->errors(), 400);
            }

            $new = new Violation();
            // $new->name = $request->name;
            // $new->military_number = $request->military_number;
            // $new->Civil_number = $request->Civil_number;
            // $new->grade = $request->grade;
            // // $new->image = $request->image;
            $new->violation_type = json_encode($request->violation_type);
            $new->flag_instantmission = $request->flag_instantmission;
            $new->mission_id = $request->mission_id;
            $new->file_num = $request->file_num;
            $new->description = $request->description ?? null;
            $new->point_id = $point_id;
            $new->flag = 0;
            // // $new->user_id = auth()->user()->id;
            $new->user_id = 1;
            $new->save();

            if ($request->hasFile('image')) {
                $file = $request->image;
                $path = 'Api/images/violations';
                // foreach ($file as $image) {
                //UploadFilesWithoutReal($path, 'image', $new, $file);
                UploadFilesIM($path, 'image', $new, $file);
                // }

            }
        }


        if ($new) {
            $model = Violation::find($new->id);

            $success['violation'] = $model->only(['id', 'name', 'military_number', 'Civil_number', 'file_num', 'grade', 'image', 'violation_type', 'user_id', 'description', 'flag', 'created_at']);
            return $this->respondSuccess($success, 'Data Saved successfully.');
        } else {
            return $this->respondError('failed to save', ['error' => 'خطأ فى حفظ البيانات'], 404);
        }
    }

    public function get_all_violation(Request $request)
    {
        $messages = [
            'point_id.required' => 'point_id required',
            // 'mission_id.required' => 'mission_id required',
        ];
        $validatedData = Validator::make($request->all(), [
            'point_id' => 'required',
            // 'mission_id' => 'required',
        ], $messages);

        if ($validatedData->fails()) {
            return $this->respondError('Validation Error.', $validatedData->errors(), 400);
        }
        $today = Carbon::today()->toDateString();
        // Retrieve the inspector ID for the authenticated user
        $inspectorId = Inspector::where('user_id', auth()->user()->id)->value('id');
        // shift
        $inspector = InspectorMission::where('inspector_id', $inspectorId)->where('date', $today)->where('day_off', 0)->first();


        // Get the team name where the inspector is listed in `inspector_ids`
        $teamName = GroupTeam::whereRaw('find_in_set(?, inspector_ids)', [$inspectorId])->value('name');
        // Get all the inspector IDs associated with the team(s) the user is part of
        $teamInspectors = GroupTeam::whereRaw('find_in_set(?, inspector_ids)', [$inspectorId])
            ->pluck('inspector_ids')->toArray();

        // Flatten the array and convert the inspector IDs to individual IDs
        $inspectorIds = array_unique(explode(',', implode(',', $teamInspectors)));

        // Find user IDs corresponding to the inspector IDs
        $userIds = Inspector::whereIn('id', $inspectorIds)->pluck('user_id')->toArray();

        $violation = Violation::with('user')->where('point_id', $request->point_id)
            ->where('flag_instantmission', "0")
            ->whereIn('user_id', $userIds)
            ->whereDate('created_at', $today)
            ->where('status', 1)
            ->get();

        // if ($inspector != null) {
        //     $working_time = WorkingTime::find($inspector->working_time_id);
        //     $success['shift'] = $working_time->only(['id', 'name', 'start_time', 'end_time']);
        // } else {
        //     $working_time = null;
        //     $success['shift'] = null;
        // }
        $pointName = Point::find($request->point_id);
        $success['date'] = $today;
        $pointShift = PointDays::where('point_id', $request->point_id)
            ->where('name', Carbon::parse($today)->dayOfWeek)
            ->first();
        if ($request->point_id) {

            $shiftDetails = [
                'start_time' => '00:00',
                'end_time' => '23:59',
                'time' => null
            ];

            // Override with actual shift if available
            if ($pointShift && $pointShift->from && $pointShift->to) {
                $shiftDetails = [
                    'start_time' => $pointShift->from,
                    'end_time' => $pointShift->to,
                    'time' => null // As per requirement
                ];
            }
        } else {
            // No point_id, setting time based on violation's created_at
            $shiftDetails = [
                'start_time' => null,
                'end_time' => null,
                'time' => date("g:i:s A", strtotime($violation->created_at)) // Hour in 24-hour format
            ];
        }
        $success['shift'] = $shiftDetails;
        $success['teamName'] = $teamName;
        $success['pointName'] = $pointName->only(['id', 'name']);

        $success['violation'] = $violation->map(function ($violation) {
            // Retrieve violation types based on the existing ids
            $violationTypes = ViolationTypes::whereIn('id', explode(',', $violation->violation_type))
                ->select('id', 'name')
                ->get();

            // Add the description to the list if it's not null
            if ($violation->description) {
                $violationTypes->push((object) [
                    'id' => -1,
                    'name' => $violation->description
                ]);
            }

            return [
                'id' => $violation->id,
                'can_update' => $violation->user_id == auth()->user()->id ? true : false,
                'InspectorId' => $violation->user_id ?? null,
                'InspectorName' => $violation->user->name ?? null,
                'Inspectorgrade' => $violation->user->grade->name ?? null,
                'name' => $violation->name,
                'military_number' => $violation->military_number ?? null,
                'Civil_number' => $violation->Civil_number ?? null,
                'File_number' => $violation->file_num ?? null,
                'grade' => grade::where('id', $violation->grade)->select('id', 'name')->first() ?? null,
                'image' => $violation->image,
                'violation_type' => $violationTypes,
                'civil_military' => $violation->civil_type ? ViolationTypes::where('id', $violation->civil_type)->value('name') : '',
                'civil_military_id' => $violation->civil_type ? ViolationTypes::where('id', $violation->civil_type)->value('id') : '',
                'created_at' => $violation->parent == 0 ? $violation->created_at : Violation::find($violation->parent)->created_at,
                'created_at_time' => $violation->parent == 0 ? $violation->created_at->format('H:i:s') : Violation::find($violation->parent)->created_at->format('H:i:s'),
                'updated_at' => $violation->parent == 0 ? $violation->updated_at->format('H:i:s') : Violation::find($violation->parent)->updated_at->format('H:i:s'),
                'mission_id' => $violation->mission_id,
                'point_id' => $violation->point_id,
                'point_name' => $violation->point->name,
                'flag_instantmission' => $violation->flag_instantmission,
                'violation_mode' => $violation->flag,
            ];
        });

        return $this->respondSuccess($success, 'Data returned successfully.');
    }

    public function get_voilation_instantMission(Request $request)
    {
        // Validation
        $messages = [
            'mission_id.required' => 'mission_id required',
        ];
        $validatedData = Validator::make($request->all(), [
            'mission_id' => 'required',
        ], $messages);

        if ($validatedData->fails()) {
            return $this->respondError('Validation Error.', $validatedData->errors(), 400);
        }

        // Get today's date
        $today = Carbon::today()->toDateString();

        // Retrieve the inspector ID for the authenticated user
        $inspectorId = Inspector::where('user_id', auth()->user()->id)->value('id');

        // Get shift information
        $inspector = InspectorMission::where('inspector_id', $inspectorId)
            ->where('date', $today)
            ->where('day_off', 0)
            ->first();

        $working_time = $inspector ? WorkingTime::find($inspector->working_time_id) : null;
        // dd($request->mission_id);
        // Get instant mission details
        // $instantMission = DB::table('instantmissions')->with('group','groupTeam')->where('id', 57)->first();
        $instantMission = InstantMission::with('group', 'groupTeam')->find($request->mission_id);

        // instantmission::find(57);
        if (!$instantMission) {
            return $this->respondError('Instant mission not found.', [], 404);
        }

        // Handle GIS link or location
        if (str_contains($instantMission->location, 'gis.paci.gov.kw')) {
            $location = null;
            $kwFinder = $instantMission->location;
        } else {
            $location = $instantMission->location;
            $kwFinder = null;
        }
        // dd( $instantMission );

        // Get violations for this instant mission
        $violations = Violation::where('mission_id', $request->mission_id)
            ->where('flag_instantmission', 1)
            ->where('user_id', auth()->user()->id)
            ->whereDate('created_at', $today)
            ->where('status', 1)
            ->get();

        // Map violations and retrieve violation types
        $success['violation'] = $violations->map(function ($violation) {
            // Retrieve violation types based on the existing ids
            $violationTypes = ViolationTypes::whereIn('id', explode(',', $violation->violation_type))
                ->select('id', 'name')
                ->get();

            // Add description as a type if it exists
            if ($violation->description) {
                $violationTypes->push((object) [
                    'id' => -1,
                    'name' => $violation->description
                ]);
            }
            return [
                'id' => $violation->id,
                'can_update' => $violation->user_id == auth()->user()->id ? true : false,
                'InspectorId' => $violation->user_id ?? null,
                'InspectorName' => $violation->user->name ?? null,
                'Inspectorgrade' => $violation->user->grade->name ?? null,
                'name' => $violation->name,
                'military_number' => $violation->military_number ?? null,
                'Civil_number' => $violation->Civil_number ?? null,
                'File_number' => $violation->file_num ?? null,
                'grade' => grade::where('id', $violation->grade)->select('id', 'name')->first() ?? null,
                'image' => $violation->image,
                'violation_type' => $violationTypes,
                'civil_military' => $violation->civil_type ? ViolationTypes::where('id', $violation->civil_type)->value('name') : '',
                'civil_military_id' => $violation->civil_type ? ViolationTypes::where('id', $violation->civil_type)->value('id') : '',
                'created_at' => $violation->parent == 0 ? $violation->created_at : Violation::find($violation->parent)->created_at,
                'created_at_time' => $violation->parent == 0 ? $violation->created_at->format('H:i:s') : Violation::find($violation->parent)->created_at->format('H:i:s'),
                'updated_at' => $violation->parent == 0 ? $violation->updated_at->format('H:i:s') : Violation::find($violation->parent)->updated_at->format('H:i:s'),
                'mission_id' => $violation->mission_id,
                'point_id' => $violation->point_id,
                'point_name' => null,
                'flag_instantmission' => $violation->flag_instantmission,
                'violation_mode' => $violation->flag,
            ];
        });

        // Add additional instant mission details
        $success['date'] = $today;
        $createdAt = $instantMission->created_at;


        // dd($createdAt);
        $createdAt = Carbon::parse($createdAt);

        $time = $createdAt->format('h:i'); // Only time

        // Determine if it's AM or PM and set the Arabic equivalent
        $period = $createdAt->format('A'); // AM or PM
        $time_arabic = ($period === 'AM') ? 'صباحا' : 'مساءا';
        $success['shift'] =  [
            'date' => $createdAt->format('Y-m-d'),
            'time' => $time ?? null,
        ];
        $success['instantMissions'] = [
            'instant_mission_id' => $instantMission->id,
            'name' => $instantMission->label,  // Assuming description field
            'location' => $location,
            'KWfinder' => $kwFinder,
            'description' => $instantMission->description,
            'group' => $instantMission->group_id ? $instantMission->group->name : 'N/A',  // Include group name
            'team' => $instantMission->group_team_id ? $instantMission->groupTeam->name : 'N/A',  // Include group team name
            'date' => $instantMission->created_at->format('Y-m-d'),
            'time' => $time ?? null,
            'time_name' => $time_arabic ?? null,
            'latitude' => $instantMission->latitude,
            'longitude' => $instantMission->longitude,
        ];

        return $this->respondSuccess($success, 'Data returned successfully.');
    }

    public function getGrade(Request $request, $type)
    {
        $grade = grade::where('type', $type)->get();
        if ($grade->isNotEmpty()) {
            $success['grade'] = $grade->map(function ($item) {
                return $item->only(['id', 'name']);
            });
        } else {
            $success['grade'] = '';
        }
        return $this->respondSuccess($success, 'Get Data successfully.');
    }


    public function updateViolation(Request $request)
    {
        $messages = [
            'type.required' => 'type required',
            'flag_instantmission.required' => 'flag instantmission required',
        ];
        $validatedData = Validator::make($request->all(), [
            'type' => 'required',
            'flag_instantmission' => 'required',
        ], $messages);

        if ($validatedData->fails()) {
            return $this->respondError('Validation Error.', $validatedData->errors(), 400);
        }
        // 1=> this violation of instant mission
        if ($request->flag_instantmission == "1") {
            $point_id = Null;
        } else {
            $point_id = $request->point_id;
        }
        if ($request->civil_military == 1  || $request->civil_military == 3) {
            //عسكري
            $military_number = $request->military_number;
            $Civil_number = $request->Civil_number;
            $file_num = $request->file_num;
        } elseif ($request->civil_military == 2 || $request->civil_military == 4) {
            //ظابط ||مهنيين ||أفراد
            $military_number = null;
            $Civil_number = $request->Civil_number;
            $file_num = $request->file_num;
        }


        if ($request->type == "1") {
            $messages = [
                'name.required' => 'الاسم  مطلوب ولا يمكن تركه فارغاً.',
                'name.string' => 'الاسم  يجب أن يكون نصاً.',
                'image.required' => 'المرفقات مطلوبة',
                'violation_type.required' => 'نوع المخالفة مطلوب',
                'point_id.required_if' => 'رقم النقطة  مطلوب',
                'mission_id.required' => 'رقم المهمة  مطلوب',
            ];
            $validatedData = Validator::make($request->all(), [
                'name' => 'required|string',
                'image' => 'nullable',
                'point_id' => ['required_if:flag_instantmission,0'],
                'mission_id' => 'required',

            ], $messages);

            if ($validatedData->fails()) {
                return $this->respondError('Validation Error.', $validatedData->errors(), 400);
            }
            $today = Carbon::now()->toDateString();
            $index = $this->todayIndex($today);
            // $point=Point::with('pointDays')->where('id',$point_id)->first();
            $point = Point::find($point_id);
            if ($point && $point->work_type == 1) {
                $pointDay = $point->pointDays->where('name', $index)->first();
                $workTime = PointDays::where('point_id', $point_id)->where('name', $index)->first();
                $startTime = $workTime->from;
                $endtTime = $workTime->to;;
                $is_avilable = $this->isTimeAvailable($startTime, $endtTime);
                if (!$is_avilable) {
                    return $this->respondError('failed to save', ['error' => 'انتهت مواعيد عمل النقطه'], 404);
                }
            }
            $idsArray = array_map('intval', explode(',', $request->violation_type));
            $cleanedString = implode(",", $idsArray);

            $parent_violate = Violation::findOrFail($request->id);
            if ($parent_violate->user_id == auth()->user()->id) {
                $time_edit = Setting::where('key', 'timer')->value('value');
                $cutoffTime = $parent_violate->created_at->addMinutes($time_edit);

                if (now() > $cutoffTime) {
                    return $this->respondError('لا يمكنك تحديث هذا السجل بعد الوقت المحدد', [], 403);
                } else {
                    $parent_id = $parent_violate->parent;
                    $images = [];

                    // Handle old images
                    if (!empty($request->old_images)) {
                        // If old_images is a string, explode it into an array
                        if (is_string($request->old_images)) {
                            $oldImages = explode(',', $request->old_images);
                        } else {
                            $oldImages = $request->old_images; // In case it's already an array
                        }

                        if (is_array($oldImages)) {
                            $images = $oldImages;
                        }
                    }

                    if ($parent_id == 0) {

                        $parent_violate->status = 0;
                        $parent_violate->save();
                        $new = new Violation();
                        $new->name = $request->name;
                        $new->military_number = $military_number ?? null;
                        $new->Civil_number = $Civil_number ?? null;
                        $new->file_num = $file_num ?? null;
                        $new->grade = $request->grade;
                        $new->mission_id = $request->mission_id;
                        $new->point_id = $point_id;
                        $new->civil_type = $request->civil_military;
                        $new->violation_type = $cleanedString;
                        $new->flag_instantmission = $request->flag_instantmission;
                        $new->parent = $request->id;
                        $new->status = 1;
                        $new->description = $request->description ?? null;
                        $new->flag = 1;
                        $new->user_id = auth()->user()->id;
                        // Handle new image upload
                        if ($request->hasFile('image')) {
                            $files = $request->file('image');
                            $path = 'Api/images/paperTransactions';
                            $model = Violation::find($new->id);
                            $newImages = $this->UploadFilesIM($path, 'image', $model, $files);

                            // Merge old and new images
                            $images = array_merge($images, $newImages);
                        }

                        // Save images as a comma-separated string
                        $new->image = implode(',', $images);
                        $new->save();
                        if ($new) {
                            $model = Violation::find($new->id);
                            $created = Violation::find($request->id);
                            $success['violation'] = $model->only(['id', 'name', 'military_number', 'Civil_number', 'file_num', 'grade', 'image', 'violation_type', 'user_id', 'description', 'flag']);
                            $success['violation']['created_at'] = $created->created_at;
                            // dd($model->created_at , $created,$request->id);

                            return $this->respondSuccess($success, 'Data Saved successfully.');
                        } else {
                            return $this->respondError('failed to save', ['error' => 'خطأ فى حفظ البيانات'], 404);
                        }
                    } else {
                        if ($request->flag_instantmission == "1") {
                            $viloations = Violation::where('parent', $parent_id)->pluck('id')->toArray();
                        } else {
                            $viloations = Violation::where('point_id', $point->id)->where('parent', $parent_id)->pluck('id')->toArray();
                        }
                        foreach ($viloations as $viloation) {
                            $viloate = Violation::findOrFail($viloation);
                            $viloate->status = 0;
                            $viloate->save();
                        }
                        $new = new Violation();
                        $new->name = $request->name;
                        $new->military_number = $military_number ?? null;
                        $new->Civil_number = $Civil_number ?? null;
                        $new->file_num = $file_num ?? null;
                        $new->grade = $request->grade;
                        $new->mission_id = $request->mission_id;
                        $new->point_id = $point_id;
                        $new->civil_type = $request->civil_military;
                        $new->violation_type = $cleanedString;
                        $new->flag_instantmission = $request->flag_instantmission;
                        $new->parent = $parent_id;
                        $new->status = 1;
                        $new->description = $request->description ?? null;
                        $new->flag = 1;
                        $new->user_id = auth()->user()->id;
                        // $new->save();

                        // if ($request->hasFile('image')) {
                        //     $files = $request->file('image'); // Expecting an array of files
                        //     $path = 'Api/images/violations'; // Directory path
                        //     $model = Violation::find($new->id);

                        //     UploadFilesIM($path, 'image', $model, $files);
                        // }
                        // Handle new image upload
                        if ($request->hasFile('image')) {
                            $files = $request->file('image');
                            $path = 'Api/images/paperTransactions';
                            $model = Violation::find($new->id);
                            $newImages = $this->UploadFilesIM($path, 'image', $model, $files);

                            // Merge old and new images
                            $images = array_merge($images, $newImages);
                        }

                        // Save images as a comma-separated string
                        $new->image = implode(',', $images);
                        $new->save();
                        if ($new) {
                            $model = Violation::find($new->id);
                            $created = Violation::find($parent_id);

                            $success['violation'] = $model->only(['id', 'name', 'military_number', 'Civil_number', 'file_num', 'grade', 'image', 'violation_type', 'user_id', 'description', 'flag']);
                            $success['violation']['created_at'] = $created->created_at;
                            // dd($model->created_at , Violation::find($parent_id)->value('created_at'));

                            return $this->respondSuccess($success, 'Data Saved successfully.');
                        } else {
                            return $this->respondError('failed to save', ['error' => 'خطأ فى حفظ البيانات'], 404);
                        }
                    }
                }
            } else {
                return $this->respondError('عفوا غير مسموح لك بالتعديل على هذه المخالفه', [], 403);
            }
        } else {

            $messages = [
                'image.required' => 'المرفقات مطلوبة',
                'violation_type.required' => 'نوع المخالفة مطلوب',
                'point_id.required_if' => 'رقم النقطة  مطلوب',
                'mission_id.required' => 'رقم المهمة  مطلوب',
            ];
            $validatedData = Validator::make($request->all(), [
                'image' => 'required',
                // 'violation_type' => 'required',
                // 'point_id' => 'required',
                'point_id' => ['required_if:flag_instantmission,0'],
                'mission_id' => 'required',
            ], $messages);

            if ($validatedData->fails()) {
                return $this->respondError('Validation Error.', $validatedData->errors(), 400);
            }

            $parent_violate = Violation::findOrFail($request->id);
            if ($parent_violate->user_id == auth()->user()->id) {

                $time_edit = Setting::where('key', 'timer')->value('value');
                $cutoffTime = $parent_violate->created_at->addMinutes($time_edit);

                if (now() > $cutoffTime) {
                    return $this->respondError('لا يمكنك تحديث هذا السجل بعد الوقت المحدد', [], 403);
                } else {
                    $parent_id = $parent_violate->parent;
                    $images = [];

                    // Handle old images
                    if (!empty($request->old_images)) {
                        // If old_images is a string, explode it into an array
                        if (is_string($request->old_images)) {
                            $oldImages = explode(',', $request->old_images);
                        } else {
                            $oldImages = $request->old_images; // In case it's already an array
                        }

                        if (is_array($oldImages)) {
                            $images = $oldImages;
                        }
                    }
                    if ($parent_id == 0) {
                        $parent_violate->status = 0;
                        $parent_violate->save();
                        $new = new Violation();
                        $new->violation_type = json_encode($request->violation_type);
                        $new->flag_instantmission = $request->flag_instantmission;
                        $new->mission_id = $request->mission_id;
                        $new->file_num = $request->file_num;
                        $new->description = $request->description ?? null;
                        $new->point_id = $point_id;
                        $new->flag = 0;
                        $new->parent = $request->id;
                        $new->status = 1;
                        // // $new->user_id = auth()->user()->id;
                        $new->user_id = 1;
                        // Handle new image upload
                        if ($request->hasFile('image')) {
                            $files = $request->file('image');
                            $path = 'Api/images/paperTransactions';
                            $model = Violation::find($new->id);
                            $newImages = $this->UploadFilesIM($path, 'image', $model, $files);

                            // Merge old and new images
                            $images = array_merge($images, $newImages);
                        }

                        // Save images as a comma-separated string
                        $new->image = implode(',', $images);
                        $new->save();
                        if ($new) {
                            $model = Violation::find($new->id);

                            $success['violation'] = $model->only(['id', 'name', 'military_number', 'Civil_number', 'file_num', 'grade', 'image', 'violation_type', 'user_id', 'description', 'flag']);
                            $success['violation']['created_at'] = Violation::find($request->id)->value('created_at');
                            return $this->respondSuccess($success, 'Data Saved successfully.');
                        } else {
                            return $this->respondError('failed to save', ['error' => 'خطأ فى حفظ البيانات'], 404);
                        }
                    } else {
                        if ($request->flag_instantmission == "1") {
                            $viloations = Violation::where('parent', $parent_id)->pluck('id')->toArray();
                        } else {
                            $viloations = Violation::where('point_id', $request->point_id)->where('parent', $parent_id)->pluck('id')->toArray();
                        }
                        // $viloations = Violation::where('point_id', $request->point_id)->where('parent', $parent_id)->pluck('id')->toArray();
                        foreach ($viloations as $viloation) {
                            $viloate = Violation::findOrFail($viloation);
                            $viloate->status = 0;
                            $viloate->save();
                        }
                        $new = new Violation();

                        $new->violation_type = json_encode($request->violation_type);
                        $new->flag_instantmission = $request->flag_instantmission;
                        $new->mission_id = $request->mission_id;
                        $new->file_num = $request->file_num;
                        $new->description = $request->description ?? null;
                        $new->point_id = $point_id;
                        $new->flag = 0;
                        $new->parent = $request->id;
                        $new->status = 1;
                        // // $new->user_id = auth()->user()->id;
                        $new->user_id = 1;
                        // Handle new image upload
                        if ($request->hasFile('image')) {
                            $files = $request->file('image');
                            $path = 'Api/images/paperTransactions';
                            $model = Violation::find($new->id);
                            $newImages = $this->UploadFilesIM($path, 'image', $model, $files);

                            // Merge old and new images
                            $images = array_merge($images, $newImages);
                        }

                        // Save images as a comma-separated string
                        $new->image = implode(',', $images);
                        $new->save();
                        if ($new) {
                            $model = Violation::find($new->id);

                            $success['violation'] = $model->only(['id', 'name', 'military_number', 'Civil_number', 'file_num', 'grade', 'image', 'violation_type', 'user_id', 'description', 'flag']);
                            $success['violation']['created_at'] = Violation::find($parent_id)->value('created_at');

                            return $this->respondSuccess($success, 'Data Saved successfully.');
                        } else {
                            return $this->respondError('failed to save', ['error' => 'خطأ فى حفظ البيانات'], 404);
                        }
                    }
                }
            } else {
                return $this->respondError('عفوا غير مسموح لك بالتعديل على هذه المخالفه', [], 403);
            }

            // if ($new) {
            //     $model = Violation::find($new->id);

            //     $success['violation'] = $model->only(['id', 'name', 'military_number', 'Civil_number', 'file_num', 'grade', 'image', 'violation_type', 'user_id', 'description', 'flag']);
            //     $success['violation']['created_at'] = Violation::find($request->id)->value('created_at');

            //     return $this->respondSuccess($success, 'Data Saved successfully.');
            // } else {
            //     return $this->respondError('failed to save', ['error' => 'خطأ فى حفظ البيانات'], 404);
            // }
        }
    }
    function UploadFilesIM($path, $inputName, $model, $files)
    {
        $uploadedImages = [];

        foreach ($files as $file) {
            // Generate a unique filename
            $filename = time() . '-' . uniqid() . '.' . $file->getClientOriginalExtension();

            // Move the file to the specified path
            $file->move(public_path($path), $filename);

            // Store the file URL
            $uploadedImages[] = url($path . '/' . $filename);
        }

        return $uploadedImages;
    }
}
