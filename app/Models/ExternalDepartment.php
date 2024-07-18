<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExternalDepartment extends Model
{
    use HasFactory;
    public function outgoings()
    {
        return $this->hasMany(outgoings::class);

    }
}
