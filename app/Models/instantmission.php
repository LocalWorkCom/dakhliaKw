<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
