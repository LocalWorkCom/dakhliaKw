<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PointContent extends Model
{
    use HasFactory;
    protected $table = "point_contents";
    protected $fillable = [
        'mission_id',
        'point_id',
        'inspector_id',
        'parent',
        'flag',
        'mechanisms_num',
        'cams_num',
        'computers_num',
        'cars_num',
        'faxes_num',
        'wires_num',
    ];

    /**
     * Relationship to the InspectorMission model.
     */
    public function mission()
    {
        return $this->belongsTo(InspectorMission::class, 'mission_id');
    }

    /**
     * Relationship to the Point model.
     */
    public function point()
    {
        return $this->belongsTo(Point::class, 'point_id');
    }

    /**
     * Relationship to the Inspector model.
     */
    public function inspector()
    {
        return $this->belongsTo(Inspector::class, 'inspector_id');
    }

    /**
     * Parent relationship (self-referencing).
     */
    public function parentContent()
    {
        return $this->belongsTo(PointContent::class, 'parent');
    }

    /**
     * Child relationship (self-referencing).
     */
    public function childContents()
    {
        return $this->hasMany(PointContent::class, 'parent');
    }
    public function dungeonInfos()
    {
        return $this->hasMany(DungeonInfo::class, 'content_id');
    }
    public function weaponInfos()
    {
        return $this->hasMany(WeaponInfo::class, 'content_id');
    }
}
