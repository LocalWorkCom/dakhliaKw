<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AbsenceType extends Model
{
    use HasFactory;
    public function absences()
    {
        return $this->hasMany(Absence::class, 'absence_types_id');
    }
}
