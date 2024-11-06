<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceEmployee extends Model
{
    use HasFactory;

    protected $table = 'attendance_employees';
    public function attendance()
    {
        return $this->belongsTo(Attendance::class, 'attendance_id');
    }

    // Define the relationship with Force
    public function force()
    {
        return $this->belongsTo(ForceName::class, 'force_id');
    }
    public function grade()
    {
        return $this->belongsTo(grade::class, 'grade_id');
    }

    // Define relationship to Type
    public function type()
    {
        return $this->belongsTo(ViolationTypes::class, 'type_id');
    }
}
