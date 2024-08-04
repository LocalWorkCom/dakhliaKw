<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkingTree extends Model
{
    use HasFactory;
    protected $table = "working_trees";


    public function WorkingTreeTimes()
    {
        return $this->hasMany(WorkingTreeTime::class , 'working_tree_id');
    }
    
}
