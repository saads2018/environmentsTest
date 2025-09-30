<?php

namespace App\Http\Controllers\Tenants;

use App\Http\Controllers\ApiController;

use App\Http\Requests\Quote\IndexRequest;
use App\Http\Requests\Quote\StoreRequest;
use App\Http\Requests\Quote\MassRequest;
use App\Http\Requests\Quote\UpdateRequest;

use App\Http\Resources\QuoteResource;
use App\Http\Resources\QuoteCollection;

use App\Models\Tenant\Quote;

use Illuminate\Support\Str;

use Exception;

class QuoteController extends ApiController
{

	protected Quote $quote;

	public function __construct(Quote $quote)
    {
        $this->quote = $quote;
    }


    public function index(IndexRequest $request)
    {
		$per_page = $request->per_page ? $request->per_page : 10;

		$model = Quote::getList($request);
		$data = new QuoteCollection($model->paginate($per_page));
		return $this->successResponse($data);
    }

    public function show($id) {
		$model = Quote::getById($id)->firstOrFail();
		return $this->quoteResponse($model);
	}

	public function quoteOfTheDay() {
		$now = now();
		$weekStartDate = $now->copy()->startOfWeek()->format('Y-m-d');
		$model = Quote::getOfTheDay($weekStartDate)->first();
		if($model == null) {
			$model = Quote::getList(null)->first();
			//Notify corporate that we're out of quotes
		}

		if($model == null) {
			$quote = new Quote();
			$quote->id = "0";
			$quote->text = " ";
			$quote->scheduled_at = now();
			$quote->created_at = now();
			return $this->quoteResponse($quote);
		}
		return $this->quoteResponse($model);
	}

	public function store(StoreRequest $request) {
		$latestQuote = Quote::orderBy('scheduled_at','DESC')->first();
		$data = $request->validated();
		if($latestQuote == null) {
			$data['scheduled_at'] = now()->copy()->startOfWeek();
		} else {
			$data['scheduled_at'] = $latestQuote->scheduled_at->addWeeks(1); //get max scheduled_at + one week
		}
		$quote = Quote::create($data);
		return $this->quoteResponse($quote);

	}

	public function massStore(MassRequest $request) {
		$latestQuote = Quote::orderBy('scheduled_at','DESC')->first();
		if($latestQuote == null) {
			$latestQuoteSchedule = now()->copy()->addWeeks(-1)->startOfWeek();
		} else {
			$latestQuoteSchedule = $latestQuote->scheduled_at; //get max scheduled_at + one week
		}
		
		$validatedRequest = $request->validated();
		$arr = array();
		foreach ($validatedRequest['text'] as $key => $value) {
			if(!$value){
				continue;
			}
			$schedule = $latestQuoteSchedule->copy()->addWeeks(1);
			$latestQuoteSchedule = $schedule;
			array_push($arr, ['text' => $value, 'id' => Str::uuid()->toString(), 'scheduled_at' => $schedule]);
		}
		if(count($arr) == 0) {
			return $this->errorResponse("No quotes to add", 400);
		}
		try {
			$count = Quote::upsert($arr, ['id']);
		} catch(Exception $ex) {
			return $this->errorResponse("Unexpected error occured.", 500);
		}
		return $this->successResponse("{$count} quotes added");
	}

	public function update($id, UpdateRequest $request) {
		$model = Quote::getById($id)->firstOrFail();
		$model->updateOrCreate(["id" => $id], $request->validated());
		return $this->quoteResponse($model);

	}

	public function delete($id) {
		$model = Quote::where('id', $id)->firstOrFail();
		$model->delete();
		return $this->successResponse('Quote deleted');

	}

    protected function quoteResponse(Quote $quote)
    {
		$data = new QuoteResource($quote);
		return $this->successResponse($data);
    }

}