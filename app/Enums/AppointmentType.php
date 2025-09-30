<?php

namespace App\Enums;

enum AppointmentType: string {
	case APPOINTMENT = 'appointment';
	case TELEHEALTH = 'telehealth';
	case PERSONAL = 'personal';

}