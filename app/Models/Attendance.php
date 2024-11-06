<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $table = 'attendances';
    public function employees()
    {
        return $this->hasMany(AttendanceEmployee::class, 'attendance_id');
    }
    // public function inspector()
    // {
    //     return $this->hasMany(Inspector::class, 'inspector_id');
    // }
}
