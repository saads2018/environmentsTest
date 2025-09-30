<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\UUID; 

class Soap extends Model
{
    use HasFactory, UUID;

    protected $table = "soap";

        /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'clinical_note_id',
        'hpi',
        'family_hx',
        'past_medical_hx',
        'social_hx',
        'general',
        'general_dd',
        'general_comments',
        'skin',
        'skin_dd',
        'skin_comments',
        'heent',
        'heent_dd',
        'heent_comments',
        'neck',
        'neck_dd',
        'neck_comments',
        'cardio',
        'cardio_dd',
        'cardio_comments',
        'respiratory',
        'respiratory_dd',
        'respiratory_comments',
        'gi',
        'gi_dd',
        'gi_comments',
        'urinary',
        'urinary_dd',
        'urinary_comments',
        'periph_vasc',
        'periph_vasc_dd',
        'periph_vasc_comments',
        'msk',
        'msk_dd',
        'msk_comments',
        'neuro',
        'neuro_dd',
        'neuro_comments',
        'endo',
        'endo_dd',
        'endo_comments',
        'psychiatric',
        'psychiatric_dd',
        'psychiatric_comments',
        'general_wnl',
        'general_wnl_dd',
        'general_wnl_comments',
        'heent_wnl',
        'heent_wnl_dd',
        'heent_wnl_comments',
        'skin_wnl',
        'skin_wnl_dd',
        'skin_wnl_comments',
        'neck_wnl',
        'neck_wnl_dd',
        'neck_wnl_comments',
        'cardio_wnl',
        'cardio_wnl_dd',
        'cardio_wnl_comments',
        'lungs_wnl',
        'lungs_wnl_dd',
        'lungs_wnl_comments',
        'abdomen_wnl',
        'abdomen_wnl_dd',
        'abdomen_wnl_comments',
        'msk_wnl',
        'msk_wnl_dd',
        'msk_wnl_comments',
        'neuro_wnl',
        'neuro_wnl_dd',
        'neuro_wnl_comments',
        'extremities_wnl',
        'extremities_wnl_dd',
        'extremities_wnl_comments',
        'billing_icd10',
        'problems',
        'billing_icd9',
        'problem_history',
        'assesments',
        'lab',
        'lab_comms',
        'radiology',
        'radiology_comms',
        'pt_rec',
        'pt_rec_comms',
        'home_health',
        'home_health_comms',
        'referrals',
        'referrals_comms',
        'edu',
        'edu_comms',
        'diet',
        'diet_comms',
        'general_ins',
        'general_ins_comms'
    ];

    public function clinical_note() {
        return $this->belongsTo(ClinicalNote::class);
    }

}
