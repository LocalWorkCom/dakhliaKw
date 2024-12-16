<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkingTree extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = "working_trees";


    public function workingTreeTimes()
    {
        return $this->hasMany(WorkingTreeTime::class , 'working_tree_id');
    }

    public function GroupTeams()
    {
        return $this->hasMany(GroupTeam::class , 'working_tree_id');
    }
    
}
