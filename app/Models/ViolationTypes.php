<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ViolationTypes extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'violation_type';
    public $timestamps = false;

    protected $fillable = ['name','type_id']; 
    // protected $casts = [
    //     'type_id' => 'array',
    // ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function types()
    {
        return $this->belongsTo(departements::class,'type_id');
    }

    public function violations()
    {
        return $this->hasMany(Violation::class, 'violation_type');
    }

    public function absenceEmployees()
    {
        return $this->hasMany(AbsenceEmployee::class, 'type_employee');
    }

    public function absenceViolations()
    {
        return $this->hasMany(AbsenceViolation::class, 'violation_type_id');
    }

    public function attendanceEmployees()
    {
        return $this->hasMany(AttendanceEmployee::class, 'type_id');
    }

}
