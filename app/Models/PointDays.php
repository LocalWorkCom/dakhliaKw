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
}
