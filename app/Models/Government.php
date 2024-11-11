<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Government extends Model
{
    use HasFactory, SoftDeletes;
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

    public function groupPointRelations()
    {
        return $this->hasMany(Grouppoint::class);
    }

    public function regionRelations()
    {
        return $this->hasMany(Region::class);
    }

    public function userRelations()
    {
        return $this->hasMany(User::class, 'Provinces', 'id');
    }
}
