<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class outgoings extends Model
{
    use HasFactory;
    protected $fillable = [ 
        "name",
        "num",
        "note",
        "person_to",
        "active",
        "created_by",
        "updated_by ",
    ];
    public function department()
    {
        return $this->belongsTo(ExternalDepartment::class,'');
    }
    public function files()
    {
        return $this->belongsTo(outgoing_files::class,'');
    }
    public function personTo()
    {
        return $this->belongsTo(User::class, 'person_to');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
    
}
