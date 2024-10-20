<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VacationType extends Model
{
    use HasFactory;
    protected $table = "vacation_types";
    protected $fillable = [
        'name',
        'flag'
    ];
    protected $hidden = [
        'created_departement',
        'created_by',
        'updated_at',
        'active',
        'updated_by',
        'created_at',
    ];
}
