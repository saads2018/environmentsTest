<?php

namespace App\Http\Controllers\Tenants;

use Illuminate\Routing\Controller as BaseController;

use App\Http\Requests\Medical\IndexRequest;
use App\Http\Requests\Medical\StoreRequest;
use App\Http\Requests\Medical\UpdateRequest;

use App\Models\Tenant\MedicalConditions;


class MedicalController extends BaseController
{

	protected MedicalConditions $medicalConditions;

	public function __construct(MedicalConditions $medConditions)
    {
        $this->medicalConditions = $medConditions;
    }


    public function index(IndexRequest $request)
    {
        return $this->medicalConditions->get();
    }

    public function show($id): array {
		$model = MedicalConditions::where('id', $id)->firstOrFail();
		return $this->medicalResponse($model);
	}

	public function store(StoreRequest $request): array {

		$conditions = MedicalConditions::create($request->validated());

		return $this->medicalResponse($conditions);

	}

	public function update($id, UpdateRequest $request): array {

		$model = MedicalConditions::where('id', $id)->firstOrFail();
		$model->update($request->validated());

		return $this->medicalResponse($model);

	}

	protected function medicalResponse(MedicalConditions $medConditions): array
    {
        return [
        	'name' => $medConditions->only('name'), 
        ];
    }

}