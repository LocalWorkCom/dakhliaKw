<?php

namespace App\Models;
use App\Models\WorkingTime;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Groups extends Model
{
    use HasFactory;
    protected $table = 'groups';
    public $timestamps = false;

    protected $fillable = ['name', 'work_time_id', 'points_inspector']; // Ensure this matches your table's columns


    public function working_time()
    {
        return $this->belongsTo(WorkingTime::class,'work_time_id');
    }
}