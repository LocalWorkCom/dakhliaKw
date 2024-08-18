<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersonalMission extends Model
{
    use HasFactory;
    protected $table = 'personal_missions';
   

    protected $fillable = [
        'date','point_id','inspector_id','group_id','team_id'
    ];
    // A PersonalMission belongs to an Inspector
    public function inspector()
    {
        return $this->belongsTo(Inspector::class);
    }

    // A PersonalMission belongs to a Group
    public function group()
    {
        return $this->belongsTo(Groups::class);
    }

    // A PersonalMission belongs to a Team (from the group_team table)
    public function team()
    {
        return $this->belongsTo(GroupTeam::class, 'team_id');
    }

    // A PersonalMission belongs to a Point (from the grouppoint table)
    public function point()
    {
        return $this->belongsTo(GroupPoint::class, 'point_id');
    }
}
