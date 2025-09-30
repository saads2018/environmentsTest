<?php

namespace App\Http\Controllers\Tenants;

use App\Http\Controllers\ApiController;
use App\Models\Tenant\User;
use Illuminate\Support\Carbon;

use App\Http\Requests\Appointment\IndexRequest;
use App\Http\Requests\Appointment\StoreRequest;
use App\Http\Requests\Appointment\UpdateRequest;

use App\Http\Resources\AppointmentResource;
use App\Http\Resources\AppointmentCollection;

use App\Models\Tenant\Appointment;

use Mail;
use App\Mail\Appointment\Created as AppointmentCreated;
use App\Mail\Appointment\Updated as AppointmentUpdated;
use App\Mail\Appointment\Cancelled as AppointmentCancelled;

class AppointmentController extends ApiController
{

	protected Appointment $appt;

	public function __construct(Appointment $appt)
    {
        $this->appt = $appt;
    }


    public function index(IndexRequest $request)
    {
		$user = auth()->guard('api')->user();

		$per_page = $request->per_page ? $request->per_page : 10;

		$collection = $this->appt->orderBy('start_time', 'desc');

		if(isset($request->limit)) {
            $collection->limit($request->limit);
        }
		
		if(isset($request->upcoming) && $request->upcoming) {
			$collection = $collection->where('start_time', '>=', Carbon::now()->toDateTimeString());
		}

		if($user->isPatient) {
			$collection = $collection->where("patient_id", $user->profile->id);
		}
		$data = new AppointmentCollection($collection->paginate($per_page));
		return $this->successResponse($data);
    }

	public function listToday(IndexRequest $request){
		$user = auth()->guard('api')->user();
		$appts = Appointment::whereDate('start_time', Carbon::today())
			// ->where('start_time', '>=', Carbon::now()->toDateTimeString())
			->orderBy('start_time');
		
		if($user->isPatient) {
			$appts = $appts->where("patient_id", $user->profile->id);
		} else {
			$appts = $appts->where("physician_id", $user->profile->id);
		}
		if(isset($request->limit)) {
            $appts->limit($request->limit);
        }
		$data = new AppointmentCollection($appts->paginate());
		return $this->successResponse($data);
	}

    public function show($id) {
		$model = Appointment::where('id', $id)->firstOrFail();
		return $this->apptResponse($model);
	}

	public function store(StoreRequest $request) {
		$physicianUser =  auth()->guard('api')->user();
		$physician = $physicianUser->profile;
		$data = $request->validated();
		$patientUser = User::where('profile_id', $data['patient_id'])->firstOrFail();
		$validatedRequest = array_merge(['physician_id' => $physician->id, 'type' => 'appointment'], $data);
		$appt = Appointment::create($validatedRequest);

		$mailer = new AppointmentCreated($appt);
		Mail::to($patientUser->email)->send($mailer);
		//Disabling mail sending to provider
		// Mail::to($physicianUser->email)->send($mailer);
		return $this->apptResponse($appt);

	}

	public function update($id, UpdateRequest $request) {

		$model = Appointment::where('id', $id)->firstOrFail();
		$model->update($request->validated());

		$physicianEmail = $model->physician->user->email;
		$patientEmail = $model->patient->user->email;

		$mailer = new AppointmentUpdated($model);
		//Disabling mail sending to provider
		// Mail::to($physicianEmail)->send($mailer);
		Mail::to($patientEmail)->send($mailer);

		return $this->apptResponse($model);

	}

	public function delete($id) {
		$model = Appointment::where('id', $id)->firstOrFail();
		$model->delete();

		$physicianEmail = $model->physician->user->email;
		$patientEmail = $model->patient->user->email;
		$mailer = new AppointmentCancelled($model);
		Mail::to($physicianEmail)->send($mailer);
		Mail::to($patientEmail)->send($mailer);

		return $this->successResponse('Appointment cancelled');
	}

    protected function apptResponse(Appointment $appt)
    {
		$data = new AppointmentResource($appt->load('physician', 'patient'));
		return $this->successResponse($data);
    }

}