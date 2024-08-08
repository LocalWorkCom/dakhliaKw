<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupTeam extends Model
{
    use HasFactory;
   
    public function government()
    {
        return $this->hasMany(Government::class);
    }


    public function instantMission()
    {

        return $this->hasMany(instantmission::class);
    }

    public function working_tree()
    {
        return $this->belongsTo(WorkingTree::class, 'working_tree_id');
    }


}
