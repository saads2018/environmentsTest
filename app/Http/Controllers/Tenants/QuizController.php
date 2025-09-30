<?php

namespace App\Http\Controllers\Tenants;

use App\Http\Controllers\ApiController;

use App\Http\Requests\Quiz\IndexRequest;
use App\Http\Requests\Quiz\StoreRequest;
use App\Http\Requests\Quiz\UpdateRequest;

use App\Http\Resources\QuizResource;
use App\Http\Resources\QuizCollection;

use App\Models\Tenant\QuizOrder;
use App\Models\Tenant\Quiz;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

use Exception;

class QuizController extends ApiController
{

	protected Quiz $quiz;

	public function __construct(Quiz $quiz)
    {
        $this->quiz = $quiz;
    }

    public function index(IndexRequest $request)
    { 
		$user = auth()->guard('api')->user();

		$per_page = $request->per_page ? $request->per_page : 10;

		$model = Quiz::getList($user, $request);
		$paginated = $model->paginate($per_page);
		$model = $paginated->setCollection($paginated->getCollection()->values());
		$data = new QuizCollection($model);
		return $this->successResponse($data); 
    }

    public function show($id) {
		$model = Quiz::getById($id)->firstOrFail();
		return $this->quizResponse($model);
	}

	public function store(StoreRequest $request) {
        $values = array_filter($request->validated());
        $userProfile = auth()->guard('api')->user()->profile;
        $values = array_merge($values, ['physician_id' => $userProfile->id]);
		$quiz = Quiz::create($values);
		return $this->quizResponse($quiz);

	}

	//TODO - add ability to show central items as well
	public function completed() {
		$userProfile = auth()->guard('api')->user()->profile;
		$collection = $this->quiz->whereHas('patientsCompleted', function ($query) use($userProfile) {
			$query->where('profile_id', $userProfile->id);
		});
		$data = new QuizCollection($collection->get());
		return $this->successResponse($data);
	} 


	public function update($id, UpdateRequest $request) {
		$model = Quiz::getById($id)->firstOrFail();
		$model->updateOrCreate(["id" => $id], $request->validated());
		return $this->quizResponse($model);

	}


	public function setOrder(Request $request) {
		$code = $request->code;
		if(isset($request->quizzes)) {
			$list = $request->quizzes;
			foreach ($list as $quiz) {
				QuizOrder::updateOrCreate(['quiz_id' => $quiz['id'], 'code' => $code], ['code' => $code, 'order' => $quiz['order']]);
			}
		}
		return $this->successResponse("New quiz positions are set");
	}

	public function delete($id) {
		$model = Quiz::where('id', $id)->firstOrFail();
		$model->delete();
		return $this->successResponse('Quiz deleted');

	}

    protected function quizResponse(Quiz $quiz)
    {
		$data = new QuizResource($quiz);
		return $this->successResponse($data);
    }

}