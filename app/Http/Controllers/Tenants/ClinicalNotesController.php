<?php

namespace App\Http\Controllers\Tenants;

use App\Helpers\PDF\NoteReportPdf;
use App\Http\Controllers\ApiController;

use App\Http\Requests\ClinicalNote\IndexRequest;
use App\Http\Requests\ClinicalNote\StoreRequest;
use App\Http\Requests\ClinicalNote\UpdateRequest;


use App\Http\Resources\ClinicalNoteResource;
use App\Http\Resources\ClinicalNoteCollection;

use App\Models\Tenant\ClinicalNote;
use App\Models\Tenant\HealthData;


class ClinicalNotesController extends ApiController
{

	protected ClinicalNote $note;

	public function __construct(ClinicalNote $note)
    {
        $this->note = $note;
    }

    public function index(IndexRequest $request)
    {
		$collection = $this->note->orderBy('created_at');

		$per_page = $request->per_page ? $request->per_page : 10;

		if(isset($request->limit)) {
			$per_page = $request->limit;
		}

		if(isset($request->searchTerm)) {
			$term = $request->searchTerm;
			$collection = $collection->whereHas('patient.user', function ($query) use($term) {
				$query->where('first_name', 'like', "%$term%")
				->orWhereRaw("concat(first_name, ' ', last_name) like '%$term%' ")
				->orWhere('last_name', 'like', "$term");
			});
        }

		$data = new ClinicalNoteCollection($collection->paginate($per_page));
		return $this->successResponse($data);
    }

    public function show($id) {
		$model = ClinicalNote::where('id', $id)->firstOrFail();
		return $this->noteResponse($model);
	}

	public function store(StoreRequest $request) {
		$validatedRequest = $request->validated();

		$note = new ClinicalNote($validatedRequest);

		if(array_key_exists("health-data", $validatedRequest) && array_filter($validatedRequest['health-data']) !== []){
			//create health data object
			$healthData = array_filter($validatedRequest['health-data']);
			$healthData['patient_id'] = $validatedRequest['patient_id'];
			$healthDataObject = HealthData::create($healthData);
			
			$note->health_data_id = $healthDataObject->id;
		}

		$note->save();
		return $this->noteResponse($note);
	}


	public function update($id, UpdateRequest $request) {
		$model = ClinicalNote::where('id', $id)->firstOrFail();
		$validatedRequest = $request->validated();
		$values = array_filter($validatedRequest);

		if(array_key_exists("health-data", $values) && $values['health-data'] !== []){
			//update health data object
			$healthDataObject = $model->healthData()->first();
			if($healthDataObject) {
				$healthDataObject->update($validatedRequest['health-data']);
			} else {
				$healthData = array_filter($validatedRequest['health-data']);
				$healthData['patient_id'] = $validatedRequest['patient_id'];
				$healthDataObject = HealthData::create($healthData);
				$values['health_data_id'] = $healthDataObject->id;
			}
		}

		$model->update($values);


		return $this->noteResponse($model);

	}

	public function delete($id) {
		$model = ClinicalNote::where('id', $id)->firstOrFail();
		$model->delete();
		return $this->successResponse('Note deleted');

	}

	public function pdf($id) {
		$model = ClinicalNote::where('id', $id)->with('patient', 'patient.user', 'appointment', 'healthData')->firstOrFail();

		$clinicName = tenant("name");
		$patient = $model->patient;
		$appointment = $model->appointment;
		$healthData = $model->healthData;
		$pdf = new NoteReportPdf($clinicName, $patient, $appointment, $model, $healthData);
		header('Access-Control-Allow-Origin: *');
		$pdf->Output('report.pdf' ,'I');
	}

    protected function noteResponse(ClinicalNote $note)
    {
		$data = new ClinicalNoteResource($note->load('patient', 'appointment', 'healthData'));
		return $this->successResponse($data);
    }

}