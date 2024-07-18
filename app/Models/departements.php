<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class departements extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'manger',
        'manger_assistance',
        'description'
    ];

   

    // Relationships
    public function manager()
    {
        return $this->belongsTo(User::class, 'manger');
    }

    public function managerAssistant()
    {
        return $this->belongsTo(User::class, 'manger_assistance');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');

    }

    public function iotelegrams()
    {
        return $this->hasMany(iotelegrams::class , 'from_departement');
    }
    public function outgoings()
    {
        return $this->hasMany(outgoings::class, 'from_departement');
    }
}
