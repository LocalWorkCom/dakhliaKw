<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class iotelegrams extends Model
{
    use HasFactory;

    public function created_by()
    {
        return $this->belongsTo(User::class);
    }
    public function recieved_by()
    {
        return $this->belongsTo(User::class);
    }
    public function representive()
    {
        return $this->belongsTo(User::class);
    }
    public function updated_by()
    {
        return $this->belongsTo(User::class);
    }
    public function department()
    {
        return $this->belongsTo(departements::class);
    }
}
