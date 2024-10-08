<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Government extends Model
{
    use HasFactory;
    protected $table = 'governments';
    public $timestamps = false;

    protected $fillable = [
        'name'
    ];
    public function region()
    {
        return $this->hasMany(outgoings::class);

    }
    public function points()
    {
        return $this->hasMany(Point::class);
    }
    public function groupPoints()
    {
        return $this->belongsTo(Grouppoint::class);
    }
}
