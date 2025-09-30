<?php

namespace App\Http\Controllers\Tenants;

use Illuminate\Routing\Controller as BaseController;

use App\Http\Requests\Medicine\IndexRequest;
use App\Http\Requests\Medicine\StoreRequest;
use App\Http\Requests\Medicine\UpdateRequest;

use App\Models\Tenant\Medicine;


class MedicineController extends BaseController
{

	protected Medicine $medicine;

	public function __construct(Medicine $medicine)
    {
        $this->medicine = $medicine;
    }


    public function index(IndexRequest $request)
    {
        return $this->medicine->get();
    }

    public function show($id): array {
		$model = Medicine::where('id', $id)->firstOrFail();
		return $this->medicalResponse($model);
	}

	public function store(StoreRequest $request): array {

		$conditions = Medicine::create($request->validated());

		return $this->medicalResponse($conditions);

	}

	public function update($id, UpdateRequest $request): array {

		$model = Medicine::where('id', $id)->firstOrFail();
		$model->update($request->validated());

		return $this->medicalResponse($model);

	}

	protected function medicalResponse(Medicine $medicine): array
    {
        return [
        	'name' => $medicine->only('name'), 
        ];
    }

}