<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class outgoings extends Model
{
    use HasFactory;
    protected $table = 'outgoings';
    protected $fillable = [ 
        "name",
        "num",
        "note",
        "person_to",
        "active",
        "created_by",
        "updated_by ",
    ];
    public function department_External()
    {
        return $this->belongsTo(ExternalDepartment::class,'department_id');
    }
    public function files()
    {
        return $this->belongsTo(outgoing_files::class,'');
    }
    public function personTo()
    {
        return $this->belongsTo(exportuser::class, 'person_to', 'id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
    public function departments()
    {
        return $this->belongsTo(departements::class);
    }
    
}