<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupTeam extends Model
{
    use HasFactory;
    public function group()
    {

        return $this->belongsTo(Groups::class, 'group_id');
    }
    public function working_tree()
    {
        return $this->belongsTo(WorkingTree::class, 'working_tree_id');
    }
}
