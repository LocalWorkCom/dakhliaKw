<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InspectorGroupHistory extends Model
{
    use HasFactory;

    protected $table = 'inspector_group_histories';

    protected $primaryKey = 'id';

    public function group()
    {
        return $this->belongsTo(Groups::class);
    }
    public function inspector()
    {
        return $this->belongsTo(Inspector::class);
    }
    public function team()
    {
        return $this->belongsTo(GroupTeam::class);
    }

}
