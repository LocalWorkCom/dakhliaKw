<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    public function inspectors()
    {
        return $this->hasMany(Inspector::class,'group_id');
    }
}
