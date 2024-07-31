<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sector extends Model
{
    use HasFactory;
    protected $table = 'sectors';
    public $timestamps = false;

    protected $fillable = [
        'name','government_id','region_id','sector_id','google_map','lat','long','note'
    ];
}
