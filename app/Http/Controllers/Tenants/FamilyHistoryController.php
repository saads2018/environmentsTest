<?php

namespace App\Http\Controllers\Tenants;

use Illuminate\Routing\Controller as BaseController;

use App\Http\Requests\Medical\IndexRequest;
use App\Http\Requests\Medical\StoreRequest;
use App\Http\Requests\Medical\UpdateRequest;

use App\Models\Tenant\FamilyHistory;


class FamilyHistoryController extends BaseController
{

	protected FamilyHistory $history;

	public function __construct(FamilyHistory $history)
    {
        $this->history = $history;
    }


    public function index(IndexRequest $request)
    {
        return $this->history->get();
    }

    public function show($id): array {
		$model = FamilyHistory::where('id', $id)->firstOrFail();
		return $this->medicalResponse($model);
	}

	public function store(StoreRequest $request): array {

		$conditions = FamilyHistory::create($request->validated());

		return $this->medicalResponse($conditions);

	}

	public function update($id, UpdateRequest $request): array {

		$model = FamilyHistory::where('id', $id)->firstOrFail();
		$model->update($request->validated());

		return $this->medicalResponse($model);

	}

	protected function medicalResponse(FamilyHistory $history): array
    {
        return [
        	'name' => $history->only('name'), 
        ];
    }

}