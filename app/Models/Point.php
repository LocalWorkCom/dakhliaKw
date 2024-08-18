<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Point extends Model
{
    use HasFactory;
    protected $table = 'points';
   

    protected $fillable = [
        'name','government_id ','region_id ','sector_id ','google_map','lat','long','note','work_type','days_work','created_by '
    ];
    protected $casts = [
        'days_work' => 'array',
    ];
    public function government()
    {
        return $this->belongsTo(Government::class);
    }
    public function pointDays()
    {
        return $this->hasMany(PointDays::class, 'point_id');
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
}
