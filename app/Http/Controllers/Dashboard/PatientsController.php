<?php

namespace App\Http\Controllers\Dashboard;

use App\Helpers\AWSHelper;
use App\Http\Controllers\Controller;

use App\Models\Tenant;

use App\Models\Tenant\PatientProfile as TenantPatients;
use App\Models\Tenant\User as TenantUser;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

use App\Http\Requests\Clinic\IndexRequest;

use Illuminate\Validation\Rule;
use Ellaisys\Cognito\Auth\RegistersUsers;


class PatientsController extends Controller
{

    use RegistersUsers;

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
        $patients = $this->getClinicPatients($clinic); 

        return view('dashboard.patients.list')->with(compact('tenants', 'patients', 'clinic'));
    }

    public function ownerList(IndexRequest $request)
    {

        $user = auth()->user();
        $clinic = Tenant::where('user_id', $user->id)->firstOrFail();
        $patients = $this->getClinicPatients($clinic); 

        return view('ownerDashboard.patients.list')->with(compact('patients', 'clinic'));
    }

    public function view($clinicId, $patientId)
    {
        $clinic = Tenant::where('id', $clinicId)->firstOrFail();
        $patient = $this->getPatientByClinic($clinic, $patientId);
        $chartData = $patient->chartData;
        $questionaires = $patient->questionaires;
        $helper = new AWSHelper();
        foreach ($questionaires as &$q) {
            $q['link'] = $helper->downloadLinkFile("{$clinic->id}/users/{$patient->user->id}/reports/{$q['physician_report']}");
        }
        return view('dashboard.patients.edit')->with(compact('clinic', 'patient', 'chartData', 'questionaires'));
    }

    public function ownerView($patientId)
    {
        $user = auth()->user();
        $clinic = Tenant::where('user_id', $user->id)->firstOrFail();
        $patient = $this->getPatientByClinic($clinic, $patientId);
        return view('ownerDashboard.patients.edit')->with(compact('clinic', 'patient'));
    }

    public function getClinicPatients($clinic) {
        return $clinic->run(function () {
            $pts = TenantPatients::with('user', 'questionaires', 'appointments')->get();
            foreach ($pts as &$pt) {
                $pt->quizRequired = $pt->questionnaireRequired;
                $appt = $pt->appointments->first();
                if($appt) {
                    $pt->nextAppt = Carbon::createFromFormat('Y-m-d H:i:s', $appt->start_time)->toFormattedDayDateString();
                } else {
                    $pt->nextAppt = "Not defined";
                }
            }
            return $pts;
        }); 
    }

    public function getPatientByClinic($clinic, $ptId) {
        return $clinic->run(function () use($ptId) {
            $pt = TenantPatients::with('user', 'questionaires', 'appointments', 'currentHealthData', 'labResults', 'healthData')->where('id', $ptId)->firstOrFail();
            $pt->completedQuizzes = $pt->completedQuizzes2();
            $pt->currentHealthData = $pt->currentHealthData->first();
            $pt->chartData = $pt->healthData->toArray();
            $pt->questionaires = $pt->questionaires->toArray();
            return $pt;
        }); 
    }

    public function createForm() {
        $clinics = Tenant::get();
        return view('dashboard.patients.create')->with(compact('clinics'));
    }

    public function createPatient(Request $request) {
        $request->validate([
            'clinic' => 'required|string|max:255',
        ]);
        $clinic = Tenant::where('id', $request->clinic)->firstOrFail();
        
        return $clinic->run(function () use($request, $clinic) {

            $validatedRequest = array_filter($request->validate([
                'user.first_name' => 'required|string|max:255',
                'user.last_name' => 'required|string|max:255',
                'user.email' => 'required|email|max:255|unique:users,email',
                'user.phone' => 'required|string|max:255',
                'profile.dob' => 'required|date',
                'profile.gender' => ['required', Rule::in(['m', 'f'])],
                'health-data.weight' => 'required|string|max:255',
                'health-data.height_ft' => 'required|string|max:255',
                'health-data.height_in' => 'required|string|max:255',
            ]));

            $height = $validatedRequest['health-data']['height_ft'] . "'" . $validatedRequest['health-data']['height_in'] . '"';
            $validatedRequest['health-data']['bmi'] = $this->calculateBMI($height, $validatedRequest['health-data']['weight']);
            $validatedRequest['health-data']['height'] = $height;
		
            $patientProfile = TenantPatients::create($validatedRequest['profile']);

            $patientProfile->healthData()->create($validatedRequest['health-data']); 
            

            $data = $validatedRequest['user'];
            $data['name'] = $data['first_name'] . ' ' . $data['last_name'];
            $collection = collect($data);
            try {
                $cognitoRegistered=$this->createCognitoUser($collection);
                if (!$cognitoRegistered) {
                    $patientProfile->delete();
                    return redirect()->route('patient.create', ['clinicId' => $clinic->id])->with('error','Patient creation failed!');
                }
                unset($data['password']);
                unset($data['name']);
                $user = TenantUser::create($data);
                
                $patientProfile->user()->save($user);
            } catch(\Exception $ex) {
                $patientProfile->delete();
                return redirect()->route('patient.create', ['clinicId' => $clinic->id])->with('error','Patient creation failed!');
            }

            return redirect()->route('edit-patient', ['clinicId' => $clinic->id, 'patientId' => $patientProfile->id])->with('success','Patient created!');

        });

    }

    public function update($id, Request $request) {
		return redirect()->route('edit-patient');
    }

    public function resetPassword($patientId) {
        $user = auth()->user();
        $clinic = Tenant::where('user_id', $user->id)->firstOrFail();
        $patient = $this->getPatientByClinic($clinic, $patientId);
        $helper = new AWSHelper();
        $helper->resetUserPassword($clinic->poolId, $patient->user->email);
        return redirect()->route('patient.edit.owner', ['patientId' => $patientId])->with('success','Password reset successfully sent.');
    }

    public function adminResetPassword($clinicId, $patientId) {
        $clinic = Tenant::where('id', $clinicId)->firstOrFail();
        $patient = $this->getPatientByClinic($clinic, $patientId);
        $helper = new AWSHelper();
        $helper->resetUserPassword($clinic->poolId, $patient->user->email);
        return redirect()->route('edit-patient', ['clinicId' => $clinicId, 'patientId' => $patientId])->with('success','Password reset successfully sent.');
    }


    private function calculateBMI($height, $weight) {
        $mHeight = $this->convertToCm($height) / 100;
        $kgWeight = $weight * 0.45359237;
        $bmi = $kgWeight / pow($mHeight, 2);
        return round($bmi, 2);
    }
    
    private function convertToCm($inchInput) {
        $rex = '/^(\d+)\'(\d+)(?:\'\'|")$/';

        preg_match($rex, $inchInput, $matches);
        $feet = 0;
        $inch = 0;
    
        if (!empty($matches)) {
            $feet = intval($matches[1], 10);
            $inch = intval($matches[2], 10);
        } else {
            return 0;
        }
        $cmConvert = (($feet * 12) + $inch) * 2.54;
        return $cmConvert;
    }

}