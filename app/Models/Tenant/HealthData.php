<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\UUID; 
class HealthData extends Model
{
    use HasFactory, UUID;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'height',
        'weight',
        'bmi',
        'bodyfat',
        'bp',
        'resting_hr',
        'patient_id'
    ];


    public function patient() {
        return $this->hasOne(PatientProfile::class);
    }
}

