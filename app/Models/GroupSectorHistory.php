<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GroupSectorHistory extends Model
{
    use HasFactory;

    protected $table = 'group_sector_history';

    protected $primaryKey = 'id';

    public function group()
    {
        return $this->belongsTo(Groups::class);
    }
    public function sector()
    {
        return $this->belongsTo(Sector::class);
    }


  
}
