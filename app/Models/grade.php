<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class grade extends Model
{
    use HasFactory;
    protected $table = 'grades';
    public $timestamps = false;

    protected $fillable = [
        'name'
    ];

}
