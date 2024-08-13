<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ViolationTypes extends Model
{
    use HasFactory;
    protected $table = 'violation_type';
    public $timestamps = false;

    protected $fillable = ['name','type_id']; 
    protected $casts = [
        'type_id' => 'array',
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function types()
    {
        return $this->belongsTo(departements::class,'type_id');
    }
}
