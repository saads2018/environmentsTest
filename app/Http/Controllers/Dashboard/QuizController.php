<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;

use App\Models\Tenant\Quiz;
use App\Models\Tenant\QuizOrder;

use Illuminate\Database\Query\JoinClause;
use Illuminate\Http\Request;
use App\Http\Requests\CorporateQuiz\IndexRequest;


class QuizController extends Controller
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
    public function list(IndexRequest $request)
    {

        $codes = \App\Enums\EducationCode::toReadableArray();
        $code = 'cardio';
        if(isset($request->code)) {
            $code = $request->code;
        }

        $orders = QuizOrder::
                   select('order', 'quiz_id', 'code')
                   ->where('code', $code);
        $quizzes = Quiz::leftJoinSub($orders, 'quiz_order', function (JoinClause $join) {
            $join->on('quizzes.id', '=', 'quiz_order.quiz_id');
        })
        ->whereJsonContains("codes", $code)
        ->orderBy('order')
        ->get();

        return view('dashboard.quizzes.list')->with(compact('quizzes', 'codes', 'code'));
    }

    public function createForm() {
        $codes = \App\Enums\EducationCode::toReadableArray();
        $medCodes = array_map(function($key, $value) {
            return ["id" => $key, "value" => $value];
          }, array_keys($codes), $codes);
        return view('dashboard.quizzes.create')->with(compact('medCodes'));
    }

    public function view($id)
    {
        $quiz = Quiz::where('id', $id)->firstOrFail();
        $codes = \App\Enums\EducationCode::toReadableArray();
        $medCodes = array_map(function($key, $value) {
            return ["id" => $key, "value" => $value];
          }, array_keys($codes), $codes);

        return view('dashboard.quizzes.edit')->with(compact('quiz', 'medCodes'));
    }

    public function update($id, Request $request) {

        $values = array_filter($request->validate([
            'title' => 'required|string|max:255',
            'article' => 'required|string',
            'questions' => 'required|array',
            'codes' => 'required|array'
        ]));

        foreach ($values['questions'] as &$question) {
            $question['correct'] = intval($question['correct']);
        }

		$quiz = Quiz::where('id', $id)->firstOrFail();
		$quiz->update($values);

		return redirect()->route('edit-quiz', ['id' => $id])->with('success', 'Quiz updated!');
    }

    public function create(Request $request) {

        $values = array_filter($request->validate([
            'title' => 'required|string|max:255',
            'article' => 'required|string',
            'questions' => 'required|array',
            'codes' => 'required|array'
        ]));

        foreach ($values['questions'] as &$question) {
            $question['correct'] = intval($question['correct']);
        }

		$quiz = Quiz::create($values);

		return redirect()->route('edit-quiz', ['id' => $quiz->id])->with('success', 'Quiz created!');
    }


    public function setOrder(Request $request) {
		$code = $request->code;
		if(isset($request->quizzes)) {
			$list = $request->quizzes;
			foreach ($list as $quiz) {
				QuizOrder::updateOrCreate(['quiz_id' => $quiz['id'], 'code' => $code], ['code' => $code, 'order' => $quiz['order']]);
			}
		}
		return redirect()->route('quiz.list')->with('success', 'Quizzes&Education order set!');
	}

}