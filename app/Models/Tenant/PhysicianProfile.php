<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\UUID;


class PhysicianProfile extends Model
{
    use HasFactory, UUID;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'dob',
        'gender',

    ];

    protected $casts = [
        'dob' => 'date:Y/m/d',
    ];

    protected $guarded = [];
  
    public function user() 
    { 
        return $this->morphOne('App\Models\Tenant\User', 'profile');
    }

    public function patients() {
        return $this->belongsToMany('App\Models\Tenant\PatientProfile', 'physician_patient', 'physician_id', 'patient_id');
    }
}
