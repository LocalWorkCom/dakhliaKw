<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AbsenceEmployee extends Model
{
    use HasFactory;
    public function gradeName()
    {
        return $this->belongsTo(grade::class, 'grade'); 
    }

    // Define the relationship to the AbsenceType model
    public function absenceType()
    {
        return $this->belongsTo(AbsenceType::class, 'absence_types_id'); 
    }
    public function absence()
    {
        return $this->belongsTo(Absence::class, 'absences_id');
    }
  
    public function typeEmployee()
    {
        return $this->belongsTo(ViolationTypes::class, 'type_employee');
    }
}
