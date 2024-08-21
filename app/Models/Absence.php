<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absence extends Model
{
    use HasFactory;
    public function point()
    {
        return $this->belongsTo(Point::class, 'point_id');
    }

    // Define the relationship to the InspectorMission model
    public function mission()
    {
        return $this->belongsTo(InspectorMission::class, 'mission_id');
    }

    // Define the relationship to the Inspector model
    public function inspector()
    {
        return $this->belongsTo(Inspector::class, 'inspector_id');
    }
}
