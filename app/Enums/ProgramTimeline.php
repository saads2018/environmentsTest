<?php

namespace App\Enums;

enum ProgramTimeline: string {
	case FAST = '1';
	case INTERMEDIATE = '2';
	case LONG = '3';

	public function label(): string
    {
        return match($this) {
            ProgramTimeline::FAST => '3 Months',
			ProgramTimeline::INTERMEDIATE => '6 Months',
			ProgramTimeline::LONG => '9 Months'
        };
    }

    public function description(): string {
        return match($this) {
            ProgramTimeline::FAST => '3 Months. Initial Visit and One Follow-Up Visit.',
			ProgramTimeline::INTERMEDIATE => '6 Months. Initial visit, 2 Follow-Up visits each 3 months.',
			ProgramTimeline::LONG => '9 Months. Initial visit, 3 Follow-Up visits each 3 months.'
        };
    }

    public static function fromScore(int $score): static
    {
        return match(true) {
            $score >= 80 => static::FAST,
            $score >= 46 => static::INTERMEDIATE,
            default => static::LONG,
        };
    }

}