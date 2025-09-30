<?php

namespace App\Http\Controllers\Tenants;

use App\Http\Controllers\ApiController;

use App\Http\Resources\ClinicalNoteResource;
use App\Http\Resources\QuizResource;
use App\Mail\Direct\DirectMessage;
use App\Models\Tenant;
use App\Models\Tenant\ClinicalNote;
use App\Models\Tenant\PhysicianProfile;
use App\Models\Tenant\Quiz;
use Symfony\Component\HttpFoundation\Request;

use App\Models\Tenant\PatientProfile;
use App\Models\Tenant\User;

use App\Http\Requests\Patient\IndexRequest;
use App\Http\Requests\Patient\StoreRequest;
use App\Http\Requests\Patient\UpdateRequest;
use App\Http\Requests\Patient\QuestionnaireRequest;

use App\Http\Requests\HealthData\StoreRequest as HealthDataStoreRequest;


use App\Http\Resources\ProfileResource;
use App\Http\Resources\ProfileCollection;


use Ellaisys\Cognito\Auth\RegistersUsers;
use Ellaisys\Cognito\AwsCognitoClient;


use App\Enums\SleepHours;
use App\Enums\ActivityLevel;
use App\Enums\BMI;
use App\Enums\StressLevel;
use App\Enums\WaistSize;
use App\Enums\ConsumptionLevel;

use Auth;
use Exception;

use App\Helpers\QuestionnaireReportGenerator;

use Mail;
use App\Mail\Questionnaire\Completed as QuestionnaireCompleted;
use App\Mail\Questionnaire\Force as ForceQuestionnaire;


class PatientController extends ApiController
{

	use RegistersUsers;

	protected PatientProfile $profile;

	public function __construct(PatientProfile $profile)
    {
        $this->profile = $profile;
    }

    public function index(IndexRequest $request)
    {
		$collection = $this->profile->orderBy('created_at');

		$per_page = $request->per_page ? $request->per_page : 10;

		if(isset($request->limit)) {
			$per_page = $request->limit;
		}

		if(isset($request->searchTerm)) {
			$term = $request->searchTerm;
			$collection = $collection->whereHas('user', function ($query) use($term) {
				$query->where('first_name', 'like', "%$term%")
				->orWhereRaw("concat(first_name, ' ', last_name) like '%$term%' ")
				->orWhere('last_name', 'like', "$term");
			});
        }

		$data = new ProfileCollection($collection->with("appointments")->paginate($per_page));
		return $this->successResponse($data);
    }

    public function show($profileId) {
		$profile = PatientProfile::where('id', $profileId)->firstOrFail();
		return $this->patientResponse($profile);
	}

	public function delete($profileId) {
		$requestUser = auth()->guard('api')->user();

		if($requestUser->isPatient) {
			return $this->errorResponse('Access denied', 403);
		}

		$profile = PatientProfile::where('id', $profileId)->firstOrFail();
		try{
			app()->make(AwsCognitoClient::class)->deleteUser($profile->user->email);
		} catch(\Exception $ex) {}
		
		$profile->user->delete();
		$profile->delete();
		return $this->successResponse("Patient deleted along with data");
	}

	public function search(Request $request)
    {
		$name = $request->name;
		$patients = User::without(['profile'])
			->where('first_name', 'like', "%$name%")
			->orWhereRaw("concat(first_name, ' ', last_name) like '%$name%' ")
			->orWhere('last_name', 'like', "$name")
			->patients()
			->get();
        return $this->successResponse($patients);
    }

	public function store(StoreRequest $request) {
		$validatedRequest = $request->validated();
		
		$patientProfile = $this->profile->create();
		
		$additionalData = [
			'contact_info' => array_key_exists("contact", $validatedRequest) ? $validatedRequest['contact'] : '',
			'emergency_contact' => array_key_exists("emergency", $validatedRequest) ? $validatedRequest['emergency'] : '',
			'insurance_info' => array_key_exists("insurance", $validatedRequest) ? $validatedRequest['insurance'] : '',
			'meds' => array_key_exists("meds", $validatedRequest) ? $validatedRequest['meds'] : '',
			'data' => array_key_exists("additional-data", $validatedRequest) ? $validatedRequest['additional-data'] : '',
		];
		if(array_key_exists("profile", $validatedRequest)){
			$additionalData = array_merge($additionalData, $validatedRequest['profile']);
		}
		try {
			$patientProfile->update($additionalData);

			if(array_key_exists("health-data", $validatedRequest)){
				$patientProfile->healthData()->create($validatedRequest['health-data']); 
			}

			$data = $validatedRequest['user'];
			$data['name'] = $data['first_name'] . ' ' . $data['last_name'];
			$collection = collect($data);
		
			$cognitoRegistered=$this->createCognitoUser($collection);
			if (!$cognitoRegistered) {
				$patientProfile->delete();
				return $this->errorResponse('User creation failed', 400);
			}
			unset($data['password']);
			unset($data['name']);
			$user = User::create($data);
			
			$patientProfile->user()->save($user);
		} catch(Exception $ex) {
			$patientProfile->delete();
			return $this->errorResponse('User creation failed', 400);
		}

		return $this->patientResponse($patientProfile);

	}

	public function update($profileId, UpdateRequest $request) {

		$requestUser = auth()->guard('api')->user();

		$profile = PatientProfile::where('id', $profileId)->firstOrFail();
		$validatedRequest = $request->validated();

		if($requestUser->isPatient) {
			if($requestUser->profile->id != $profileId){
				return $this->errorResponse('Access denied', 403);
			}
			$profile->patient_confirmed = true;
		}

		if(array_key_exists("user", $validatedRequest)){
			$data = $validatedRequest['user'];
			$profile->user()->update($data);
		}

		$additionalData = [
			'contact_info' => array_key_exists("contact", $validatedRequest) ? $validatedRequest['contact'] : '',
			'emergency_contact' => array_key_exists("emergency", $validatedRequest) ? $validatedRequest['emergency'] : '',
			'insurance_info' => array_key_exists("insurance", $validatedRequest) ? $validatedRequest['insurance'] : '',
			'meds' => array_key_exists("meds", $validatedRequest) ? $validatedRequest['meds'] : '',
			'dxcode' => array_key_exists("dxcode", $validatedRequest) ? $validatedRequest['dxcode'] : '',
			'data' => array_key_exists("additional-data", $validatedRequest) ? $validatedRequest['additional-data'] : '',
		]; 
		if(array_key_exists("profile", $validatedRequest)){
			$additionalData = array_merge($additionalData, $validatedRequest['profile']);
		}
		//remove empty values
		$additionalData = array_filter($additionalData);
		$profile->update($additionalData);

		if(array_key_exists("health-data", $validatedRequest)){
			$healthData = $validatedRequest['health-data'];
			$healthData = array_filter($healthData);
			if($profile->currentHealthData()->count() > 0){
				$profile->currentHealthData()->update($healthData); 
			} else {
				$profile->healthData()->create($healthData);
			}
		}

		return $this->patientResponse($profile);

	}

	public function attach($patientId) {
		$user = auth()->guard('api')->user()->profile()->firstOrFail();
		$patient = PatientProfile::where('id', $patientId)->firstOrFail();
		$patient->physicians()->syncWithoutDetaching($user);

		return $this->patientResponse($patient);
	}

	public function attachMedCondition($patientId, Request $request) {
		$requestUser = auth()->guard('api')->user();
		if($requestUser->isPatient && $requestUser->profile->id != $patientId) {
			return $this->errorResponse('Access denied', 403);
		}
		
		$patient = PatientProfile::where('id', $patientId)->firstOrFail();
		$patient->conditions()->sync($request->id);

		return $this->patientResponse($patient);
	}

	public function attachFamilyHistory($patientId, Request $request) {
		$requestUser = auth()->guard('api')->user();
		if($requestUser->isPatient && $requestUser->profile->id != $patientId) {
			return $this->errorResponse('Access denied', 403);
		}

		$patient = PatientProfile::where('id', $patientId)->firstOrFail();
		$patient->familyHistory()->sync($request->id);

		return $this->patientResponse($patient);
	}

	public function attachHealthData($patientId, HealthDataStoreRequest $request) {
		$patient = PatientProfile::where('id', $patientId)->firstOrFail();
		$patient->healthData()->create($request->validated());

		return $this->patientResponse($patient);
	}

	public function getFormData(): array {
		return [
			'data' => [
				'sleep_hours' => SleepHours::toReadableArray(),
				'activity_level' => ActivityLevel::toReadableArray(),
				'bmi' => BMI::toReadableArray(),
				'stress_levels' => StressLevel::toReadableArray(),
				'waist_size' => WaistSize::toReadableArray(),
				'alcohol_consumption' => ConsumptionLevel::toReadableArray(),
				'caffeine_consumption' => ConsumptionLevel::toReadableArray(),
			]
		];
	}

	public function forceManualQuiz($patientId) {
		$patient = PatientProfile::where('id', $patientId)->firstOrFail();
		$patient->manual_quiz = true;
		$patient->save();

		$mailer = new ForceQuestionnaire($patient);
		$tenantUser = $patient->user;
		Mail::to($tenantUser->email)->send($mailer);
		return $this->successResponse('Questionnaire manually assigned.');
	}

	public function attachQuestionaire($patientId, QuestionnaireRequest $request) {
		$validatedRequest = $request->validated();
		$patient = PatientProfile::where('id', $patientId)->firstOrFail();
		$answer_data = $validatedRequest['sections'];
		$questionnaire = $patient->questionaires()->create(['answer_data' => $answer_data, 'lifestyle_data' => $validatedRequest['lifestyle_data']]);

		$reportGenerator = new QuestionnaireReportGenerator($patient, $questionnaire);
		$reportGenerator->generateReports();

		//refresh patient data
		$patient = $patient->fresh(); 
		$questionnaire = $questionnaire->fresh();

		$mailer = new QuestionnaireCompleted($questionnaire);
		$tenantUser = tenant()->user;
		Mail::to($tenantUser->email)->send($mailer);

		return $this->patientResponse($patient);
	}

	public function attachQuizResult(Request $request) {
		$requestUser = auth()->guard('api')->user();
		if(!$requestUser->isPatient) {
			return $this->errorResponse('Access denied', 403);
		}

		$patientId = $requestUser->profile->id;

		$patient = PatientProfile::where('id', $patientId)->firstOrFail();
		$patient->completedQuizzes()->syncWithoutDetaching([$request->id => ['score' => $request->score, 'answer_data' => json_encode($request->answers)]]);

		return $this->patientResponse($patient);
	}
	
	public function report($profileId) {
		$patient = PatientProfile::where('id', $profileId)->firstOrFail();

		$reportGenerator = new QuestionnaireReportGenerator($patient, $patient->questionaires->first());
		$reportGenerator->generateReports();

		return $this->successResponse('');
	}

	public function sendEmail($profileId, Request $request) {

		$patient = PatientProfile::where('id', $profileId)->firstOrFail();

		$values = array_filter($request->validate([
            'subject' => 'required|string|max:255',
            'content' => 'required|string'
        ]));

		$mailer = new DirectMessage($values['subject'], $values['content']);
		Mail::to($patient->user->email)->send($mailer);
		return $this->successResponse('');

	}

	public function lastQuiz($profileId) {
		$patient = PatientProfile::where('id', $profileId)->firstOrFail();
		$quizList = $patient->completedQuizzes2();
		$lastQuiz = end($quizList);
		if($lastQuiz){
			return $this->successResponse(new QuizResource($lastQuiz));
		} else {
			$this->successResponse(null);
		}
		
	}

	public function lastNote($profileId) {
		$patient = PatientProfile::where('id', $profileId)->firstOrFail();
		$note = $patient->clinicalNotes->last();
		if($note) {
			return $this->successResponse(new ClinicalNoteResource($note));
		}
		$this->successResponse(null);
	}

	protected function patientResponse(PatientProfile $profile)
    {
		$data = new ProfileResource($profile->load('user', 'familyHistory', 'conditions', 'appointments', 'clinicalNotes', 'clinicalNotes.appointment'));
		return $this->successResponse($data);
    }

}