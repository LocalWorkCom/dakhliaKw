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
        'description',
        'parent_id'
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
        return $this->hasMany(Iotelegram::class , 'from_departement');
    }
    
    public function outgoings()
    {
        return $this->hasMany(outgoings::class, 'created_department');
    }

    public function parent()
    {
        return $this->belongsTo(departements::class, 'parent_id');
    }

    // Define the relationship to the child departments
    public function children()
    {
        return $this->hasMany(departements::class, 'parent_id');
    }
    public function childrenDepartments()
    {
        return $this->hasMany(departements::class, 'parent_id')->with('children');

    }
}
