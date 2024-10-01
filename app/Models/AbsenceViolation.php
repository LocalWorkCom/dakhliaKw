<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AbsenceViolation extends Model
{
    use HasFactory;

    protected $table = 'absence_violation';

    // Define the relationship to the InspectorMission model
    public function absence()
    {
        return $this->belongsTo(Absence::class, 'absence_id');
    }

    // Define the relationship to the Inspector model
    public function violation_type()
    {
        return $this->belongsTo(ViolationTypes::class, 'violation_type_id');
    }
}
