<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WeaponInfo extends Model
{
    use HasFactory;
    protected $table = "weapon_infos";
    protected $fillable = [
        'name',
        'weapon_num',
        'ammunition_num',
        'content_id',
    ];

    /**
     * Define the relationship with the PointContent model.
     * WeaponInfo belongs to one PointContent.
     */
    public function pointContent()
    {
        return $this->belongsTo(PointContent::class, 'content_id');
    }
}
