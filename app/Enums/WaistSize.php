<?php

namespace App\Enums;

enum WaistSize: string {
	case LESSHIP = '1';
	case GREATERHIP = '2';

	public static function toReadableArray(): array {
		return [
			'1' => 'Greater than or equal to hip size',
			'2' => 'Less than hip size',
		];
	}

	public function score(): int
    {
        return match($this) {
            WaistSize::LESSHIP => 0,
			WaistSize::GREATERHIP => 5,
        };
    }
}