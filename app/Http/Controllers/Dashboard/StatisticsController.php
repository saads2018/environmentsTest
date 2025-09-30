<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;

use App\Models\Tenant;

use App\Models\Tenant\Appointment as TenantAppointment;
use App\Models\Tenant\PatientProfile as TenantPatients;

use Illuminate\Support\Carbon;

use App\Http\Requests\Clinic\IndexRequest;

class StatisticsController extends Controller
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
        $appointments = $this->getClinicAppts($clinic);
        $patients = $this->getClinicPatients($clinic); 
        return view('dashboard.statistics.list')->with(compact('tenants', 'appointments', 'patients', 'clinic'));
    }

    public function getClinicPatients($clinic) {
        return $clinic->run(function () {
            $pts = TenantPatients::with('user', 'appointments')->get();
            $start = Carbon::now()->startOfMonth();
            $end = Carbon::now()->endOfMonth();
            foreach ($pts as &$pt) {
                $appts = $pt->rawAppointments->where('start_time', ">=", $start)->where('start_time', '<=', $end)->all();
                $pt->apptCount = count($appts);
            }
            return $pts;
        }); 
    }


    public function getClinicAppts($clinic) {
        return $clinic->run(function () {
            $appts = TenantAppointment::with('patient', 'physician')->get();
           return $appts;
        }); 
    }

}