<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

use App\Traits\UUID;
use Spatie\Permission\Traits\HasRoles;


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, UUID, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'image'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
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

    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = ['profile'];
 
 
    public function profile()
    {
        return $this->morphTo();
    }

    public function getIsPatientAttribute()
    {
      return $this->profile_type == 'App\Models\Tenant\PatientProfile';
    }
    public function getIsPhysicianAttribute()
    {
      return $this->profile_type == 'App\Models\Tenant\PhysicianProfile';
    }


    public function scopePatients($query)
    {
        return $query->where('profile_type', 'App\Models\Tenant\PatientProfile');
    }

    public function scopePhysicians($query)
    {
        return $query->where('profile_type', 'App\Models\Tenant\PhysicianProfile');
    }


}
