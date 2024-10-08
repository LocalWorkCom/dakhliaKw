<?php

namespace App\Models;

use App\Models\WorkingTree;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Groups extends Model
{
    use HasFactory;
    protected $table = 'groups';
    public $timestamps = false;

    protected $fillable = ['name', 'points_inspector', 'created_departement']; // Ensure this matches your table's columns



    public function inspector()
    {
        return $this->belongsTo(Inspector::class, 'id');
    }
    public function sector()
    {
        return $this->belongsTo(Sector::class, 'sector_id');
    }
    public function instant_Mission()
    {

        return $this->hasMany(instantmission::class);
    }

    public function groupPoints() {
        return $this->hasMany(Grouppoint::class);
    }

    public function groupTeamsRelation() {
        return $this->hasMany(GroupTeam::class,'group_id');
    }

    public function groupTeams() {
        return $this->hasMany(GroupTeam::class);
    }
}
