<?php

namespace App\Http\Controllers\Tenants;

use App\Http\Controllers\ApiController;

use App\Models\Tenant\PhysicianProfile;
use App\Models\Tenant\User;

use Symfony\Component\HttpFoundation\Request;
use App\Http\Requests\Physician\IndexRequest;
use App\Http\Requests\Physician\StoreRequest;
use App\Http\Requests\Physician\UpdateRequest;

use App\Http\Resources\PhysicianResource;
use App\Http\Resources\PhysicianCollection;

use App\Http\Resources\ProfileCollection;

use Ellaisys\Cognito\Auth\RegistersUsers;

use Auth;
use Exception;
use Illuminate\Http\Exceptions\HttpResponseException;


class PhysicianController extends ApiController
{

	use RegistersUsers;

	protected PhysicianProfile $profile;

	public function __construct(PhysicianProfile $profile)
    {
        $this->profile = $profile;
    }

    public function index(IndexRequest $request)
    {
		$data = new PhysicianCollection($this->profile->get());
		return $this->successResponse($data);
    }

    public function show($profileId) {
		$profile = PhysicianProfile::where('id', $profileId)->firstOrFail();
		return $this->physicianResponse($profile);
	}

	public function search(Request $request)
    {
		$name = $request->name;
		$physicians = User::without(['profile'])
			->where('first_name', 'like', "%$name%")
			->orWhereRaw("concat(first_name, ' ', last_name) like '%$name%' ")
			->orWhere('last_name', 'like', "$name")
			->physicians()
			->get();
        return $this->successResponse($physicians);
    }

	public function patients($profileId) {
		$profile = PhysicianProfile::where('id', $profileId)->firstOrFail();
		$data = new ProfileCollection($profile->patients()->get());
		return $this->successResponse($data);
	}

	public function store(StoreRequest $request) {
		try {
			$physicicanProfile = $this->profile->create($request->validated()['profile']);

			$data = $request->validated()['user'];
			$data['name'] = $data['first_name'] . " " . $data['last_name'];
			$collection = collect($data);
			
			$cognitoRegistered=$this->createCognitoUser($collection);
			if (!$cognitoRegistered) {
				$physicicanProfile->delete();
				return $this->errorResponse('User creation failed', 400);
			}
			unset($data['password']);
			unset($data['name']);
			$user = User::create($data);
			
			$physicicanProfile->user()->save($user);
		} catch(Exception $ex) {
			$physicicanProfile->delete();
			return $this->errorResponse('User creation failed', 400);
		}

		return $this->physicianResponse($physicicanProfile);

	}

	public function update($profileId, UpdateRequest $request) {

		$profile = PhysicianProfile::where('id', $profileId)->firstOrFail();

		$profile->user()->update($request->validated()['user']);

		$profile->update($request->validated()['profile']);

		return $this->physicianResponse($profile);

	}

	protected function physicianResponse(PhysicianProfile $profile)
    {
		$data = new PhysicianResource($profile->load('user'));
		return $this->successResponse($data);
    }

}