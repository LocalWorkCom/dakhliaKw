<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VacationType extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = "vacation_types";
    protected $fillable = [
        'name',
        'flag'
    ];
    protected $hidden = [
        'created_departement',
        'created_by',
        'updated_at',
        'active',
        'updated_by',
        'created_at',
    ];

    public function employeeVacations()
    {
        return $this->hasMany(EmployeeVacation::class , 'vacation_type_id');
    }
}
