<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ForceName extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'force_names';
    public function attendanceEmployees()
    {
        return $this->hasMany(AttendanceEmployee::class, 'force_id');
    }
}
