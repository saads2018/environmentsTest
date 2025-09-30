<?php

namespace App\Http\Controllers\Tenants;

use App\Http\Controllers\ApiController;

use App\Http\Requests\Recipe\IndexRequest;
use App\Http\Requests\Recipe\StoreRequest;
use App\Http\Requests\Recipe\UpdateRequest;

use App\Http\Resources\RecipeResource;
use App\Http\Resources\RecipeCollection;

use App\Models\Tenant\Recipe;

use Illuminate\Support\Str;

use Exception;

class RecipeController extends ApiController
{

	protected Recipe $recipe;

	public function __construct(Recipe $recipe)
    {
        $this->recipe = $recipe;
    }


	public function index(IndexRequest $request)
    {
		$user = auth()->guard('api')->user();

        $per_page = $request->per_page ? $request->per_page : 10;

		$model = Recipe::getList($user, $request);
		$paginated = $model->paginate($per_page);
		$model = $paginated->setCollection($paginated->getCollection()->values());
		$data = new RecipeCollection($model);
		return $this->successResponse($data); 
    }

    public function show($id) {
		$model = Recipe::getById($id)->firstOrFail();
		return $this->recipeResponse($model);
	}

	public function store(StoreRequest $request) {
        $values = array_filter($request->validated());
        //make sure to remove empty object from nested arrays
        $values['ingredients'] = array_filter($values['ingredients']);
        $values['steps'] = array_filter($values['steps']);
        
        if (count($values['steps']) == 0 || count( $values['ingredients']) == 0){
            return $this->errorResponse("Values can't be empty!", 422);
        }
		$recipe = Recipe::create($values);
		return $this->recipeResponse($recipe);

	}


	public function update($id, UpdateRequest $request) {
		$values = array_filter($request->validated());
		
        //make sure to remove empty object from nested arrays
        $values['ingredients'] = array_filter($values['ingredients']);
        $values['steps'] = array_filter($values['steps']);

		$model = Recipe::getById($id)->firstOrFail();
		$model->updateOrCreate(["id" => $id], $values);
		return $this->recipeResponse($model);

	}

	public function delete($id) {
		$model = Recipe::where('id', $id)->firstOrFail();
		$model->delete();
		return $this->successResponse('Recipe deleted');

	}

    protected function recipeResponse(Recipe $recipe)
    {
        $data = new RecipeResource($recipe);
		return $this->successResponse($data);
    }

}