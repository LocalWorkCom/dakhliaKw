<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Point extends Model
{
    use HasFactory;
    protected $table = 'points';
    public $timestamps = false;

    protected $fillable = [
        'name','government_id ','region_id ','sector_id ','google_map','lat','long','note','from','to'
    ];
    public function government()
    {
        return $this->belongsTo(Government::class);
    }

    /**
     * Get the region that owns the point.
     */
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
