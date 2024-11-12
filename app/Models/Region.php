<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Region extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'regions';
    public $timestamps = false;

    protected $fillable = [
        'name'
    ];
    
    public function government()
    {
        return $this->belongsTo(Government::class, 'government_id ', 'id');
    }
    public function points()
    {
        return $this->hasMany(Point::class);
    }

    public function users()
    {
        return $this->hasMany(User::class, 'region', 'id');
    }
}
