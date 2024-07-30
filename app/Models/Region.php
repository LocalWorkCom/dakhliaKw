<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    use HasFactory;
    protected $table = 'regions';
    public $timestamps = false;

    protected $fillable = [
        'name'
    ];
    
    public function government()
    {
        return $this->belongsTo(Government::class, 'government_id ', 'id');
    }
}
