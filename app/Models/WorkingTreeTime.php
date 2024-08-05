<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkingTreeTime extends Model
{
    use HasFactory;
    protected $table = "working_tree_times";
    
    public function workingTree()
    {
        return $this->belongsTo(WorkingTree::class, 'working_tree_id'); 
    }
    public function workingTime()
    {
        return $this->belongsTo(WorkingTime::class, 'working_time_id'); 
    }
}
