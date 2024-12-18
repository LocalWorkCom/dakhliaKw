<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

//use Laravel\Sanctum\HasApiTokens;
use Laravel\Passport\HasApiTokens;

use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'fcm_token'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    public function routeNotificationForFcm()
    {
        return $this->fcm_token; // Assuming you store the FCM token in the 'fcm_token' column
    }
    public function getIsVerifiedAttribute()
    {
        return $this->code === $this->verfication_code; // Adjust logic as needed
    }

    public function outgoingCreatedBy()
    {
        return $this->hasMany(outgoings::class, 'created_by');
    }

    public function outgoingUpdatedBy()
    {
        return $this->hasMany(outgoings::class, 'updated_by');
    }

    public function createdDepartments()
    {
        return $this->hasMany(departements::class, 'created_by');
    }
    public function hasPermission($permission)
    {
        $userPermission = Rule::find(auth()->user()->rule_id);
        // dd($permission);
        // 1,2,3,4,5
        $permission_ids = explode(',', $userPermission->permission_ids);
        // dd($permission_ids);
        // Fetch all permissions that the user has access to based on their role
        $allPermissions = Permission::whereIn('id', $permission_ids)->where('name', $permission)->get();
        // dd(count($allPermissions));
        if (count($allPermissions) > 0) {
            return true;
        } else {
            return false;
        }
    }
    public function createdViolations()
    {
        return $this->hasMany(ViolationTypes::class, 'created_by');
    }

    // Relationship with Violation for updated violations
    public function updatedViolations()
    {
        return $this->hasMany(ViolationTypes::class, 'updated_by');
    }
    public function department()
    {
        return $this->belongsTo(departements::class, 'department_id'); // Assuming 'department_id' is the foreign key
    }


    public function rule()
    {
        return $this->belongsTo(Rule::class);
    }
    public function grade()
    {
        return $this->belongsTo(grade::class, 'grade_id'); // Assuming 'grade_id' is the foreign key
    }

    // public function inspectors()
    // {
    //     return $this->belongsTo(grade::class, 'id'); // Assuming 'grade_id' is the foreign key
    // }

    public function inspectors()
    {
        return $this->belongsTo(Inspector::class, 'id');
    }
    public function pointDays()
    {
        return $this->hasMany(PointDays::class, 'created_by');
    }
    public function violations()
    {
        return $this->hasMany(Violation::class, 'user_id');
    }
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }
    public function paperTransactions()
    {
        return $this->hasMany(PaperTransaction::class, 'created_by');
    }

    public function nationality()
    {
        return $this->belongsTo(Country::class, 'id'); // Assuming 'department_id' is the foreign key
    }
}
