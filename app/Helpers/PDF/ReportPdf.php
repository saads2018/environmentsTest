<?php

namespace App\Helpers\PDF;

use App\Helpers\AWSHelper;

class ReportPdf extends PDFEnhanced
{

    //Base path for assets used in this pdf
    protected string $basePath;
    protected $defLeftMargin = 46;

    //Report colors
    protected $lightBlue = array(92, 144, 241);
    protected $blue = array(2, 88, 188);
    protected $textColor = array(30, 29, 46);
    protected $white = array(255, 255, 255);
    protected $red = array(255, 83, 83);
    protected $yellow = array(255, 180, 67);
    protected $green = array(116, 201, 115);
    protected $grayText = array(70, 70, 70);
    
    //Column logic
    protected $col = 0; // Current column
    protected $y0;      // Ordinate of column start
    protected $totalColCount = 0;

    public function __construct()
    {
        parent::__construct();
        $this->basePath = base_path() . '/storage/app/pdf/';

        //Margins are set for the central part of the document. I.e the cross section of blue lines is 5,0
        $this->SetLeftMargin($this->defLeftMargin);
        $this->SetTopMargin(80);
        $this->SetRightMargin(12);

        //Set bottom margin
        $this->SetAutoPageBreak(true, 30);

    }

    // Page header
    function Header()
    {
        // Background image
        $this->Image($this->basePath . 'pdf_background.png', 0, 0, $this->GetPageWidth());
        $this->generateDate();
        $this->generateTitle();
    }

    // Page footer
    function Footer()
    {
        $this->SetLeftMargin(0);
        $this->SetY(-15);
        $this->SetFont('Helvetica', 'B', 14);
        $this->Cell(131);
        $this->textColor($this->white);
        $this->Cell(0, 10, 'Copyright ' . chr(169) . ' ' . date('Y') .  ' NaviWell', 0, 0, 'C');

        $this->SetLeftMargin($this->defLeftMargin);
    }

    function generateDate()
    {
        //Year and month are centered, independently of month length
        $x = $this->getX();
        $y = $this->getY();
        $this->setXY(22, 73);
        $this->Rotate(90);
        $this->textColor($this->lightBlue);
        $this->SetFontSize(14);
        $this->Cell(30, 8, strtoupper(date("F")), 0, 2, "C");
        $this->SetFontSize(28);
        $this->Cell(30, 8, date("Y"), 0, 0, "C");
        $this->Rotate(0);
        $this->setXY($x, $y);
    }
    function generateTitle()
    {
        //save initial values
        $x = $this->getX();
        $y = $this->getY();
        //set desired pos
        $this->setXY(44, 50);

        $this->textColor($this->textColor);
        $this->SetFontSize(38);
        $this->SetFont('', 'B');
        $this->Cell(56, 12, "HEALTH", 0, 2, "C");
        $this->SetFontSize(37);
        $this->SetFont('', '');
        $this->Cell(56, 12, "REPORT", 0, 2, "C");
        //reset to initial values
        $this->setXY($x, $y);
    }

    function SetCol($col)
    {
        if($this->totalColCount == 0){
            $this->totalColCount = 1;
        }
        $colW = ($this->GetPageWidth() - $this->defLeftMargin - $this->rMargin) / $this->totalColCount;
        if($col == 3 || $col == 0) {
            $col = 0;
            $this->y0 = $this->GetY();
        }
        // Set position at a given column
        $this->col = $col;
        $x = $col*$colW;
        $this->SetLeftMargin($x + $this->defLeftMargin);
        $this->SetX($x + $this->defLeftMargin);
    }


    function savePDF($patient_user_id) {
        $name =  \Illuminate\Support\Str::uuid()->toString() . '.pdf';
        $this->Output('F', $this->basePath . $name);
        $helper = new AWSHelper;
        $helper->uploadPDF($name, $patient_user_id);
        return $name;
    }

}