<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DungeonInfo extends Model
{
    use HasFactory;
    protected $table = "dungeon_infos";
    protected $fillable = [
        'men_num',
        'women_num',
        'overtake',
        'duration',
        'note',
        'content_id',
    ];

    /**
     * Define the relationship with the PointContent model.
     * DungeonInfo belongs to one PointContent.
     */
    public function pointContent()
    {
        return $this->belongsTo(PointContent::class, 'content_id');
    }
}
