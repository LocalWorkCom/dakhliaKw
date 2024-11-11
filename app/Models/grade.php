<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class grade extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'grades';
    public $timestamps = false;

    protected $fillable = [
        'name'
    ];

    public function grades()
    {
        return $this->hasMany(User::class , 'id');
    }

    public function users()
    {
        return $this->hasMany(User::class , 'grade_id');
    }

    public function absenceEmployees()
    {
        return $this->hasMany(AbsenceEmployee::class, 'grade');
    }
    public function attendanceEmployees()
    {
        return $this->hasMany(AttendanceEmployee::class, 'grade_id');
    }

    public function violations()
    {
        return $this->hasMany(Violation::class, 'grade');
    }
}
