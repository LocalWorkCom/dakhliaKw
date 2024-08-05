<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Inspector extends Model
{
    use HasFactory , SoftDeletes;

    protected $table = 'inspectors';

    protected $primaryKey = 'id';
    protected $fillable = ['name', 'phone', 'position', 'Id_number', 'group_id' ,'type'];

}
