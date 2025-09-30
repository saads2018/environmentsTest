<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

use App\Traits\UUID;
use App\Enums\SleepHours;
use App\Enums\ActivityLevel;
use App\Enums\BMI;
use App\Enums\StressLevel;
use App\Enums\WaistSize;
use App\Enums\ConsumptionLevel;


class Questionaire extends Model
{
    use HasFactory, UUID;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
    	'answer_data',
        'lifestyle_data',
        'patient_report',
        'physician_report'
    ];

    protected $casts = [
        'answer_data' => 'array',
        'lifestyle_data' => 'array',
    ];

    protected $appends = [
        'sleep_hours',
        'activity_level',
        'bmi',
        'stress_levels',
        'waist_size',
        'alcohol_consumption',
        'caffeine_consumption',
        'eat_out_level'
    ];

    public function patient() {
        return $this->belongsTo(PatientProfile::class);
    }


    protected function sleepHours(): Attribute {
        return $this->makeAttributeFromDataEnum("sleep_hours", SleepHours::class);
    }

    protected function activityLevel(): Attribute {
         return $this->makeAttributeFromDataEnum("activity_level", ActivityLevel::class);
    }

    protected function bmi(): Attribute {
         return $this->makeAttributeFromDataEnum("bmi", BMI::class);
    }

    protected function stressLevels(): Attribute {
         return $this->makeAttributeFromDataEnum("stress_levels", StressLevel::class);
    }

    protected function waistSize(): Attribute {
         return $this->makeAttributeFromDataEnum("waist_size", WaistSize::class);
    }

    protected function alcoholConsumption(): Attribute {
         return $this->makeAttributeFromDataEnum("alcohol_consumption", ConsumptionLevel::class);
    }

    protected function caffeineConsumption(): Attribute {
         return $this->makeAttributeFromDataEnum("caffeine_consumption", ConsumptionLevel::class);
    }

    protected function eatOutLevel(): Attribute {
         return $this->makeAttributeFromData("eat_out_level");
    }


    private function makeAttributeFromData($attribute = ''): Attribute {
        return Attribute::make(
            get: function ($value, $attributes) use ($attribute) {
                if(!array_key_exists('lifestyle_data', $attributes)) {
                    return null;
                }
                return json_decode($attributes['lifestyle_data'])->$attribute ?? null;

            } ,
            set: fn ($value) => [
                "lifestyle_data->{$attribute}" => $value,
            ],
        );
    }

    private function makeAttributeFromDataEnum($attribute = '', $enumClass = ''): Attribute {
        return Attribute::make(
            get: function ($value, $attributes) use ($attribute, $enumClass) {
                if(!array_key_exists('lifestyle_data', $attributes)) {
                    return null;
                }
                return $enumClass::tryFrom(
                    json_decode($attributes['lifestyle_data'])->$attribute ?? null
                );
            },
            set: fn ($value) => [
                "lifestyle_data->{$attribute}" => $value,
            ],
        );
    }

}
