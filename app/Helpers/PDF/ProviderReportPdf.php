<?php

namespace App\Helpers\PDF;

class ProviderReportPdf extends ReportPdf
{
    protected $cellBg = array(239, 245, 255);

    protected $clinic;
    protected $patientName;
    protected $score;
    protected $timeline;
    protected $diagnosisCodes;
    protected $advancedDiagnosis;
    protected $supplements;
    protected $suggestions;

    public function __construct(string $clinic, string $patientName, int $score, string $timeline, array $diagnosisCodes, array $advancedDiagnosis, array $supplements, array $suggestions)
    {
        $this->clinic = $clinic;
        $this->patientName = $patientName;
        $this->score = $score;
        $this->diagnosisCodes = $diagnosisCodes;
        $this->advancedDiagnosis = $advancedDiagnosis;
        $this->timeline = $timeline;
        $this->supplements = $supplements;
        $this->suggestions = $suggestions;

        parent::__construct();

        $this->SetFont('Helvetica', 'B', 16);

        //Reset bottom margin
        $this->SetAutoPageBreak(true, 40);

        $this->AddPage();

        $this->Ln(2);
        $this->CellWithBackground("Patient name:", $this->patientName);
        $this->CellWithBackground("Clinic:", $this->clinic);
        $this->CellWithBackground("Total score:", $this->score . "%");
        $this->CellWithBackground("Suggested program timeline:", $this->timeline);

        if(count($this->diagnosisCodes)){
            $this->generateDiagCodes($this->diagnosisCodes);
        }

        if(count($this->advancedDiagnosis)){
            $this->generateAdvancedDiag($this->advancedDiagnosis);
        }

        if(count($this->supplements) || count($this->suggestions)){
            $this->generateSupSugg($this->supplements, $this->suggestions);
        }

    }

    function generateDiagCodes($codes)
    {

        // $this->threeColumnTable("Diagnosis Suggested", $codes);
        
        $c = implode(', ', $codes);

        $this->textColor($this->blue);
        $this->SetFontSize(14);
        $this->SetFont('', 'B');

        //Generate empty cells to calculate w/h
        $x = $this->getX();
        $y = $this->getY();

        $this->Cell(0, 12, '');
        $newX = $this->getX();
        $this->Ln();
        $this->y0 = $this->GetY();
        $this->SetFont('', '');
        $this->textColor($this->textColor);

        $this->Cell(5, 6);
        $this->MultiCell(0, 6, $c, 0, 1);

        $this->Ln(2);
        $newY = $this->getY();
        $this->RoundedRect($x, $y, $newX - $x, $newY - $y, 2, '1111', 'F', null, $this->cellBg);
        $this->setXY($x, $y);

        $this->textColor($this->blue);
        $this->SetFontSize(14);
        $this->SetFont('', 'B');
        $this->Cell(0, 12, 'Diagnosis Suggested', 0, 1, 'C', false);

        $this->SetFont('', '');
        $this->textColor($this->textColor);
        $this->Cell(5, 6);
        $this->MultiCell(0, 6, $c, 0, 1);

        $this->Ln(5);
    }

    function generateAdvancedDiag($advDiag)
    {
        $this->threeColumnTable("Advanced Diagnostics Suggested", $advDiag);
    }

    function threeColumnTable($title, $values) {
        $this->totalColCount = 3;
        $this->setCol(0);

        $this->textColor($this->blue);
        $this->SetFontSize(14);
        $this->SetFont('', 'B');

        //Generate empty cells to calculate w/h
        $x = $this->getX();
        $y = $this->getY();

        $this->Cell(0, 12, '');
        $newX = $this->getX();
        $this->Ln();
        $w = ($this->GetPageWidth() - $this->lMargin - 3) / 3;
        $this->y0 = $this->GetY();
        $this->setCol(0);
        $this->SetFont('', '');
        $this->textColor($this->textColor);
        $maxY = 0;
        for ($i = 0; $i < count($values); $i++) {
            if ($this->col % 3 == 0 && $this->col != 0) {
                $this->setCol(0);
            }
            $str = chr(149) . ' ' . $values[$i];
            $strW = $this->GetStringWidth($str);
            $this->Cell(5, 10);
            $this->Cell($w, 6, '', 0, 1);
            if($this->GetY() > $maxY){
                $maxY = $this->getY();
            }
            if($strW < $w) {
                $this->setCol($this->col + 1);
                
            } else {
                $this->setCol(0);
            }
            $this->setY($this->y0);

        }
        $this->setCol(0);
        $this->setY($maxY);

        $this->Ln(2);
        $newY = $this->getY();
        $this->RoundedRect($x, $y, $newX - $x, $newY - $y, 2, '1111', 'F', null, $this->cellBg);
        $this->setXY($x, $y);


        $this->textColor($this->blue);
        $this->SetFontSize(14);
        $this->SetFont('', 'B');
        $this->Cell(0, 12, $title, 0, 1, 'C', false);
        $this->y0 = $this->GetY();
        
        $this->SetFont('', '');
        $this->textColor($this->textColor);
        for ($i = 0; $i < count($values); $i++) {
            if ($this->col % 3 == 0 && $this->col != 0) {
                $this->setCol(0);
            }
            $str = chr(149) . ' ' . $values[$i];
            $strW = $this->GetStringWidth($str);
            $this->Cell(5, 6);
            $this->Cell($w, 6, $str, 0, 1);
            if($strW < $w) {
                $this->setCol($this->col + 1);
                
            } else {
                $this->setCol(0);
            }
            $this->setY($this->y0);
            
            
        }
        $this->setCol(0);
        $this->setY($maxY);
        $this->Ln();
    }

    function generateSupSugg($supplements, $suggestions) {
        $pageN = $this->PageNo();
        //Generate empty cells to calculate w/h
        $x = $this->getX();
        $y = $this->getY();

        $this->textColor($this->blue);
        $this->SetFontSize(14);
        $this->SetFont('', 'B');
        $cellW = ($this->getPageWidth() - $this->lMargin) / 2.5;
        $this->Cell($cellW, 10, '', 0, 0, "C", false);
        $this->Cell(10, 10);
        $this->Cell($cellW, 10, '', 0, 0, "C", false);
        
        $this->Ln(10);

        $zipped = array_map(null, $supplements, $suggestions);
        foreach ($zipped as $tuple) {
            list($sup, $sugg) = $tuple;
            $supplement = $sup ? chr(149) . ' ' . $sup : '';
            $suggestion = $sugg ? chr(149) . ' ' . $sugg : '';
            $this->Cell(5, 6);
            $this->Cell($cellW, 6, '', 0, 0);
            $this->Cell(20, 6);
            $this->Cell($cellW, 6, '', 0, 1);
        }
        $this->Ln(2);

        //Check if we moved to the next page and move all content to new page only.
        if($this->PageNo() != $pageN) {
            $this->setXY($this->lMargin, $this->tMargin+5);
            $this->generateSupSugg($supplements, $suggestions);
            return;
        }
        $newY = $this->getY();
        $this->RoundedRect($x, $y, $cellW, $newY - $y, 2, '1111', 'F', null, $this->cellBg);
        $this->RoundedRect($cellW + $x + 20, $y, $cellW, $newY - $y, 2, '1111', 'F', null, $this->cellBg);
        $this->setXY($x, $y);

        $this->textColor($this->blue);
        $this->SetFontSize(14);
        $this->SetFont('', 'B');
        $this->Cell($cellW, 10, "Supplements", 0, 0, "C");
        $this->Cell(20, 10);
        $this->Cell($cellW, 10, "Additional Suggestions", 0, 0, "C");
        $this->Ln(10);
        $this->SetFont('', '');
        $this->textColor($this->textColor);

        foreach ($zipped as $tuple) {
            list($sup, $sugg) = $tuple;
            $supplement = $sup ? chr(149) . ' ' . $sup : '';
            $suggestion = $sugg ? chr(149) . ' ' . $sugg : '';
            $this->Cell(5, 6);
            $this->Cell($cellW, 6, $supplement, 0, 0);
            $this->Cell(20, 6);
            $this->Cell($cellW, 6, $suggestion, 0, 1);
        }
    }

    //Generates sinle line cell with padded background
    function CellWithBackground($title, $content)
    {
        //Generate empty cells to calculate w/h
        $x = $this->getX();
        $y = $this->getY();
        $this->Cell(0, 12);
        $newX = $this->getX();
        $this->Ln();
        $newY = $this->getY();

        $this->RoundedRect($x, $y, $newX - $x, $newY - $y, 2, '1111', 'F', null, $this->cellBg);

        $this->setXY($x + 5, $y);

        $this->textColor($this->blue);
        $this->SetFontSize(14);
        $this->SetFont('', 'B');
        $w = $this->getStringWidth($title);
        $this->Cell($w + 2, 12, $title, 0, 0, "L", false);

        $this->textColor($this->textColor);
        $this->SetFontSize(14);
        $this->SetFont('', '');
        $this->Cell(0, 12, $content, 0, 1, "L", false);

        $this->Ln(5);
    }

    // Page footer
    function Footer()
    {
        parent::Footer();

        $this->textColor($this->textColor);
        $this->SetY(-32);
        $this->SetFontSize(16);
        $this->SetFont('', '');
        $signature = "Physician signature __________________";
        $w = $this->GetStringWidth($signature) + 10;
        $this->setX(-$w);
        $this->Cell($w, 10, $signature, 0, 0);
    }
}