<?php

namespace App\Helpers\PDF;

class PatientReportPdf extends ReportPdf
{
    protected $gaugeX = 100;
    protected $gaugeY = 102;
    protected $gaugeR = 16.5;
    protected $gaugeParams = array(
        'style' => array('width' => 2, 'cap' => 'round', 'join' => 'miter', 'dash' => '0', 'color' => ''),
        'red' => array(
            'start' => 208,
            'max' => -98,
            'multiplier' => 2.17
        ),
        'yellow' => array(
            'start' => 103,
            'max' => -83,
            'multiplier' => 2.44
        ),
        'green' => array(
            'start' => 12,
            'max' => -40,
            'multiplier' => 1.9
        )
    );

    protected $clinic;
    protected $patientName;
    protected $score;
    protected $description;
    protected $timeline;
    protected $supplements;
    protected $suggestions;

    public function __construct(string $clinic, string $patientName, int $score, string $description, string $timeline, array $supplements, array $suggestions)
    {
        $this->clinic = $clinic;
        $this->patientName = $patientName;
        $this->score = $score;
        $this->description = $description;
        $this->timeline = $timeline;
        $this->supplements = $supplements;
        $this->suggestions = $suggestions;

        parent::__construct();

        $this->SetFont('Helvetica', 'B', 16);

        $this->AddPage();

        $this->generateScore($this->score);

        $this->generateDescription($this->description);
        
        $this->AddPage();

        $this->generateProgramTimeline($this->timeline);

        $this->generateSupSugg($this->supplements, $this->suggestions);

    }


    //Output description
    function generateDescription($description)
    {
        $this->textColor($this->blue);
        $this->SetFontSize(14);
        $this->SetFont('', 'B');
        $this->titleWDot("Description");
        $this->Ln();
        $this->SetFont('', '');
        $this->textColor($this->textColor);
        $this->MultiCell(0, 6, $description, 0, 'L');

        $this->Ln();
    }

    function generateProgramTimeline($timeline)
    {
        $this->textColor($this->blue);
        $this->SetFontSize(14);
        $this->SetFont('', 'B');
        $this->titleWDot("Program timeline and visit frequency");
        $this->Ln();
        $this->SetFont('', '');
        $this->textColor($this->textColor);
        $this->MultiCell(0, 6, $timeline, 0, 'L');
        $this->Ln();
    }

    function generateSupSugg($supplements, $suggestions)
    {
        $this->textColor($this->blue);
        $this->SetFontSize(14);
        $this->SetFont('', 'B');
        $width = ($this->getPageWidth() - $this->lMargin) / 2;
        $this->titleWDot("Supplements", $width);
        $this->titleWDot("Additional Suggestions", $width);
        $this->Ln(10);

        $this->SetFont('', '');
        $this->textColor($this->textColor);

        $zipped = array_map(null, $supplements, $suggestions);
        foreach ($zipped as $tuple) {
            list($sup, $sugg) = $tuple;
            $supplement = $sup ? chr(149) . ' ' . $sup : '';
            $suggestion = $sugg ? chr(149) . ' ' . $sugg : '';
            $this->Cell($width, 6, $supplement, 0, 0);
            $this->Cell($width, 6, $suggestion, 0, 1);
        }
    }


    //Score generation with gauge
    function generateScore($score)
    {
        $x = $this->getX();
        $y = $this->getY();
        $this->setXY($x, $y);
        $this->Image($this->basePath . 'health_score.jpg', ($this->getPageWidth() - $this->lMargin + 10) / 2, $this->tMargin, 65);
        $this->setXY($x, $y);

        $this->gaugeX = ($this->getPageWidth() + $this->lMargin / 2 + 3) / 2;

        //Prefill white gauge
        $whiteStyle = $this->gaugeParams['style'];
        $whiteStyle['color'] = $this->white;
        $this->Ellipse($this->gaugeX, $this->gaugeY, $this->gaugeR, 0, 208, 0, -(98), null, $whiteStyle);
        $this->Ellipse($this->gaugeX, $this->gaugeY, $this->gaugeR, 0, 103, 0, -(83), null, $whiteStyle);
        $this->Ellipse($this->gaugeX, $this->gaugeY, $this->gaugeR, 0, 12, 0, -(40), null, $whiteStyle);

        //Fill gauge with corresponding scores
        $this->generateRedCircle($score);
        $this->generateYellowCircle($score);
        $this->generateGreenCircle($score);
        $this->outputScoreSummary($score);

        $this->Ln(10);
    }

    function outputScoreSummary($score)
    {
        $scoreColor = $this->red;
        $scoreText = "Poor";
        if ($score > 45) {
            $scoreColor = $this->yellow;
            $scoreText = "Okay";
        }
        if ($score > 79) {
            $scoreColor = $this->green;
            $scoreText = "Great";
        }

        $this->setXY(($this->getPageWidth() + $this->lMargin / 2) / 2 - 11, 96);

        $this->textColor($scoreColor);
        $this->SetFontSize(26);
        $this->SetFont('', 'B');
        $this->Cell(25, 10, $score . "%", 0, 1, 'C');

        $this->textColor($this->blue);
        $this->SetFontSize(12);
        $this->Text(($this->getPageWidth() - $this->lMargin / 2 - 4) / 2, 121, "Your health score is");

        $this->RoundedRect(($this->getPageWidth() + $this->lMargin + 9) / 2, 116, 14, 7, 1, '1111', 'F', null, $scoreColor);
        $this->textColor($this->white);

        $this->setXY(($this->getPageWidth() + $this->lMargin + 9) / 2, 116.3);
        $this->Cell(14, 6.5, $scoreText, 0, 1, 'C');
    }


    function generateRedCircle($score)
    {
        $params = $this->gaugeParams['red'];
        $redStyle = $this->gaugeParams['style'];
        $redStyle['color'] = $this->red;
        //98º is max fill, manual calculation
        $scoreAngle = round($score * $params['multiplier']);
        $angle = min(abs($params['max']), $scoreAngle);
        $this->EllipseWDot($this->gaugeX, $this->gaugeY, $this->gaugeR, 0, $params['start'], 0, -($angle), $params['max'], -($scoreAngle), null, $redStyle);
    }

    function generateYellowCircle($score)
    {
        $params = $this->gaugeParams['yellow'];
        $yellowStyle = $this->gaugeParams['style'];
        $yellowStyle['color'] = $this->yellow;
        //85º is max fill, manual calculation
        if ($score > 45) {
            $score = $score - 45; //max becomes 34
            $scoreAngle = round($score * $params['multiplier']);
            $angle = min(abs($params['max']), $scoreAngle);
            $this->EllipseWDot($this->gaugeX, $this->gaugeY, $this->gaugeR, 0, $params['start'], 0, -($angle), $params['max'], -($scoreAngle), null, $yellowStyle);
        }
    }

    function generateGreenCircle($score)
    {
        $params = $this->gaugeParams['green'];
        $greenStyle = $this->gaugeParams['style'];
        $greenStyle['color'] = $this->green;
        //40º is max fill, manual calculation
        if ($score > 79) {
            $score = $score - 79; //max becomes 21
            $scoreAngle = round($score * $params['multiplier']);
            $angle = min(abs($params['max']), $scoreAngle);
            $this->EllipseWDot($this->gaugeX, $this->gaugeY, $this->gaugeR, 0, $params['start'], 0, -($angle), $params['max'], -($scoreAngle), null, $greenStyle);
        }
    }


    //Dotted title
    function titleWDot($title, $width = 0)
    {
        $y = $this->getY();
        $x = $this->getX();
        $this->Image($this->basePath . 'bullet.png', $x - 2, $y, 10);
        $this->setXY($x, $y);
        if ($width == 0) {
            $width = $this->GetStringWidth($title);
        }

        $this->Cell($width, 10, $title);
    }

    //Page header
    function Header()
    {
        parent::Header();
        $this->generateClinicName();
        $this->generatePatientName();

    }

    function generateClinicName() {
        $this->textColor($this->grayText);
        $this->SetFontSize(20);
        $this->SetFont('', '');
        $this->Text(108, 63, $this->clinic);
    }

    function generatePatientName()
    {
        $this->textColor($this->blue);
        $this->SetFontSize(20);
        $this->SetFont('', 'B');
        $this->Text(108, 72, $this->patientName);
    }

}

?>