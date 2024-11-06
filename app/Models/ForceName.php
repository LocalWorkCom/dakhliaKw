<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ForceName extends Model
{
    use HasFactory;

    protected $table = 'force_names';
    public function attendanceEmployees()
    {
        return $this->hasMany(AttendanceEmployee::class, 'force_id');
    }
}
