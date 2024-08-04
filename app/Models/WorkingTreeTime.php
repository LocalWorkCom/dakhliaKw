<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkingTreeTime extends Model
{
    use HasFactory;
    protected $table = "working_tree_times";
    
    public function WorkingTree()
    {
        return $this->belongsTo(WorkingTree::class, 'working_tree_id'); 
    }
}
