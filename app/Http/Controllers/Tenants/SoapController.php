<?php

namespace App\Http\Controllers\Tenants;

use App\Http\Controllers\ApiController;

use App\Http\Requests\Soap\StoreRequest;
use App\Http\Requests\Soap\UpdateRequest;


use App\Http\Resources\SoapResource;

use App\Models\Tenant\Soap;


class SoapController extends ApiController
{

	protected Soap $model;

	public function __construct(Soap $model)
    {
        $this->model = $model;
    }

    public function show($id) {
		$model = Soap::where('id', $id)->firstOrFail();
		return $this->response($model);
	}

	public function store(StoreRequest $request) {
		$validatedRequest = $request->validated();

		$model = new Soap($validatedRequest);
		$model->save();
		return $this->response($model);
	}


	public function update($id, UpdateRequest $request) {
		$model = Soap::where('id', $id)->firstOrFail();
		$validatedRequest = $request->validated();
		$values = array_filter($validatedRequest);

		$model->update($values);
		return $this->response($model);

	}

    protected function response(Soap $soap)
    {
		$data = new SoapResource($soap);
		return $this->successResponse($data);
    }

}