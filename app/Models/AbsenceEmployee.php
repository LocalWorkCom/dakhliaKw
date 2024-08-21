<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AbsenceEmployee extends Model
{
    use HasFactory;
    public function gradeName()
    {
        return $this->belongsTo(Grade::class, 'grade'); // Assuming 'grade' is the foreign key column
    }

    // Define the relationship to the AbsenceType model
    public function absenceType()
    {
        return $this->belongsTo(AbsenceType::class, 'absence_types_id'); // Assuming 'absence_types_id' is the foreign key column
    }
}
