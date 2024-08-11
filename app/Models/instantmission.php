<?php

namespace App\Models;

use App\Events\MissionCreated;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class instantmission extends Model
{
    use HasFactory;

    public function group()
    {
        return $this->belongsTo(Groups::class,'group_id');
    }

    public function groupTeam()
    {
        return $this->belongsTo(GroupTeam::class,'group_team_id');
    }

    protected static function booted()
{
    static::created(function ($mission) {
        event(new MissionCreated($mission));
    });
}
}
