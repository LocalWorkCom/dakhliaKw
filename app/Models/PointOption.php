<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PointOption extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = "point_options";
    protected $fillable = [
        'name',
    ];
    public function option()
    {
        return $this->hasMany(Point::class, 'option');
    }

}
