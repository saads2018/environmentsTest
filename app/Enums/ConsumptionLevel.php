<?php

namespace App\Enums;

enum ConsumptionLevel: string {
	case LOW = '1';
	case MEDIUM = '2';
	case HIGH = '3';

		public static function toReadableArray(): array {
		return [
			'1' => '0-1',
			'2' => '2-3',
			'3' => '4 or more'
		];
	}

	public function score(): int
    {
        return match($this) {
            ConsumptionLevel::LOW => 0,
			ConsumptionLevel::MEDIUM => 1,
			ConsumptionLevel::HIGH => 2,
        };
    }
}