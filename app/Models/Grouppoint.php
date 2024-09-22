<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grouppoint extends Model
{
    use HasFactory;
    protected $table = 'group_points';

    protected $fillable = [
        'name',
        'points_ids',
        'government_id',
        'sector_id'
    ];

    protected $casts = [
        'points_ids' => 'array', // Automatically cast the attribute to an array
    ];
    public function government()
    {
        return $this->belongsTo(Government::class, 'government_id', 'id');
    }
    public function points()
    {
        return $this->hasMany(Point::class, 'id', 'points_ids');
    }
    public function sector()
    {
        return $this->hasMany(Sector::class, 'id', 'sector_id');
    }
}
