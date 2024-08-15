<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PointDays extends Model
{
    use HasFactory;
    protected $table = 'point_days';
    public $timestamps = false;
    protected $fillable = [
        'name','from','to','point_id','created_by'
    ];
    public function point()
    {
        return $this->belongsTo(Point::class, 'point_id');
    }

    // Define the relationship with the User model
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
