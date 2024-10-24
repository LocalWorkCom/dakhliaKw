<?php

namespace App\Models;

use App\Events\MissionCreated;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class instantmission extends Model
{
    protected $table= 'instantmissions';
    use HasFactory;

    public function group()
    {
        return $this->belongsTo(Groups::class,'group_id');
    }

    public function groupTeam()
    {
        return $this->belongsTo(GroupTeam::class,'group_team_id');
    }
    public function inspector()
    {
        return $this->belongsTo(Inspector::class,'inspector_id');
    }
    public function violations()
    {
        return $this->hasMany(Violation::class);
    }
    protected static function booted()
{
    static::created(function ($mission) {
        event(new MissionCreated($mission));
    });
}
}
