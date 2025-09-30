<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\UUID;

use App\Enums\AppointmentType;
use App\Enums\AppointmentVisitType;

class Appointment extends Model
{
    use HasFactory, UUID;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
    	'patient_id',
        'physician_id',
        'start_time',
        'finish_time',
        'type',
        'visit_type',
        'notes',
    ];

    protected $casts = [
        'type' => AppointmentType::class,
        'visit_type' => AppointmentVisitType::class,
    ];
    
    protected $dates = [
        'start_time',
        'finish_time',
    ];

    public function patient() {
        return $this->belongsTo(PatientProfile::class);
    }

    public function physician() {
        return $this->belongsTo(PhysicianProfile::class);
    }


    public function note() {
        return $this->hasOne(ClinicalNote::class, 'appt_id');
    }

}
