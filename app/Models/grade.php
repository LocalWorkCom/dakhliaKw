<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class grade extends Model
{
    use HasFactory;
    protected $table = 'grades';
    public $timestamps = false;

    protected $fillable = [
        'name'
    ];

    public function grades()
    {
        return $this->hasMany(User::class , 'id');
    }
    public function absenceEmployees()
    {
        return $this->hasMany(AbsenceEmployee::class, 'grade');
    }
    public function attendanceEmployees()
    {
        return $this->hasMany(AttendanceEmployee::class, 'grade_id');
    }
}
