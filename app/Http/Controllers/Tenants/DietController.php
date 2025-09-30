<?php

namespace App\Http\Controllers\Tenants;

use App\Http\Controllers\ApiController;

use App\Http\Requests\Diet\IndexRequest;
use App\Http\Requests\Diet\StoreRequest;
use App\Http\Requests\Diet\UpdateRequest;


use App\Http\Resources\DietResource;
use App\Http\Resources\DietCollection;

use App\Models\Tenant\Diet;

use Illuminate\Support\Str;

use Exception;

class DietController extends ApiController
{

	protected Diet $diet;

	public function __construct(Diet $diet)
    {
        $this->diet = $diet;
    }

    public function index(IndexRequest $request)
    {
		$user = auth()->guard('api')->user();
		$per_page = $request->per_page ? $request->per_page : 10;
		$model = Diet::getList($user, $request);

		$paginated = $model->paginate($per_page);
		$model = $paginated->setCollection($paginated->getCollection()->values());
		$data = new DietCollection($model);
		return $this->successResponse($data); 
    }

    public function show($id) {
		$model = Diet::getById($id)->firstOrFail();
		return $this->dietResponse($model);
	}

	public function store(StoreRequest $request) {
		$values = array_filter($request->validated());
		$data = array();
		$data['days'] = $values['days'];
		$values['data'] = $data;
		$diet = Diet::create($values);
		return $this->dietResponse($diet);
	}


	public function update($id, UpdateRequest $request) {
		$model = Diet::getById($id)->firstOrFail();
		$values = array_filter($request->validated());
		$data = array();
		$data['days'] = $values['days'];
		$values['data'] = $data;
		$model->updateOrCreate(["id" => $id], $values);
		return $this->dietResponse($model);

	}

	public function delete($id) {
		$model = Diet::where('id', $id)->firstOrFail();
		$model->delete();
		return $this->successResponse('Diet deleted');

	}

    protected function dietResponse(Diet $diet)
    {
		$data = new DietResource($diet);
		return $this->successResponse($data);
    }

}