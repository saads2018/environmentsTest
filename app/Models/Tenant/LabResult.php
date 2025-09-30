<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\UUID;

class LabResult extends Model
{
    use HasFactory, UUID;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
    	'file',
        'patient_id',
        'date'
    ];

    protected $casts = [
        'file' => 'array',
    ];

    protected $dates = [
        'date',
    ];

    public function patient() {
        return $this->belongsTo(PatientProfile::class)->with('user');
    }
    
}
