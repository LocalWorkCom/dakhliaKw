<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkingTime extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'start_time',
        'end_time',
    ];

    public function workingTreeTimes()
    {
        return $this->hasMany(WorkingTreeTime::class , 'working_time_id');
    }

    public function inspectorMissions()
    {
        return $this->hasMany(InspectorMission::class , 'working_time_id');
    }

}
