<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
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
        // 1,2,3,4,5
        $permission_ids = explode(',', $userPermission->permission_ids);
        // dd($permission_ids);
        // Fetch all permissions that the user has access to based on their role
        $allPermissions = Permission::whereIn('id', $permission_ids)->where('name',$permission)->get();
        // dd(count($allPermissions));
        if(count($allPermissions) > 0)
        {
            return true;
        }
        else{
            return false;
        }

        
    }
}
