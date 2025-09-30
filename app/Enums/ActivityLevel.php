<?php

namespace App\Enums;

enum ActivityLevel: string {
	case LOW = '1';
	case MEDIUM = '2';
	case HIGH = '3';

	public static function toReadableArray(): array {
		return [
			'1' => '0-1',
			'2' => '2-3',
			'3' => '4-6'
		];
	}

	public function score(): int
    {
        return match($this) {
            ActivityLevel::LOW => 5,
			ActivityLevel::MEDIUM => 3,
			ActivityLevel::HIGH => 0,
        };
    }

}