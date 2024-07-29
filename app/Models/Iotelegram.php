<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Iotelegram extends Model
{
    use HasFactory;

    public function created_by()
    {
        return $this->belongsTo(User::class);
    }
    public function created_department()
    {
        return $this->belongsTo(departements::class);
    }
    public function internal_department()
    {
        return $this->belongsTo(departements::class, 'from_departement');
    }
    public function external_department()
    {
        return $this->belongsTo(ExternalDepartment::class, 'from_departement');
    }
    public function recieved()
    {
        return $this->belongsTo(User::class, 'recieved_by');
    }
    public function representive()
    {
        return $this->belongsTo(Postman::class, 'representive_id');
    }
    public function updated_by()
    {
        return $this->belongsTo(User::class);
    }
    public function ioFiles()
    {
        return $this->hasMany(Io_file::class, 'iotelegram_id');
    }
}
