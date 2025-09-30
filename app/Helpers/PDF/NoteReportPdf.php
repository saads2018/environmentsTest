<?php

namespace App\Helpers\PDF;

class NoteReportPdf extends PDFEnhanced
{

    protected $black = array(0, 0, 0);

    protected $clinic;
    protected $patientName;
    protected $dob;
    protected $visit;
    protected $sex;
    protected $chart; // ???

    protected $time_in, $time_out;
    protected $discussed, $cpt_code, $icd_code;
    protected $counselling, $homework;

    protected $next_session_date;
    protected $next_followup_physical, $next_followup_labs;

    protected $healthData;

    protected $nutritionData = [];

    public function __construct($clinicName, $patient, $appointment, $note, $healthData)
    {

        parent::__construct();

        $this->clinic = $clinicName;

        $this->patientName = $patient->name;
        $this->dob = $patient->dob->format('m/d/Y');;
        $this->visit = $appointment->start_time->format('m/d/Y');
        $this->sex = strtoupper($patient->gender);
        $this->chart = "PADE000001";

        $this->time_in = $note->time_in;
        $this->time_out = $note->time_out;

        $this->counselling = $note->counseling;
        $this->discussed = strip_tags($note->discussed);
        $this->cpt_code = "99404";
        $codes = [];
        foreach ($patient->codes as $code) {
            $str = implode(", ", $code->codes());
            array_push($codes, $str);
        }
        $this->icd_code = implode(", ", $codes);


        $this->homework = $note->homework;

        $this->next_session_date = $note->next_appt;
        $this->next_followup_physical = $note->next_followup_physical;
        $this->next_followup_labs = $note->next_followup_physical;

        $this->healthData = $healthData;

        if($note->include) {

            if($note->age){
                $this->nutritionData['Age:'] = $note->age;
            }

            if($note->height){
                $this->nutritionData['Height:'] = $note->height;
            }

            if($note->weight){
                $this->nutritionData['Weight:'] = $note->weight;
            }

            if($note->bmi){
                $this->nutritionData['BMI:'] = $note->bmi;
            }

            if($note->ibw) {
                $this->nutritionData['IBW:'] = $note->ibw;
            }

            if($note->bmr) {
                $this->nutritionData['BMR:'] = $note->bmr;
            }
            

            if($note->food_allergies) {
                $this->nutritionData['empty_1'] = '';
                $this->nutritionData['Food allergies/Intolerances:'] = $note->food_allergies;
            }

            if($note->med_allergies) {
                $this->nutritionData['empty_2'] = '';
                $this->nutritionData['Medication Allergies:'] = $note->med_allergies;
            }

            if($note->nutrition_rel_labs) {
                $this->nutritionData['empty_3'] = '';
                $this->nutritionData['Nutrition Relevant Labs:'] = $note->nutrition_rel_labs;
            }

            if($note->nutrition_rel_meds) {
                $this->nutritionData['empty_4'] = '';
                $this->nutritionData['Nutrition Relevant Medications:'] = $note->nutrition_rel_meds;
            }

            if($note->nutrition_rel_diag) {
                $this->nutritionData['empty_5'] = '';
                $this->nutritionData['Nutrition Related Diagnosis:'] = $note->nutrition_rel_diag;
            }

            if($note->diet_order) {
                $this->nutritionData['empty_6'] = '';
                $this->nutritionData['Diet Order:'] = $note->diet_order;
            }

            if($note->texture) {
                $this->nutritionData['empty_7'] = '';
                $this->nutritionData['Texture:'] = $note->texture;
            }

            if($note->complications) {
                $this->nutritionData['empty_8'] = '';
                $this->nutritionData['Complications:'] = $note->complications;
                $this->nutritionData['empty_9'] = '';
            }

            if($note->est_cal_per_day) {
                $this->nutritionData['Estimated Calories Per Day:'] = $note->est_cal_per_day;
            }

            if($note->est_protein_per_day) {
                $this->nutritionData['Estimated Protein Per Day:'] = $note->est_protein_per_day;
            }

            if($note->est_carbs_per_day) {
                $this->nutritionData['Estimated Carbs Per Day:'] = $note->est_carbs_per_day;
            }

            if($note->est_fat_per_day) {
                $this->nutritionData['Estimated Fat Per Day:'] = $note->est_fat_per_day;
            }

            if($note->est_fluid_per_day) {
                $this->nutritionData['Estimated Fluid Needs Per Day:'] = $note->est_fluid_per_day;
            }

            if($note->plan) {
                $this->nutritionData['empty_10'] = '';
                $this->nutritionData['PLAN:'] = $note->plan;
            }

            if($note->notes) {
                $this->nutritionData['Notes:'] = $note->notes;
            }
            

        }


        //Margins are set for the central part of the document. I.e the cross section of blue lines is 5,0
        $this->SetLeftMargin(12);
        $this->SetRightMargin(12);

        //Set bottom margin
        $this->SetAutoPageBreak(true, 30);

        $this->SetFont('Helvetica', 'B', 16);


        $this->AddPage();

        $this->generateContent();

    

    }


    //Output description
    function generateContent() {

        $this->SetLeftMargin(16);
        $this->SetRightMargin(16);

        $this->Ln(12);

        if(count($this->nutritionData) > 0) {

            $this->Write(6, 'Nutrition Assessment:');

            $this->SetLeftMargin(20);

            $this->Ln(8);

            foreach ($this->nutritionData as $key => $value) {
                if(str_contains($key, 'empty')) {
                    $this->buildNutritionValue('', '');
                } else {
                    $this->buildNutritionValue($key, $value);
                }
                
            }

        }

        $this->SetX(16);
        $this->SetFont('Helvetica', 'B', 16);
        $this->Write(6, 'Preventative Encounter:');

        $this->SetLeftMargin(20);

        $this->Ln(8);

        $this->SetFont('', '', 10);
        $this->Write(5, 'Time In: ');
        $this->Write(5, $this->time_in);

        $this->Ln();
        $this->Write(5, 'Time Out: ');
        $this->Write(5, $this->time_out);


        $this->Ln(8);
        $this->Write(5, $this->counselling);

        $this->Ln(8);
        $this->Write(5, 'Discussed the following: ');
        $this->Write(5, $this->discussed);
        $this->Ln();

        $this->Write(5, 'CPT code: ');
        $this->Write(5, $this->cpt_code);

        $this->Ln();
        $this->Write(5, 'ICD-10 code: ');
        $this->Write(5, $this->icd_code);




        $this->SetFont('', 'B', 12);
        $this->Ln(8);
        $this->Write(5, 'Patient Homework and Follow-Up:');
        $this->Ln(8);
        $this->SetFont('', '', 10);

        $this->Write(5, '1. Next encounter date: ');
        $this->Write(5, $this->next_session_date);
        $this->Ln();

        $this->Write(5, '2. The patient has been instructed to begin/continue the following: ');
        $this->Ln();
        $this->Write(5, $this->homework);
        $this->Ln();

        $this->Write(5, '3. Date of next scheduled office visit with provider: ');
        $nextVisit = "{$this->next_followup_labs} - Labs; {$this->next_followup_physical} - Physical.";
        $this->Write(5, $nextVisit);
        $this->Ln();


        if($this->healthData) {
            $this->SetFont('', 'B', 12);
            $this->Ln(8);
            $this->Write(5, 'Inbody Results:');
            $this->Ln(8);
            $this->SetFont('', '', 10);

            $this->Write(5, 'Height: ');
            $this->Write(5, $this->healthData->height);
            $this->Ln();

            $this->Write(5, 'Weight: ');
            $this->Write(5, $this->healthData->weight);
            $this->Ln();

            $this->Write(5, 'BF%: ');
            $this->Write(5, $this->healthData->bodyfat);
            $this->Ln();

            $this->Write(5, 'BP: ');
            $this->Write(5, $this->healthData->bp);
            $this->Ln();

            $this->Write(5, 'BMI: ');
            $this->Write(5, $this->healthData->bmi);
            $this->Ln();

            $this->Write(5, 'Resting HR: ');
            $this->Write(5, $this->healthData->resting_hr);
            $this->Ln();
        }

    }

    //Page header
    function Header()
    {
        parent::Header();
        $this->generateTopBar();

    }

    function buildNutritionValue($title, $value) {
        $this->SetFont('', 'BU', 10);
        $this->Write(5, $title);
        $this->SetFont('', '', 10);
        $this->Write(5, ' '. $value);
        $this->Ln(8);
    }

    function generateTopBar()
    {

        $oldMargin = $this->lMargin;
        $this->SetLeftMargin(12);
        $this->Ln(0);
        $lineHeight = 6;
        $fontSize = 12;
        $titleFontSize = 13;
        $this->textColor($this->black);
        $this->SetFontSize($fontSize);
        $this->SetFont('', 'B');


        $this->SetFont('', 'B', $titleFontSize); $this->Write($lineHeight, 'Patient: ');
        $this->SetFont('', '', $fontSize); $this->Write($lineHeight, $this->patientName);

        $this->SetX($this->GetPageWidth() - $this->GetPageWidth()/1.8);

        $this->SetFont('', 'B', $titleFontSize); $this->Write($lineHeight, 'DOB: ');
        $this->SetFont('', '', $fontSize); $this->Write($lineHeight, $this->dob);

        $this->SetX($this->GetPageWidth() - $this->GetPageWidth()/3.5);

        $this->SetFont('', 'B', $titleFontSize); $this->Write($lineHeight, 'Sex: ');
        $this->SetFont('', '', $fontSize); $this->Write($lineHeight, $this->sex);

        $this->Ln();

        $this->SetFont('', 'B', $titleFontSize); $this->Write($lineHeight, 'Provider: ');
        $this->SetFont('', '', $fontSize); $this->Write($lineHeight, $this->clinic);

        $this->SetX($this->GetPageWidth() - $this->GetPageWidth()/1.8);

        $this->SetFont('', 'B', $titleFontSize); $this->Write($lineHeight, 'Visit: ');
        $this->SetFont('', '', $fontSize); $this->Write($lineHeight, $this->visit);

        $this->SetX($this->GetPageWidth() - $this->GetPageWidth()/3.5);

        $this->SetFont('', 'B', $titleFontSize); $this->Write($lineHeight, 'Chart: ');
        $this->SetFont('', '', $fontSize); $this->Write($lineHeight, $this->chart);


        $this->Ln($lineHeight+1);

        $this->SetLineWidth(0.4);
        $this->Line(12, $this->GetY(), $this->GetPageWidth() - 12, $this->GetY());

        $this->SetLeftMargin($oldMargin);

    }

    function Footer()
    {
        $oldMargin = $this->lMargin;
        $this->SetLeftMargin(12);
        $this->SetY(-15);
        $this->SetFont('Helvetica', 'B', 12);

        
        $this->SetLineWidth(0.4);
        $this->Line(12, $this->GetY(), $this->GetPageWidth() - 12, $this->GetY());

        $this->Cell(0, 10, "[Page {$this->PageNo()}]", 0, 0, 'L');
        $this->SetX(12);

        $this->Cell(0, 10, 'Copyright ' . chr(169) . ' ' . date('Y') .  ' NaviWell', 0, 0, 'C');

        $this->SetLeftMargin($oldMargin);
    }

}

?>