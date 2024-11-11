<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sector extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'sectors';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'governments_IDs',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'governments_IDs' => 'array', // Automatically cast the attribute to an array
    ];
    public function government()
    {
        return $this->belongsTo(Government::class, 'governments_IDs', 'id');
    }
    public function points()
    {
        return $this->hasMany(Point::class);
    }

    public function groups()
    {
        return $this->hasMany(Groups::class);
    }

    public function groupSectorHistory()
    {
        return $this->hasMany(GroupSectorHistory::class);
    }

    public function grouppoint()
    {
        return $this->hasMany(Grouppoint::class);
    }
}
