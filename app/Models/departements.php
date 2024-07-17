<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class departements extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'manager_director',
        'ass_manager_director',
    ];
    public function outgoings()
    {
        return $this->hasMany(outgoings::class);
    }
}
