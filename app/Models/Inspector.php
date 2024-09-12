<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class Inspector extends Model
{
    use HasFactory , SoftDeletes, Notifiable;

    protected $table = 'inspectors';

    protected $primaryKey = 'id';
    protected $fillable = ['name', 'phone', 'position', 'Id_number', 'group_id' ,'type'];
    public function group()
    {
        return $this->belongsTo(Groups::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function groupTeams()
    {
        return $this->belongsTo(GroupTeam::class,'inspector_ids');
    }
    public function absences()
    {
        return $this->hasMany(Absence::class, 'inspector_id');
    }
    public function routeNotificationForMail()
    {
        // Assuming 'email' is the attribute that stores the inspector's email address
        return $this->email;
    }
}
