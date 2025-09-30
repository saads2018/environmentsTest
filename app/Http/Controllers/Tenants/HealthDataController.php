<?php

namespace App\Http\Controllers\Tenants;

use App\Http\Controllers\ApiController;

use Symfony\Component\HttpFoundation\Request;

use App\Http\Requests\HealthData\StoreRequest;

use App\Models\Tenant\HealthData;
use App\Http\Resources\HealthDataResource;
use App\Http\Resources\HealthDataCollection;

class HealthDataController extends ApiController
{

    protected HealthData $healthData; 

    public function __construct(HealthData $healthData)
    {
        $this->healthData = $healthData;
    }

    public function show($id): HealthDataResource {
		$data = HealthData::where('id', $id)->firstOrFail();
		return $this->healthResponse($data);
	}

    public function all($profileId) {
        $data = HealthData::where('patient_id', $profileId)->orderBy('created_at')->get();
        $data = new HealthDataCollection($data);
		return $this->successResponse($data);
    }

    protected function healthResponse(HealthData $data): HealthDataResource
    {
        return new HealthDataResource($data);
    }

}