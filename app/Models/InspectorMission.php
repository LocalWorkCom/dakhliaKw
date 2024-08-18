<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InspectorMission extends Model
{
    use HasFactory;

    protected $table = 'inspector_mission';

    // Specify the primary key if it's not the default 'id'
    protected $primaryKey = 'id';

    // Disable auto-incrementing if your primary key is not an auto-incrementing integer
    public $incrementing = true;

    // If you have non-incrementing primary keys, you might want to set this to false
    protected $keyType = 'int'; // This is the default for integer primary keys

    // Specify which attributes should be mass assignable
    protected $fillable = [
        'inspector_id',
        'ids_group_point',
        'ids_instant_mission',
        'working_time_id',
        'working_tree_id',
        'vacation_id',
        'group_id',
        'group_team_id',
        'date',
        'day_off',
    ];

    // Cast the attributes to specific types
    protected $casts = [
        'ids_group_point' => 'array',
        'ids_instant_mission' => 'array',
        'personal_mission_ids' => 'array',
        'date' => 'date',
        'day_off' => 'boolean',
    ];

    // Define relationships if needed
    public function inspector()
    {
        return $this->belongsTo(Inspector::class, 'inspector_id');
    }

    public function workingTime()
    {
        return $this->belongsTo(WorkingTime::class, 'working_time_id');
    }

    public function workingTree()
    {
        return $this->belongsTo(WorkingTree::class, 'working_tree_id');
    }

    public function vacation()
    {
        return $this->belongsTo(EmployeeVacation::class, 'vacation_id');
    }

    public function group()
    {
        return $this->belongsTo(Groups::class, 'group_id');
    }

    public function groupPoints()
    {
        return $this->hasMany(Grouppoint::class, 'id', 'ids_group_point');
    }

    public function groupTeam()
    {
        return $this->belongsTo(GroupTeam::class, 'group_team_id');
    }
}
