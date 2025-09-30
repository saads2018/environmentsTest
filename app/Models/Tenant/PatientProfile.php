<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\AsEnumCollection;

use Illuminate\Support\Carbon;


use App\Traits\UUID;
use App\Enums\EducationCode;

class PatientProfile extends Model
{
    use HasFactory, UUID;

    protected static $unguarded = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'dob',
        'gender',
        'codes',
        'race',
        'ethnicity',
        'physicians',
        'patient_confirmed',
        'language',
        'religion',
        'notes',
        'contact_info',
        'emergency_contact',
        'insurance_info',
        'meds',
        'dxcode',
        'data',
        'manual_quiz'
    ];

    protected $casts = [
        'data' => 'array',
        'contact_info' => 'array',
        'emergency_contact' => 'array',
        'insurance_info' => 'array',
        'meds' => 'array',
        'dob' => 'date:Y/m/d',
        'patient_confirmed' => 'boolean',
        'codes' => AsEnumCollection::class.':'.EducationCode::class,
    ];

    protected $guarded = [];
    protected $appends = ['questionnaireRequired', 'quizAssignDate'];

    public function getNameAttribute() {
        return $this->user->first_name . " " . $this->user->last_name;
    }

    public function getAgeAttribute() {
        $today = Carbon::now(); 
        return $today->diffInYears($this->dob);
    }

    public function getQuestionnaireRequiredAttribute() {
        return $this->isQuizRequired();
    }

    public function getQuizAssignDateAttribute() {
        return $this->quizAssignDate();
    }

    private function quizAssignDate() {
        $questionaire = $this->questionaires->first();
        if (!$questionaire) {
            return $this->created_at;
        } else {
            $date = Carbon::createFromFormat('Y-m-d H:i:s', $questionaire->created_at);
            return $date->addDays(90);
        }
    }

    private function isQuizRequired() {
        //add a special flag to manually attach questionnaire
        if(isset($this->manual_quiz) && $this->manual_quiz == true) {
            return true;
        }
        $nextQDate = $this->quizAssignDate();
        $now = Carbon::now()->addDay();

        return $nextQDate->lte($now);;
    }
  
    public function user() 
    { 
        return $this->morphOne('App\Models\Tenant\User', 'profile');
    }

    public function familyHistory() {
        return $this->belongsToMany('App\Models\Tenant\FamilyHistory', 'profile_familyhistory', 'profile_id', 'history_id');
    }

    public function conditions() {
        return $this->belongsToMany('App\Models\Tenant\MedicalConditions', 'profile_medconditions', 'profile_id', 'condition_id');
    }

    public function physicians() {
        return $this->belongsToMany('App\Models\Tenant\PhysicianProfile', 'physician_patient', 'patient_id', 'physician_id');
    }

    public function healthData() {
        return $this->hasMany(HealthData::class, 'patient_id');
    }

    public function currentHealthData()
    {
        return $this->hasMany(HealthData::class, 'patient_id')->orderBy('created_at', 'desc')->limit(1);
    }

    public function rawAppointments() {
        return $this->hasMany(Appointment::class, 'patient_id');
    }

    public function appointments() {
        return $this->hasMany(Appointment::class, 'patient_id')
        ->where('start_time', '>=', Carbon::now()->toDateTimeString())
        ->orderBy('created_at', 'asc');
    }

    public function questionaires() {
        return $this->hasMany(Questionaire::class, 'patient_id')->orderBy('created_at', 'desc')->limit(1);
    }

    public function completedQuizzes() {
        return $this->belongsToMany('App\Models\Tenant\Quiz', 'profile_quizzes', 'profile_id', 'quiz_id')->withTimestamps()->withPivot('score', 'answer_data');
    }

    public function completedQuizzes2() {
        $completedQuizzes = array();
        $completedQuizzesData = \DB::table('profile_quizzes')->where('profile_id', $this->id)->orderBy("created_at")->get();
        foreach ($completedQuizzesData as $key => $quizData) {
            $quiz = Quiz::getById($quizData->quiz_id)->first();
            if($quiz){
                $quiz->score = $quizData->score;
                $quiz->completed_at = $quizData->created_at;
                array_push($completedQuizzes, $quiz);
            }
            
        }
        return $completedQuizzes;
    }

    public function labResults() {
        return $this->hasMany('App\Models\Tenant\LabResult', 'patient_id');
    }

    public function clinicalNotes() {
        return $this->hasMany(ClinicalNote::class, 'patient_id');
    }

}