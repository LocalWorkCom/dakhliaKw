<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupTeam extends Model
{
    use HasFactory;
    protected $table = 'group_teams';

    protected $fillable = [
        'name',
        'group_id',
        'created_departement'
    ];

    public function government()
    {
        return $this->hasMany(Government::class);
    }


    public function instantMission()
    {

        return $this->hasMany(instantmission::class);
    }

    public function group()
    {

        return $this->belongsTo(Groups::class, 'group_id');
    }
    
    public function working_tree()
    {
        return $this->belongsTo(WorkingTree::class, 'working_tree_id');
    }

    public function inspectors()
    {
        return $this->hasMany(Inspector::class);
    }

}
