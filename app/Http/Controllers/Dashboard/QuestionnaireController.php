<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;

use App\Models\Tenant;
use App\Models\Tenant\Questionaire;

use App\Helpers\AWSHelper;
use Illuminate\Http\Request;

class QuestionnaireController extends Controller
{

	    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function list(Request $request)
    {
        $tenants = Tenant::get();
        $clinic = $tenants[0];
        if(isset($request->name)) {
            $clinic = Tenant::where('id', $request->name)->firstOrFail();
        }
        $quizzes = $this->getClinicQuizzes($clinic); 
        return view('dashboard.questionnaire.list')->with(compact('tenants', 'quizzes', 'clinic'));
    }

    public function view($clinicId, $quizId) {
        $clinic = Tenant::where('id', $clinicId)->firstOrFail();
        $quiz = $clinic->run(function () use($clinic, $quizId) {
            $quiz = Questionaire::with('patient.user')->where('id', $quizId)->firstOrFail();
            $quiz->date = $quiz->created_at->format('m/d/Y');
            $userId = $quiz->patient->user->id;
            $quiz->download = "/file/$clinic->id/$userId/reports/$quiz->physician_report";
            return $quiz;
        });
        $questions = $this->questions();
        return view('dashboard.questionnaire.view')->with(compact('clinic', 'quiz', 'questions'));
    }

    public function getClinicQuizzes($clinic) {
        return $clinic->run(function () use($clinic) {
            $quizzes = Questionaire::with('patient.user')->get();
            foreach($quizzes as &$quiz) {
                $quiz->date = $quiz->created_at->format('m/d/Y');
            }
            return $quizzes;
        }); 
    }


    private function questions(): array {
        return [
            [
                "key" => "cardio",
                "title" => "Cardiovascular",
                'questions' => [
                    'Do you experience dizziness?',
                    'Do you have brittle nails?',
                    'Frequent fast heartbeat?',
                    'Shortness of breath',
                    'Do you get headaches?',
                    'Muscle/leg cramping',
                    'Lack of Motivation',
                    'Dryness of skin',
                    'Experience mental sluggishness',
                    'Do you get sick frequently?',
                    'Slow wound healing',
                    ],
                ],
            [
                "key" => "glucose",
                "title" => "Glucose",
                "questions" => 
                [
                    'Experienced recent weight gain?',
                    'Get light-headed if meals are missed?',
                    'Fatigue after meals?',
                    'Crave sweets during the day?',
                    'Frequent thirst or appetite?',
                    'Feel shaky, jittery, or moody if missed meals?',
                    'Do you snore at night?',
                    'Difficulty losing weight?',
                    'Edema or swelling in ankles, feet, hands or wrists?',
                    'Decreased physical stamina?',
                    'Heart palpitations?',
                    'Hungry after meals?',
                ],
            ],
            [
                "key" => "endo",
                "title" => "Endocrine",
                "questions" => [
                    'Do you experience mood changes?',
                    'Inability to concentrate?',
                    'Menstruation changes/infrequency (female)',
                    'Difficulty with erections? (males)',
                    'Cannot fall/stay asleep?',
                    'Crave salt or salty foods?',
                    'Perspire easily?',
                    'Gain weight easily?',
                    'Feel cold frequently?',
                    'Decreased or low libido?',
                    'Thinning of hair?',
                    'Experience energy drops throughout the day?',
                ],
            ],
            [
                "key" => "gi",
                "title" => "GI & Hepatic",
                "questions" => [
                    'Frequent diarrhea/constipation?',
                    'Acne/Unhealthy Skin?',
                    'Sensitive to smells and odors?',
                    'Increased food reactions?',
                    'Bloating, belching, burping?',
                    'Skin outbreaks or rash?',
                    'Stool color abnormal?',
                    'Taking more than 2 medications?',
                    'Hemorrhoids?',
                    'Frequent gas?',
                    'Experience irritable bowels?',
                    'Dry flaky skin or hair?',
                ]
            ]
        ];
    }

}