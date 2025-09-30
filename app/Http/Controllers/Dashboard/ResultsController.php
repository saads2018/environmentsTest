<?php

namespace App\Http\Controllers\Dashboard;

use App\Helpers\AWSHelper;
use App\Http\Controllers\Controller;

use App\Models\Tenant;

use App\Models\Tenant\LabResult as TenantPatientResults;
use App\Models\Tenant\PatientProfile as TenantPatient;


use Illuminate\Http\Request;

use App\Http\Requests\Clinic\IndexRequest;



class ResultsController extends Controller
{

	/**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function list(IndexRequest $request)
    {

        $tenants = Tenant::get();
        $clinic = $tenants[0];
        if(isset($request->name)) {
            $clinic = Tenant::where('id', $request->name)->firstOrFail();
        }
        $results = $this->getClinicResults($clinic); 

        return view('dashboard.results.list')->with(compact('tenants', 'results', 'clinic'));
    }

    public function view($clinicId, $resultId)
    {
        $clinic = Tenant::where('id', $clinicId)->firstOrFail();
        $result = $this->getResultByClinic($clinic, $resultId);
        return view('dashboard.results.edit')->with(compact('clinic', 'result'));
    }

    public function getClinicResults($clinic) {
        return $clinic->run(function () {
            $results = TenantPatientResults::with('patient')->get();

            return $results;
        }); 
    }

    private function getClinicPatients($clinic) {
        return $clinic->run(function () {
            return TenantPatient::with('user')->get();
        }); 
    }

    public function getResultByClinic($clinic, $resultId) {
        return $clinic->run(function () use($resultId) {
            return TenantPatientResults::with('patient')->where('id', $resultId)->firstOrFail();
        }); 
    }

    public function update($clinicId, $resultId, Request $request) {

        $values = array_filter($request->validate([
            'name' => 'sometimes|string|max:255',
            'patient_id' => 'sometimes|string',
            'file' => 'sometimes|array|max:255',
            'date' => 'sometimes|date',
        ]));
        
        $clinic = Tenant::where('id', $clinicId)->firstOrFail();
        $clinic->run(function () use($resultId, $values) {
            $model = TenantPatientResults::where('id', $resultId)->firstOrFail();
		    $model->update($values);
        }); 

		return redirect()->route('edit-result', ['clinicId' => $clinicId, 'resultId' => $resultId])->with('success', 'Diagnostic result successfully updated.');
    }

    public function createForm($clinicId) {
        $clinic = Tenant::where('id', $clinicId)->firstOrFail();
        $patients = $this->getClinicPatients($clinic);
        return view('dashboard.results.create')->with(compact('clinic', 'patients'));
    }

    public function create($clinicId, Request $request) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'patient_id' => 'required|string',
            'file' => 'required|file',
            'date' => 'required|date',
        ], [
            'file.required' => 'Provide diagnostic result file',
            'name.required' => 'Name is required',
            'patient_id.required' => 'Patient is required',
            'date' => 'Result date is required'
        ]);
        $values = array_filter($validated);

        $name =  \Illuminate\Support\Str::uuid()->toString() . ".". $values['file']->extension();
        $values['file']->storeAs('uploads', $name);

        $helper = new AWSHelper;
        $helper->uploadTenantFile($clinicId, $name, 'results' . "/" . $values['patient_id']);
        $values['file'] = ['name' => $values['file']->getClientOriginalName(), 'ref' => $name];
        
        $clinic = Tenant::where('id', $clinicId)->firstOrFail();
        return $clinic->run(function () use($clinicId, $values) {
            $model = TenantPatientResults::create($values);
            return redirect()->route('edit-result', ['clinicId' => $clinicId, 'resultId' => $model->id])->with('success', 'Diagnostic result successfully created.');
        }); 
    }

}