<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Postman extends Model
{
    use HasFactory;

    protected $table = 'postmans';
    protected $fillable = [
        'name',
        'national_id',
        'department_id',
        'phone1',
        'phone2'
    ];

}
