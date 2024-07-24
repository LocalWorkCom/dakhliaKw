<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExternalDepartment extends Model
{
    use HasFactory;
    protected $table = 'external_departements';
    public function outgoings()
    {
        return $this->hasMany(outgoings::class);

    }
}
