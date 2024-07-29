<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class exportuser extends Model
{
    use HasFactory;
    protected $table = 'export_users';

    protected $fillable = [ 
        "military_number",
        "filenum",
        "Civil_number",
        "phone",
        "name",
        "created_at",
        "updated_at",
        "active",
    ];
    public function outgoingPersonTo()
    {
        return $this->hasMany(outgoings::class, 'person_to');
    }
}
