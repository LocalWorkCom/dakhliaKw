<?php

namespace App\Models;

use App\Models\WorkingTree;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Groups extends Model
{
    use HasFactory;
    protected $table = 'groups';
    public $timestamps = false;

    protected $fillable = ['name', 'points_inspector']; // Ensure this matches your table's columns



    public function inspector()
    {
        return $this->belongsTo(Inspector::class, 'id');
    }
    public function government()
    {
        return $this->belongsTo(Government::class, 'government_id');
    }
}
