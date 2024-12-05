<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DungeonInfo;
use App\Models\Inspector;
use App\Models\InspectorMission;
use App\Models\Point;
use App\Models\PointContent;
use App\Models\WeaponInfo;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class pointsController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index(Request $request)
    {
        $messages = [
            'point_id.required' => 'يجب اختيار النقطه المضاف لها المهمه',
            'point_id.exists' => 'عفوا هذه النقطه غير متاحه',
            'mission_id.required' => 'يجب ادخال رقم المهمه',
        ];

        $validatedData = Validator::make($request->all(), [
            'point_id' => ['required'],
            'mission_id' => ['required'],
        ], $messages);

        if ($validatedData->fails()) {
            return $this->respondError('Validation Error.', $validatedData->errors(), 400);
        }
        $today = Carbon::today()->format('Y-m-d');
        $inspectorId = Inspector::where('user_id', auth()->user()->id)->value('id');
        $inspector = InspectorMission::where('inspector_id', $inspectorId)
            ->where('date', $today)
            ->where('day_off', 0)
            ->first();
        $contents = PointContent::with(['point', 'inspector'])->where('point_id', $request->point_id)->whereDate('created_at', $today)->where('flag', 1)->get();

        $success['PointContent'] = $contents->map(function ($violation) {
            // Retrieve violation types based on the existing ids
            $dungeon_info = DungeonInfo::where('content_id', $violation->id)
                ->get();
                $inspectorId_content = Inspector::where('id', $violation->inspector_id)->value('user_id');

            $WeaponInfo = WeaponInfo::where('content_id', $violation->id)->get();
            return [
                'id' => $violation->id,
                'can_update' => $inspectorId_content == auth()->user()->id ? true : false,
                'InspectorId' => $violation->inspector_id?? null,
                'InspectorName' => $violation->inspector->name ?? null,
                // 'Inspectorgrade' => $violation->inspector_id->grade->name ?? null,
                'mechanisms_num' => $violation->mechanisms_num,
                'cams_num' => $violation->cams_num ?? null,
                'computers_num' => $violation->computers_num ?? null,
                'cars_num' => $violation->cars_num ?? null,
                'faxes_num' => $violation->faxes_num ?? null,
                'wires_num' => $violation->wires_num,
                'created_at' => $violation->note,
                'created_at' => $violation->parent == 0 ? $violation->created_at : PointContent::find($violation->parent)->created_at,
                'created_at_time' => $violation->parent == 0 ? $violation->created_at->format('H:i:s') : PointContent::find($violation->parent)->created_at->format('H:i:s'),
                'updated_at' => $violation->parent == 0 ? $violation->updated_at->format('H:i:s') : PointContent::find($violation->parent)->updated_at->format('H:i:s'),
                'mission_id' => $violation->mission_id,
                'point_id' => $violation->point_id,
                'point_name' => $violation->point->name,
                'dungeon_info'=>$dungeon_info->map(function ($dungeon) {
                    return[
                        "men_num"=> $dungeon->men_num,
                        "women_num"=> $dungeon->women_num,
                        "total"=> $dungeon->men_num +  $dungeon->women_num,
                        "overtake"=> $dungeon->overtake,
                        "duration"=> $dungeon->duration,
                        "note"=> $dungeon->note,
                    ];
                }),
                'WeaponInfo'=>$WeaponInfo->map(function ($Weapon) {
                    return[
                    "name"=> $Weapon->name,
                    "weapon_num"=> $Weapon->weapon_num,
                    "ammunition_num"=> $Weapon->ammunition_num,
                    ];
                }),
            ];
        });

        return $this->respondSuccess($success, 'Data Saved successfully.');
    }
    public function store(Request $request)
    {
        $messages = [
            'point_id.required' => 'يجب اختيار النقطه المضاف لها المهمه',
            'point_id.exists' => 'عفوا هذه النقطه غير متاحه',
            'mission_id.required' => 'يجب ادخال رقم المهمه',
        ];

        $validatedData = Validator::make($request->all(), [
            'point_id' => ['required'],
            'mission_id' => ['required'],
        ], $messages);

        if ($validatedData->fails()) {
            return $this->respondError('Validation Error.', $validatedData->errors(), 400);
        }
        $today = Carbon::today()->format('Y-m-d');
        $inspectorId = Inspector::where('user_id', auth()->user()->id)->value('id');
        $inspector = InspectorMission::where('inspector_id', $inspectorId)
            ->where('date', $today)
            ->where('day_off', 0)
            ->first();
        if ($request->id) {
            $old = PointContent::find($request->id);
            $parent = $old->parent;
            if ($parent) {
                $all = PointContent::where('id', $request->id)->get();
                foreach ($all as $item) {
                    $item = PointContent::find($item->id);
                    $item->flag = 0;
                    $item->save();
                }
                $new = new PointContent();
                $new->mission_id = $request->mission_id;
                $new->point_id = $request->point_id;
                $new->inspector_id = $inspectorId;
                $new->parent = $parent;
                $new->flag = 1;

                if ($request->has('mechanisms_num')) {
                    $new->mechanisms_num = $request->mechanisms_num;
                }
                if ($request->has('cams_num')) {
                    $new->cams_num = $request->cams_num;
                }
                if ($request->has('computers_num')) {
                    $new->computers_num = $request->computers_num;
                }
                if ($request->has('cars_num')) {
                    $new->cars_num = $request->cars_num;
                }
                if ($request->has('faxes_num')) {
                    $new->faxes_num = $request->faxes_num;
                }
                if ($request->has('wires_num')) {
                    $new->wires_num = $request->wires_num;
                }
                $new->save();
                if ($request->has('dungeon_info')) {
                    foreach ($request->dungeon_info as $item) {
                        $new_dungeon = new DungeonInfo();
                        $new_dungeon->men_num = $item["men_num"];
                        $new_dungeon->women_num = $item["women_num"];
                        $new_dungeon->overtake = $item["overtake"];
                        $new_dungeon->duration = $item["duration"];
                        $new_dungeon->note = $item["note"];
                        $new_dungeon->content_id = $new->id;
                        $new_dungeon->save();
                    }
                }
                if ($request->has('weapon_info')) {
                    foreach ($request->weapon_info as $item) {
                        $new_weapon = new WeaponInfo();
                        $new_weapon->name = $item["name"];
                        $new_weapon->weapon_num = $item["weapon_num"];
                        $new_weapon->ammunition_num = $item["ammunition_num"];
                        $new_weapon->content_id = $new->id;
                        $new_weapon->save();
                    }
                }
            } else {
                $old->flag = 0;
                $old->save();
                $new = new PointContent();
                $new->mission_id = $request->mission_id;
                $new->point_id = $request->point_id;
                $new->inspector_id = $inspectorId;
                $new->parent = $request->id;
                $new->flag = 1;

                if ($request->has('mechanisms_num')) {
                    $new->mechanisms_num = $request->mechanisms_num;
                }
                if ($request->has('cams_num')) {
                    $new->cams_num = $request->cams_num;
                }
                if ($request->has('computers_num')) {
                    $new->computers_num = $request->computers_num;
                }
                if ($request->has('cars_num')) {
                    $new->cars_num = $request->cars_num;
                }
                if ($request->has('faxes_num')) {
                    $new->faxes_num = $request->faxes_num;
                }
                if ($request->has('wires_num')) {
                    $new->wires_num = $request->wires_num;
                }
                $new->save();
                if ($request->has('dungeon_info')) {
                    foreach ($request->dungeon_info as $item) {
                        $new_dungeon = new DungeonInfo();
                        $new_dungeon->men_num = $item["men_num"];
                        $new_dungeon->women_num = $item["women_num"];
                        $new_dungeon->overtake = $item["overtake"];
                        $new_dungeon->duration = $item["duration"];
                        $new_dungeon->note = $item["note"];
                        $new_dungeon->content_id = $new->id;
                        $new_dungeon->save();
                    }
                }
                if ($request->has('weapon_info')) {
                    foreach ($request->weapon_info as $item) {
                        $new_weapon = new WeaponInfo();
                        $new_weapon->name = $item["name"];
                        $new_weapon->weapon_num = $item["weapon_num"];
                        $new_weapon->ammunition_num = $item["ammunition_num"];
                        $new_weapon->content_id = $new->id;
                        $new_weapon->save();
                    }
                }
            }
        } else {
            $new = new PointContent();
            $new->mission_id = $request->mission_id;
            $new->point_id = $request->point_id;
            $new->inspector_id = $inspectorId;
            $new->parent = null;
            $new->flag = 1;

            if ($request->has('mechanisms_num')) {
                $new->mechanisms_num = $request->mechanisms_num;
            }
            if ($request->has('cams_num')) {
                $new->cams_num = $request->cams_num;
            }
            if ($request->has('computers_num')) {
                $new->computers_num = $request->computers_num;
            }
            if ($request->has('cars_num')) {
                $new->cars_num = $request->cars_num;
            }
            if ($request->has('faxes_num')) {
                $new->faxes_num = $request->faxes_num;
            }
            if ($request->has('wires_num')) {
                $new->wires_num = $request->wires_num;
            }
            $new->save();
            if ($request->has('dungeon_info')) {
                foreach ($request->dungeon_info as $item) {
                    $new_dungeon = new DungeonInfo();
                    $new_dungeon->men_num = $item["men_num"];
                    $new_dungeon->women_num = $item["women_num"];
                    $new_dungeon->overtake = $item["overtake"];
                    $new_dungeon->duration = $item["duration"];
                    $new_dungeon->note = $item["note"];
                    $new_dungeon->content_id = $new->id;
                    $new_dungeon->save();
                }
            }
            if ($request->has('weapon_info')) {
                foreach ($request->weapon_info as $item) {
                    $new_weapon = new WeaponInfo();
                    $new_weapon->name = $item["name"];
                    $new_weapon->weapon_num = $item["weapon_num"];
                    $new_weapon->ammunition_num = $item["ammunition_num"];
                    $new_weapon->content_id = $new->id;
                    $new_weapon->save();
                }
            }
        }
        $success['PointContent'] = $new;
        $success['PointContent']['dungeon_info'] = $new_dungeon;
        $success['PointContent']['weapon_info'] = $new_weapon;

        return $this->respondSuccess($success, 'Data Saved successfully.');
    }
}
