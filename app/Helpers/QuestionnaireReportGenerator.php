<?php

namespace App\Helpers;

use App\Models\Tenant\PatientProfile;
use App\Models\Tenant\Questionaire;

use App\Enums\ProgramTimeline;
use App\Enums\WaistSize;
use App\Enums\EducationCode;
use App\Enums\BMI;

use App\Models\Tenant\Medicine;

use App\Helpers\PDF\PatientReportPdf;
use App\Helpers\PDF\ProviderReportPdf;

class QuestionnaireReportGenerator {

    private $allMedicine = array();
    protected PatientProfile $patient;
    private $familyHistory;
    private $conditions;
    private $meds = array();
    private $totalMedsCount = 0;
    private $quiz;
    private $bmi;

    private $clinicName = "";
    private $patientDescription = "Welcome to NaviWell! We're glad you're here. NaviWell exists to re-think healthcare in the age of chronic diseases. It is our vision to use these questionnaires to enhance and build a better view of health and wellness in partnering with our affiliate clinics. By using things like your medical history, family history, social history, and current symptoms, it allows us to assess and build a program fit for your personal journey to better overall health. Working on your body is the quickest way to change your life - it is our pleasure to walk beside you on that journey. Below you will find your estimated initial timeline on our program, suggested frequency of visits, supplement recommendations to aid in progress during this time, and any additional testing or services that would be helpful to enhance your results. Your physician will review this report as well, create your individualized plan, and have our wellness coach and/or nutritionist reach out to you soon.";
    private $supplements = array("Multi", "Omega3", "Probiotic", "Vitamin D3K2", "Magnesium");
    private $suggestions = array("Dietitian", "Advanced Testing", "Supplements");

    private $totalScore = 0;
    private $personalScore = 0;
    private $timeline = ProgramTimeline::FAST;
    private $additionalDiagnosis = [];
    private $codeList = [];
    private $diagnosis = [];

    public function __construct(PatientProfile $patient, Questionaire $questionnaire)
    {
        $this->allMedicine = Medicine::all();
        $this->clinicName = tenant("name");
        $this->patient = $patient;

        $this->familyHistory = $patient->familyHistory;
		$this->conditions = $patient->conditions;

		$meds = is_array($patient->meds) ? array_filter($patient->meds) : null;
        if($meds) {
            foreach ($meds as $value) {
                $medArray = explode(":", $value);
                $key = $medArray[0];
                $val = intval(trim($medArray[1], " "));
                $this->meds[$key] = $val;
                $this->totalMedsCount += $val;
            }
        }

        $this->quiz = $questionnaire;

        $ptBmi = $patient->currentHealthData->first()->bmi;
        $this->bmi = BMI::fromNumber($ptBmi);

        $this->calculateScore();
    }

    private function calculateScore() {

        $this->evaluateFamilyHistoryAndConditionsScore();

        $this->evaluateLifestyleScore();

        $this->evaluateQuestionnaireScore();

        $this->evaluateEdQualification();

        $this->personalScore = 100 - ($this->totalScore / 200 * 100);
        $this->timeline = ProgramTimeline::fromScore($this->personalScore);

    }

    public function debugGetCalculatedData() {
        return ["total" => $this->totalScore, "score" => $this->personalScore, "timeline" => $this->timeline->label(), 'codes' => $this->codeList, 'additionalDiag' => $this->additionalDiagnosis, "diagnosis" => $this->diagnosis];
    }

    public function generateReports() {
        $patientReportRef = $this->generatePatientReport();
        $physicianReportRef = $this->generateProviderReport();
        $this->quiz->patient_report = $patientReportRef;
        $this->quiz->physician_report = $physicianReportRef;
        $this->quiz->save();
        $this->patient->codes = $this->diagnosis;
        $this->patient->manual_quiz = false;
        $this->patient->save();
    }

    
    public function generatePatientReport() {
        $pdf = new PatientReportPdf($this->clinicName, $this->patient->name, $this->personalScore, $this->patientDescription, $this->timeline->description(), $this->supplements, $this->suggestions);
        $pdfName = $pdf->savePDF($this->patient->user->id);
        return $pdfName;
    }

    public function generateProviderReport() {
        $pdf = new ProviderReportPdf($this->clinicName, $this->patient->name, $this->personalScore, $this->timeline->label(), $this->codeList, $this->additionalDiagnosis, $this->supplements, $this->suggestions);
        $pdfName = $pdf->savePDF($this->patient->user->id);
        return $pdfName;
    }


    private function evaluateFamilyHistoryAndConditionsScore() {
        // Add family history - Family Hx- 1 point per family member up to 6
        if(count($this->familyHistory) > 6) {
            $this->totalScore += 6;
        } else {
            $this->totalScore += count($this->familyHistory);
        } 
        //+3

        // Add conditions - Conditions- 2 points per condition up to 10 points
        if(count($this->conditions) > 5) {
            $this->totalScore += 10;
        } else {
            $this->totalScore += (count($this->conditions)*2);
        }
        //+8

        //Add patient medications - Current Meds: 1 point per med up to 5 points
        if($this->totalMedsCount > 5) {
            $this->totalScore += 5;
        } else {
            $this->totalScore += $this->totalMedsCount;
        }
        //+1

        // Add Number of Physicians: 1 point per provider up to 5 points
        if($this->patient->physicians > 5) {
             $this->totalScore += 5;
        } else {
            $this->totalScore += $this->patient->physicians;
        }
    }

    private function evaluateLifestyleScore() {
        //Evaluate lifestyle
        // {
        //     "waist_size":"2",
        //     "eat_out_level":"1",
        //     "activity_level":"2",
        //     "alcohol_consumption":"2",
        //     "sleep_hours":"2",
        //     "stress_levels":"3",
        //     "caffeine_consumption":"2"
        // }
        $this->totalScore += $this->quiz->sleep_hours->score(); // +3
        $this->totalScore += $this->quiz->activity_level->score(); // +3
        $this->totalScore += $this->bmi->score(); 
        $this->totalScore += $this->quiz->stress_levels->score(); // +3
        $this->totalScore += $this->quiz->waist_size->score(); // less than hip - +1
        $this->totalScore += $this->quiz->alcohol_consumption->score(); // +1
        $this->totalScore += $this->quiz->caffeine_consumption->score(); // + 1
        if( $this->quiz->eat_out_level == 0) {
            $this->totalScore += 1;
        }
        //total +9
    }

    private function evaluateQuestionnaireScore(){
        $quizAnswers = $this->quiz->answer_data;
        // {
        //     "cardio":[0,1,1,0,0,1,0,2,2,2,2,3], + 14
        //     "glucose":[2,0,3,1,1,0,3,3,1,0,3,0], + 17
        //     "endo":[0,1,0,2,0,3,1,3,0,1,0,2], +13
        //     "gi":[1,3,0,2,0,3,0,0,1,0,2,0] +12
        // }
        foreach ($quizAnswers as $key => $value) {
            foreach ($value as $scoreData) {
                $this->totalScore += $scoreData; // 54 total
            }
        }
    }

    private function evaluateEdQualification() {
        $quizAnswers = $this->quiz->answer_data;
        //Calculate education qualification
        
        //Glucose: E11.65, R73.9, E88.81, R63.5, E66.9, Z71.3, R53.81
        //Cardiovascular: I10, E78.5, E66.9, R53.81, E88.81, Z71.3
        //Hormones: E66.9, R53.81, R63.5, F33.9, E03.9, Z71.3
        //GI: R53.81, R63.5, E66.9, E78.5, Z71.3, Z71.82
        //Mental Health: F33.9, Z71.3, R53.81, R63.5
        //Weight Loss: E78.5, E66.9, R53.81, R63.5, Z71.82, Z71.3, M54.5, 
        //Fatigue/ Malaise: R53.81, Z71.82, Z71.3, E03.9
        //Diet/exercise: R53.81, Z71.82, Z71.3

        $codeCategories = [];

        $this->evaluateCancerAdvDiag();

        if($this->evaluateGlucoseEducation($quizAnswers)) {
            $codeCategories = array_merge($codeCategories, EducationCode::GLUCOSE->additionalDiagnosis());
            $this->codeList = array_merge($this->codeList, EducationCode::GLUCOSE->codes());
            array_push($this->diagnosis, EducationCode::GLUCOSE);
        }
        if($this->evaluateCardiovascularEducation($quizAnswers)) {
            $codeCategories = array_merge($codeCategories, EducationCode::CARDIOVASCULAR->additionalDiagnosis());
            $this->codeList = array_merge($this->codeList, EducationCode::CARDIOVASCULAR->codes());
            array_push($this->diagnosis, EducationCode::CARDIOVASCULAR);
        }

        if($this->evaluateHormonesEducation($quizAnswers)) {
            $codeCategories = array_merge($codeCategories, EducationCode::HORMONES->additionalDiagnosis());
            $this->codeList = array_merge($this->codeList, EducationCode::HORMONES->codes());
            array_push($this->diagnosis, EducationCode::HORMONES);
        }

        if($this->evaluateGIEducation($quizAnswers)) {
            $codeCategories = array_merge($codeCategories, EducationCode::GI->additionalDiagnosis());
            $this->codeList = array_merge($this->codeList, EducationCode::GI->codes());
            array_push($this->diagnosis, EducationCode::GI);
        }

        if($this->evaluateMentalEducation($quizAnswers)) {
            $codeCategories = array_merge($codeCategories, EducationCode::MENTALHEALTH->additionalDiagnosis());
            $this->codeList = array_merge($this->codeList, EducationCode::MENTALHEALTH->codes());
            array_push($this->diagnosis, EducationCode::MENTALHEALTH);
        }

        if($this->evaluateWeightLossEducation($quizAnswers)) {
            $codeCategories = array_merge($codeCategories, EducationCode::WEIGHTLOSS->additionalDiagnosis());
            $this->codeList = array_merge($this->codeList, EducationCode::WEIGHTLOSS->codes());
            array_push($this->diagnosis, EducationCode::WEIGHTLOSS);
        }

        if($this->evaluateFatigueEducation($quizAnswers)) {
            $codeCategories = array_merge($codeCategories, EducationCode::FATIGUE->additionalDiagnosis());
            $this->codeList = array_merge($this->codeList, EducationCode::FATIGUE->codes());
            array_push($this->diagnosis, EducationCode::FATIGUE);
        }

        if($this->evaluateDietEducation($quizAnswers)) {
            $codeCategories = array_merge($codeCategories, EducationCode::DIET->additionalDiagnosis());
            $this->codeList = array_merge($this->codeList, EducationCode::DIET->codes());
            array_push($this->diagnosis, EducationCode::DIET);
        }

        //Add codes from specific conditions
        foreach (EducationCode::specificConditions() as $code) {
            $filtered = $this->filterCollectionByCode($this->conditions, $code);
            $filtered2 = $this->filterCollectionByCode($this->familyHistory, $code);
            if(count($filtered) > 0 ){
                $this->codeList = array_merge($this->codeList, $code->codes());
            }
            if(count($filtered2) > 0 ){
                $this->codeList = array_merge($this->codeList, $code->codes());
            }
        }

        //Remove duplicates from code list and reindex array
        $this->codeList = array_values(array_unique($this->codeList));
        $codeCategories = array_values(array_unique($codeCategories));
        $this->additionalDiagnosis = array_merge($this->additionalDiagnosis, $codeCategories);

    }

    private function evaluateCancerAdvDiag() {
        // A: if female and there is history of breast cancer or prostate cancer, suggest “Mammogram every 2 years”
        // B: if male and there is fam history of breast cancer or prostate cancer, suggest “Prostate Exam every 2 years and PSA annually”
        // C: male or female 40 or older with family history of colon cancer, suggest “Colonoscopy every 5 years”
        $breastCancer = false;
        $prostateCancer = false;
        $colonCancer = false;
        if(!is_null($this->familyHistory->pluck('name'))) {
            $breastCancer = $this->familyHistory->pluck('name')->contains("Breast Cancer");
            $prostateCancer = $this->familyHistory->pluck('name')->contains("Prostate Cancer");
            $colonCancer = $this->familyHistory->pluck('name')->contains("Colorectal (colon) Cancer");
        }

        if ($this->patient->gender == "f" && ($breastCancer || $prostateCancer) ) {
            array_push($this->additionalDiagnosis, "Mammogram every 2 years");
        }
        if ($this->patient->gender == "m" && ($breastCancer || $prostateCancer) ) {
            array_push($this->additionalDiagnosis, "Prostate Exam every 2 years and PSA annually");
        }
        if ($this->patient->age >= 40 && $colonCancer ) {
            array_push($this->additionalDiagnosis, "Colonoscopy every 5 years");
        }
    }

    private function evaluateGlucoseEducation($quizAnswers) {
        //Glucose: E11.65, R73.9, E88.81, R63.5, E66.9, Z71.3, R53.81
        //Conditions, 2 or more Fam Hx, Current meds, Activity Level, BMI, waist>hip, 1.10, 1.11, 1.12, S2, 3.1, 3.10
        $conditions = $this->filterCollectionByCode($this->conditions, EducationCode::GLUCOSE);
        $familyHistory = $this->familyHistory;
        $medCount = $this->getMedByType(EducationCode::GLUCOSE);
        if(
            count($conditions) > 0 && 
            count($familyHistory) >= 2 &&
            $medCount > 0 &&
            $this->quiz->activity_level->score() > 0 && 
            $this->bmi->score() > 0 &&
            $this->quiz->waist_size == WaistSize::GREATERHIP &&
            $quizAnswers['cardio'][9] > 1 &&
            $quizAnswers['cardio'][10] > 1 &&
            $quizAnswers['cardio'][11] > 1 &&
            //glucose all answers more than 1,
            $this->determineQuestionnaireSectionAnswer($quizAnswers['glucose']) && 
            $quizAnswers['endo'][0] > 1 &&
            $quizAnswers['endo'][9] > 1
        ) {
            return true;
        }
        return false;
    }

    private function evaluateCardiovascularEducation($quizAnswers) {
        //Cardiovascular: I10, E78.5, E66.9, R53.81, E88.81, Z71.3
        //Conditions, 2 or more Fam Hx, Current meds, Activity Level, Sleep, BMI, waist>hip, S1, 2.7, 2.8, 2.9, 2.10, 2.11, 3.4
        $conditions = $this->filterCollectionByCode($this->conditions, EducationCode::CARDIOVASCULAR);
        $familyHistory = $this->familyHistory;
        $medCount = $this->getMedByType(EducationCode::CARDIOVASCULAR);
        if(
            count($conditions) > 0 && 
            count($familyHistory) >= 2 &&
            $medCount > 0 &&
            $this->quiz->activity_level->score() > 0 && 
            $this->quiz->sleep_hours->score() > 0 && 
            $this->bmi->score() > 0 &&
            $this->quiz->waist_size == WaistSize::GREATERHIP &&
            //cardio all answers more than 1,
            $this->determineQuestionnaireSectionAnswer($quizAnswers['cardio']) && 
            $quizAnswers['glucose'][6] > 1 &&
            $quizAnswers['glucose'][7] > 1 &&
            $quizAnswers['glucose'][8] > 1 &&
            $quizAnswers['glucose'][9] > 1 &&
            $quizAnswers['glucose'][10] > 1 &&
            $quizAnswers['endo'][3] > 1
        ) {
            return true;
        }
        return false;
    }

    private function evaluateHormonesEducation($quizAnswers) {
        //Hormones: E66.9, R53.81, R63.5, F33.9, E03.9, Z71.3
        //Conditions, Current meds, Stress Level BMI, 1.6, 1.8, 1.9, 1.10, 2.1, 2.7, 2.8, S3, 4.2, 4.9
        $conditions = $this->filterCollectionByCode($this->conditions, EducationCode::HORMONES);
        $medCount = $this->getMedByType(EducationCode::HORMONES);
        if(
            count($conditions) > 0 && 
            $medCount > 0 &&
            $this->quiz->stress_levels->score() > 0 && 
            $this->bmi->score() > 0 &&
            $quizAnswers['cardio'][5] > 1 &&
            $quizAnswers['cardio'][7] > 1 &&
            $quizAnswers['cardio'][8] > 1 &&
            $quizAnswers['cardio'][9] > 1 &&
            $quizAnswers['glucose'][0] > 1 &&
            $quizAnswers['glucose'][6] > 1 &&
            $quizAnswers['glucose'][7] > 1 &&
            //endocrine all answers more than 1,
            $this->determineQuestionnaireSectionAnswer($quizAnswers['endo']) && 
            $quizAnswers['gi'][1] > 1 &&
            $quizAnswers['gi'][8] > 1
        ) {
            return true;
        }
        return false;
    }

    private function evaluateGIEducation($quizAnswers) {
        //GI: R53.81, R63.5, E66.9, E78.5, Z71.3, Z71.82
        //Conditions, Current meds, AI Fam Hx, 1.7, 1.8, 1.10, 3.1, 3.12, S4
        $conditions = $this->filterCollectionByCode($this->conditions, EducationCode::GI);
        $familyHistory = $this->filterCollectionByCode($this->familyHistory, EducationCode::ai);
        $medCount = $this->getMedByType(EducationCode::GI);
        if(
            count($conditions) > 0 && 
            count($familyHistory) >= 1 &&
            $medCount > 0 &&
            $quizAnswers['cardio'][6] > 1 &&
            $quizAnswers['cardio'][7] > 1 &&
            $quizAnswers['cardio'][9] > 1 &&
            $quizAnswers['endo'][0] > 1 &&
            $quizAnswers['endo'][11] > 1 &&
            //gi all answers more than 1,
            $this->determineQuestionnaireSectionAnswer($quizAnswers['gi'])
        ) {
            return true;
        }
        return false;
    }

    private function evaluateMentalEducation($quizAnswers) {
        //Mental Health: F33.9, Z71.3, R53.81, R63.5
        //Conditions, 2 or more Fam Hx, Current meds, Alc, Activity Level, Stress Level, 1.6, 1.8, 1.10, 2.1, 2.7, 3.1, 3.2, 3.10, 3.11, 4.1, 4.2, 4.8
        $conditions = $this->filterCollectionByCode($this->conditions, EducationCode::MENTALHEALTH);
        $familyHistory = $this->familyHistory;
        $medCount = $this->getMedByType(EducationCode::MENTALHEALTH);
        if(
            count($conditions) > 0 && 
            count($familyHistory) >= 2 &&
            $medCount > 0 &&
            $this->quiz->alcohol_consumption->score() > 0 && 
            $this->quiz->activity_level->score() > 0 && 
            $this->quiz->stress_levels->score() > 0 && 
            $quizAnswers['cardio'][5] > 1 &&
            $quizAnswers['cardio'][7] > 1 &&
            $quizAnswers['cardio'][9] > 1 &&

            $quizAnswers['glucose'][0] > 1 &&
            $quizAnswers['glucose'][6] > 1 &&

            $quizAnswers['endo'][0] > 1 &&
            $quizAnswers['endo'][1] > 1 &&
            $quizAnswers['endo'][9] > 1 &&
            $quizAnswers['endo'][10] > 1 &&
            $quizAnswers['gi'][0] > 1 &&
            $quizAnswers['gi'][1] > 1 &&
            $quizAnswers['gi'][7] > 1
        ) {
            return true;
        }
        return false;
    }


    private function evaluateWeightLossEducation($quizAnswers) {
        //Weight Loss: E78.5, E66.9, R53.81, R63.5, Z71.82, Z71.3, M54.5
        //Conditions, 2 or more Fam Hx, Current meds, Activity Level, Stress Level, waist>hip, interest healthier, eating out, 1.8, 2.1, 2.3, 2.4, 2.7, 2.8, 2.11, 3.10, 
        $conditions = $this->filterCollectionByCode($this->conditions, EducationCode::WEIGHTLOSS);
        $familyHistory = $this->familyHistory;
        $lifestyleData = $this->quiz->lifestyle_data;
        $medCount = $this->getMedByType(EducationCode::WEIGHTLOSS);
        if(
            count($conditions) > 0 && 
            count($familyHistory) >= 2 &&
            $medCount > 0 &&
            $this->quiz->activity_level->score() > 0 && 
            $this->quiz->stress_levels->score() > 0 && 
            $this->quiz->waist_size == WaistSize::GREATERHIP &&
            $lifestyleData['interested_healthier'] == 1 &&
            $this->quiz->eat_out_level == 1 &&
            $quizAnswers['cardio'][7] > 1 &&

            $quizAnswers['glucose'][0] > 1 &&
            $quizAnswers['glucose'][2] > 1 &&
            $quizAnswers['glucose'][3] > 1 &&
            $quizAnswers['glucose'][6] > 1 &&
            $quizAnswers['glucose'][7] > 1 &&
            $quizAnswers['glucose'][10] > 1 &&
            $quizAnswers['endo'][9] > 1
        ) {
            return true;
        }
        return false;
    }

    private function evaluateFatigueEducation($quizAnswers) {
        //Fatigue/ Malaise: R53.81, Z71.82, Z71.3, E03.9
        //Combination of Glucose, Hormones, GI, Mental Health, Conditions, Caffeine, 1.8, 1.10, 2.3, 2.9, 2.11, 3.1, 3.2, 3.5, 3.12, 4.1
        $conditions = $this->filterCollectionByCode($this->conditions, EducationCode::FATIGUE);
        if(
            count($conditions) > 0 && 
            $this->evaluateGlucoseEducation($quizAnswers) &&
            $this->evaluateHormonesEducation($quizAnswers) &&
            $this->evaluateGIEducation($quizAnswers) &&
            $this->evaluateMentalEducation($quizAnswers) &&

            $this->quiz->caffeine_consumption->score() > 0 && 

            $quizAnswers['cardio'][7] > 1 &&
            $quizAnswers['cardio'][9] > 1 &&

            $quizAnswers['glucose'][2] > 1 &&
            $quizAnswers['glucose'][8] > 1 &&
            $quizAnswers['glucose'][10] > 1 &&

            $quizAnswers['endo'][0] > 1 &&
            $quizAnswers['endo'][1] > 1 &&
            $quizAnswers['endo'][4] > 1 &&
            $quizAnswers['endo'][11] > 1 &&

            $quizAnswers['gi'][0] > 1
        ) {
            return true;
        }
        return false;
    }

    private function evaluateDietEducation($quizAnswers) {
        //Diet/exercise: R53.81, Z71.82, Z71.3
        //If interested healthier
        $lifestyleData = $this->quiz->lifestyle_data;
        if(
            $lifestyleData['interested_healthier'] == 1
        ) {
            return true;
        }
        return false;
    }


    //True if any of the answers in whole section is >1
    //otherwise false
    private function determineQuestionnaireSectionAnswer($array): bool {
        foreach ($array as $answer) {
            if($answer < 2 ) {
                return false;
            }
        }
        return true;
    }

    private function filterCollectionByCode($collection, EducationCode $code) {
        $filtered = $collection->filter(function ($item) use($code) {
            if(is_null($item) || is_null($item->codes)) {
                return false;
            }
            return $item->codes->contains($code);
        });
        return $filtered;
    }

    private function getMedByType(EducationCode $code) {
        $filtered = $this->allMedicine->filter(function ($item) use($code) {
            if(is_null($item) || is_null($item->codes)) {
                return false;
            }
            return $item->codes->contains($code);
        })->pluck('id');
        
        $totalMedCount = 0;
        foreach ($this->meds as $key => $value) {
            if(is_null($filtered)) {
                return false;
            }
            if($filtered->contains($key)) {
                $totalMedCount += $value;
            }
        }
        return $totalMedCount;
    }

}
