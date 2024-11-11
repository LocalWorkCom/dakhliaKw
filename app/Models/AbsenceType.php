<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AbsenceType extends Model
{
    use HasFactory, SoftDeletes;
    public function absences()
    {
        return $this->hasMany(Absence::class, 'absence_types_id');
    }

    public function absenceEmployees()
    {
        return $this->hasMany(AbsenceEmployee::class, 'absence_types_id');
    }
}
