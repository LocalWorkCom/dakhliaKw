<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Point extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'points';


    protected $fillable = [
        'name',
        'government_id ',
        'region_id ',
        'sector_id ',
        'google_map',
        'lat',
        'long',
        'note',
        'work_type',
        'days_work',
        'created_by '
    ];
    protected $casts = [
        'days_work' => 'array',
    ];
    public function government()
    {
        return $this->belongsTo(Government::class);
    }
    public function pointContents()
    {
        return $this->hasMany(PointContent::class, 'point_id');
    }

    public function pointDays()
    {
        return $this->hasMany(PointDays::class, 'point_id');
    }
    public function paperTransactions()
    {
        return $this->hasMany(paperTransaction::class);
    }
    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    /**
     * Get the sector that owns the point.
     */
    public function sector()
    {
        return $this->belongsTo(Sector::class);
    }
    public function grouppoint()
    {
        return $this->belongsTo(Grouppoint::class);
    }
    public function absences()
    {
        return $this->hasMany(Absence::class, 'point_id');
    }

    public function personalMissions()
    {
        return $this->hasMany(PersonalMission::class);
    }

    public function violations()
    {
        return $this->hasMany(Violation::class);
    }
}
