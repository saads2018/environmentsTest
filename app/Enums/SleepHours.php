<?php

namespace App\Enums;

enum SleepHours: string {
	case TWOFOUR = '1';
	case FIVESIX = '2';
	case SEVENNINE = '3';

	public static function toReadableArray(): array {
		return [
			'1' => '2-4',
			'2' => '5-6',
			'3' => '7-9'
		];
	}

	public function score(): int
    {
        return match($this) {
            SleepHours::TWOFOUR => 5,
			SleepHours::FIVESIX => 3,
			SleepHours::SEVENNINE => 0,
        };
    }

}