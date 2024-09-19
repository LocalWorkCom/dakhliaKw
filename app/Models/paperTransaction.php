<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class paperTransaction extends Model
{
    use HasFactory;
    protected $fillable = [
        'point_id',
        'mission_id',
        'inspector_id',
        'civil_number',
        'registration_number',
        'created_by',
        'images'
    ];

    public function point()
    {
        return $this->belongsTo(Point::class);
    }

    public function mission()
    {
        return $this->belongsTo(InspectorMission::class);
    }

    public function inspector()
    {
        return $this->belongsTo(Inspector::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
