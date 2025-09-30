<?php

namespace App\Http\Controllers\Tenants;

use App\Http\Controllers\ApiController;

use App\Http\Requests\LabResult\IndexRequest;
use App\Http\Requests\LabResult\StoreRequest;
use App\Http\Requests\LabResult\UpdateRequest;

use App\Http\Resources\LabResultCollection;
use App\Http\Resources\LabResultResource;


use App\Models\Tenant\LabResult;

class LabResultsController extends ApiController
{

    protected LabResult $labResult;

    public function __construct(LabResult $labResult)
    {
        $this->labResult = $labResult;
    }

    public function index(IndexRequest $request)
    { 
        $user = auth()->guard('api')->user();

		$per_page = $request->per_page ? $request->per_page : 10;

		$collection = $this->labResult;
		if(isset($request->searchTerm)) {
			$term = $request->searchTerm;
			$collection = $collection->where('name', 'like', "%$term%");
        }

        if($user->isPatient) {
			$collection = $collection->where("patient_id", $user->profile->id);
		}

		$data = new LabResultCollection($collection->paginate($per_page));
		return $this->successResponse($data);
    }

    public function show($id) {
		$model = LabResult::where('id', $id)->firstOrFail();
		return $this->labResultResponse($model);
	}

	public function store(StoreRequest $request) {
		$values = array_filter($request->validated());
		$labResult = LabResult::create($values);
		return $this->labResultResponse($labResult);
	}


	public function update($id, UpdateRequest $request) {
		$model = LabResult::where('id', $id)->firstOrFail();
		$values = array_filter($request->validated());
		$model->update($values);
		return $this->labResultResponse($model);

	}

	public function delete($id) {
		$model = LabResult::where('id', $id)->firstOrFail();
		$model->delete();
		return $this->successResponse('Diagnostic result deleted');

	}

    protected function labResultResponse(LabResult $labResult)
    {
		$data = new LabResultResource($labResult);
		return $this->successResponse($data);
    }
}
