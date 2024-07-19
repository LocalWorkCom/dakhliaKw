<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class exportuser extends Model
{
    use HasFactory;
    protected $table = 'export_users';
    public function outgoingPersonTo()
    {
        return $this->hasMany(outgoings::class, 'person_to');
    }
}
