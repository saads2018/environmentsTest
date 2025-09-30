<?php

namespace App\Enums;

enum AppointmentVisitType: string {
	case INITIAL = 'initial';
	case WELLNESS = 'wellness';
    case DIETITIAN = 'dietitian';
	case FOLLOWUP = 'followup';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match($this) {
            static::INITIAL => 'Initial NaviWell Visit',
            static::WELLNESS => 'Wellness Coach Visit',
            static::DIETITIAN => 'Dietitian Visit',
            static::FOLLOWUP => 'Follow-Up Visit',
            static::CANCELLED => 'No Show',
        };
    }

}