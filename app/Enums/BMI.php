<?php

namespace App\Enums;

enum BMI: string {
	case OVER35 = '1';
	case S30TO35 = '2';
	case S25TO30 = '3';
	case S18TO25 = '4';
	case LESS18 = '5';

	public static function toReadableArray(): array {
		return [
			'1' => '>35',
			'2' => '30-34.9',
			'3' => '25-29.9',
			'4' => '18.5-24.9',
			'5' => '<18.5'
		];
	}

	public static function fromNumber($num): static
    {
        return match(true) {
            $num >= 35 => static::OVER35,
            $num >= 30 => static::S30TO35,
			$num >= 25 => static::S25TO30,
			$num >= 18.5 => static::S18TO25,
            default => static::LESS18,
        };
    }

	public function score(): int
    {
        return match($this) {
            BMI::OVER35 => 5,
			BMI::S30TO35 => 4,
			BMI::S25TO30 => 3,
			BMI::S18TO25 => 0,
			BMI::LESS18 => 2,
        };
    }

}