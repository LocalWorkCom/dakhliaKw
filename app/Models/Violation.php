<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Violation extends Model
{
    use HasFactory;
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function point()
    {
        return $this->belongsTo(Point::class, 'point_id');
    }
    public function violatType()
    {
        return $this->belongsTo(ViolationTypes::class, 'violation_type');
    }
    public function instantMission()
    {
        return $this->belongsTo(InstantMission::class , 'mission_id');
    }
}
