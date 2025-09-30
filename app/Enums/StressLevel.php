<?php

namespace App\Enums;

enum StressLevel: string {
	case NONE = '1';
	case MILD = '2';
	case MODERATE = '3';
	case HIGH = '4';

	public static function toReadableArray(): array {
		return [
			'1' => 'None',
			'2' => 'Mild',
			'3' => 'Moderate',
			'4' => 'High'
		];
	}

	public function score(): int
    {
        return match($this) {
            StressLevel::NONE => 0,
			StressLevel::MILD => 1,
			StressLevel::MODERATE => 3,
			StressLevel::HIGH => 5,
        };
    }
}