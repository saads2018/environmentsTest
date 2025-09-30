<?php

namespace App\Enums;

enum EducationCode: string {
	case GLUCOSE = 'glucose';
	case CARDIOVASCULAR = 'cardio';
    case HORMONES = 'hormones';
	case GI = 'gi';
    case MENTALHEALTH = 'mental';
    case WEIGHTLOSS = 'weight';
    case FATIGUE = 'fatigue';
    case DIET = 'diet';

    //Used for specific codes
    case ai = 'ai';
    case hpb = 'hpb';
    case hp = 'hc';
    case diabetes = 'diabetes';
    case chronicpain = 'chronicpain';
    case obesity = 'obesity';
    case rwg = 'rwg';

    //Publicly available codes for diet/quizzes etc
    public static function toReadableArray(): array {
		return [
			'glucose' => 'Glucose',
			'cardio' => 'Cardiovascular',
			'hormones' => 'Hormones',
            'gi' => 'GI',
            'mental' => 'Mental health',
            'weight' => 'Weight loss',
            'fatigue' => 'Fatigue/Malaise',
            'diet' => 'Diet/Exercise',
		];
	}

    public function additionalDiagnosis(): array {
        return match($this) {
            EducationCode::GLUCOSE =>           array('NutrEval', 'CoreAge'),
            EducationCode::CARDIOVASCULAR =>    array('NutrEval', 'CoreAge'),
            EducationCode::HORMONES =>          array('NutrEval', 'CoreAge', 'Stress Profile'),
            EducationCode::GI =>                array('Microbiome', 'Food sensitivity', 'NutrEval', 'CoreAge'),
            EducationCode::MENTALHEALTH =>      array('CoreAge', 'NutrEval', 'Stress Profile'),
            EducationCode::WEIGHTLOSS =>        array('Food sensitivity', 'CoreAge'),
            EducationCode::FATIGUE =>           array('Food Sensitivity', 'NutrEval', 'CoreAge', 'Stress Profile'),
            EducationCode::DIET =>              array('CoreAge'),
        };
    } 

    public function codes(): array {
        return match($this) {
            EducationCode::GLUCOSE =>           array("E11.65", "R73.9", "E88.81", "R63.5", "E66.9", "Z71.3", "R53.81"),
            EducationCode::CARDIOVASCULAR =>    array("I10", "E78.5", "E66.9", "R53.81", "E88.81", "Z71.3"),
            EducationCode::HORMONES =>          array("E66.9", "R53.81", "R63.5", "F33.9", "E03.9", "Z71.3"),
            EducationCode::GI =>                array("R53.81", "R63.5", "E66.9", "E78.5", "Z71.3", "Z71.82"),
            EducationCode::MENTALHEALTH =>      array("F33.9", "Z71.3", "R53.81", "R63.5"),
            EducationCode::WEIGHTLOSS =>        array("E78.5", "E66.9", "R53.81", "R63.5", "Z71.82", "Z71.3", "M54.5"),
            EducationCode::FATIGUE =>           array("R53.81", "Z71.82", "Z71.3", "E03.9"),
            EducationCode::DIET =>              array("R53.81", "Z71.82", "Z71.3"),


            //Codes from specific conditions
            // High blood pressure (I10), - hbp
            // High Cholesterol (E78.2), - hc
            // Diabetes (E11.9, E88.81), - diabetes
            // Chronic Pain (G89.4), - chronicpain
            // Obesity (E66.9), - obesity
            // Recent weight gain (R63.5) - rwg
            EducationCode::hpb =>               array("I10"),
            EducationCode::hp =>                array("E78.2"),
            EducationCode::diabetes =>          array("E11.9", "E88.81"),
            EducationCode::chronicpain =>       array("G89.4"),
            EducationCode::obesity =>           array("E66.9"),
            EducationCode::rwg =>               array("R63.5"),
        };
    }

    public static function specificConditions() {
        return array(EducationCode::hpb, EducationCode::hp, EducationCode::diabetes, EducationCode::chronicpain, EducationCode::obesity, EducationCode::rwg);
    }

}