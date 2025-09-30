<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\UUID; 

class ClinicalNote extends Model
{
    use HasFactory, UUID;

    protected $table = "clinical_notes";

        /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'patient_id',
        'appt_id',
        'health_data_id',
        'time_in',
        'time_out',
        'counselling',
        'discussed',
        'homework',
        'next_appt',
        'next_followup_physical',
        'next_followup_labs',

        'include',
        'age',
        'height',
        'weight',
        'bmi',
        'ibw',
        'bmr',
        'food_allergies',
        'med_allergies',
        'nutrition_rel_labs',
        'nutrition_rel_meds',
        'nutrition_rel_diag',
        'diet_order',
        'texture',
        'complications',
        'est_cal_per_day',
        'est_protein_per_day',
        'est_carbs_per_day',
        'est_fat_per_day',
        'est_fluid_per_day',
        'intake',
        'activity',
        'interventions',
        'plan',
        'notes',
        'icd_code',
        'cpt_code'
    ];


    public function appointment() {
        return $this->belongsTo(Appointment::class, 'appt_id');
    }


    public function healthData() {
        return $this->belongsTo(HealthData::class);
    }

    public function patient() {
        return $this->belongsTo(PatientProfile::class);
    }

    public function soap() {
        return $this->hasOne(Soap::class);
    }


    // public function getNextFollowupAttribute() {

    //     $patient = $this->patient;

    //     $appointments = $patient->appointments;
    //     if(count($appointments) > 0) {
    //         return $appointments->first();
    //     } else {
    //         return null;
    //     }
    // }


}
