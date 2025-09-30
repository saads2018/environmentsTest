<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;

use App\Models\Tenant;
use App\Models\Tenant\Appointment;

use App\Helpers\AWSHelper;
use Illuminate\Http\Request;

class AppointmentsController extends Controller
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
    public function list(Request $request)
    {
        $tenants = Tenant::get();
        $clinic = $tenants[0];
        if(isset($request->name)) {
            $clinic = Tenant::where('id', $request->name)->firstOrFail();
        }
        $appointments = $this->getClinicAppointments($clinic); 
        return view('dashboard.appointments.list')->with(compact('tenants', 'clinic', 'appointments'));
    }

    public function getClinicAppointments($clinic) {
        return $clinic->run(function () use($clinic) {
            $appointments = Appointment::with('patient.user')->get();
            foreach($appointments as &$appt) {
                $appt->start_date = $appt->start_time->format('Y-m-d\TH:i:s\Z');
                $appt->end_date = $appt->finish_time->format('Y-m-d\TH:i:s\Z');
            }
            return $appointments;
        }); 
    }

}