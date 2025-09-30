<?php

namespace App\Http\Controllers\Tenants;

use App\Http\Controllers\ApiController;

use App\Models\Tenant\Questionaire;

use App\Http\Resources\QuestionaireCollection;
use Illuminate\Http\Request;

class QuestionnaireController extends ApiController
{

	protected Questionaire $questionaire;

	public function __construct(Questionaire $questionaire)
    {
        $this->questionaire = $questionaire;
    }

    public function index(Request $request)
    {
        $per_page = $request->per_page ? $request->per_page : 10;

		$data = new QuestionaireCollection($this->questionaire->with('patient.user')->paginate($per_page));
		return $this->successResponse($data);
    }

}