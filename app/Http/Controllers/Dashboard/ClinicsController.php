<?php

namespace App\Http\Controllers\Dashboard;

use App\Helpers\AWSHelper;
use App\Http\Controllers\Controller;

use App\Models\Tenant;
use App\Models\User;

use App\Models\Tenant\PhysicianProfile as TenantPhysicians;

use Illuminate\Http\Request;
use Ellaisys\Cognito\Auth\RegistersUsers;

class ClinicsController extends Controller
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
    public function list()
    {

        $tenants = Tenant::get();

        foreach ($tenants as &$tenant) {
            $tenant->run(function () use($tenant) {
                $tenant->userCount = TenantPhysicians::count();
            });
        }

        return view('dashboard.clinics.list')->with(compact('tenants'));;
    }

    public function view($id)
    {
        $clinic = Tenant::where('id', $id)->firstOrFail();
        $physicians = $clinic->run(function () {
            return TenantPhysicians::with('user')->get();
        }); 
        return view('dashboard.clinics.edit')->with(compact('clinic', 'physicians'));
    }

    public function create(Request $request) {
        $data = $request->validate([
            'clinic-subdomain' => ['required', 'string', 'unique:tenants,id', 'max:255'],
            'clinic-name' => ['required', 'string', 'max:255'],
            'clinic-address' => ['sometimes', 'nullable', 'string', 'max:255'],
            'clinic-description' => ['sometimes', 'nullable', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'logo' => 'sometimes|nullable|mimes:jpeg,png,jpg,gif',
        ]);

        $collection = collect([
            'name' => $data['name'],
            'email' => $data['email'],
        ]);
        $this->createCognitoUser($collection);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
        ]);

        $tenant = new Tenant;
        $tenant->name = $data["clinic-name"];
        $tenant->id = $data["clinic-subdomain"];
        $tenant->user_id = $user->id;
        if($data["clinic-address"]){ 
            $tenant->address = $data["clinic-address"];
        }
        if($data["clinic-description"]){ 
            $tenant->description = $data["clinic-description"];
        }
        //used internally to copy user to the new pool and then unset email
        $tenant->tempEmail = $data['email'];
        $tenant->tempName = $data['name'];

        if(array_key_exists("logo", $data)) {
            $name =  \Illuminate\Support\Str::uuid()->toString() . ".". $data['logo']->extension();
            $data['logo']->storeAs('uploads', $name);

            $helper = new AWSHelper;
            $helper->uploadTenantFile($tenant->id, $name, 'logo');
            $tenant->logo = $name;
        }

        $tenant->save();

        return redirect()->route('clinic.list')
        ->with('success','Clinic created successfully!');
    }

    public function update($id, Request $request) {

        $values = array_filter($request->validate([
            'name' => ['required', 'string', 'max:255'],
            'address' => ['sometimes', 'nullable', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string', 'max:255'],
            'logo' => 'sometimes|nullable|mimes:jpeg,png,jpg,gif',
        ]));

        if(array_key_exists("logo", $values)) {
            $name =  \Illuminate\Support\Str::uuid()->toString() . ".". $values['logo']->extension();
            $values['logo']->storeAs('uploads', $name);

            $helper = new AWSHelper;
            $helper->uploadTenantFile($id, $name, 'logo');
            $values['logo'] = $name;
        } else {
            unset($values['logo']);
        }

		$clinic = Tenant::where('id', $id)->firstOrFail();
		$clinic->update($values);

		return redirect()->route('clinic.list')
        ->with('success', "$clinic->name updated successfully!");
    }

    public function updateOwner(Request $request) {
        $user = auth()->user();
        $clinic = Tenant::where('user_id', $user->id)->firstOrFail();
        $values = array_filter($request->validate([
            'address' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'string', 'max:255'],
            'logo' => 'sometimes|nullable|mimes:jpeg,png,jpg,gif',
        ]));

        if(array_key_exists("logo", $values)) {
            $name =  \Illuminate\Support\Str::uuid()->toString() . ".". $values['logo']->extension();
            $values['logo']->storeAs('uploads', $name);

            $helper = new AWSHelper;
            $helper->uploadTenantFile($clinic->id, $name, 'logo');
            $values['logo'] = $name;
        } else {
            unset($values['logo']);
        }

		$clinic->update($values);

		return redirect()->route('admin.home')
        ->with('success', "Clinic updated successfully!");;
    }

    public function delete($clinicId, Request $request)
    {
        $user = auth()->user();
        if(!$user->hasRole('admin')){
            return back()->with("error", "Insufficient permissions!");
        }
        Tenant::destroy($clinicId);

        return redirect()->route('clinic.list')->with("success", "Clinic removed successfully.");;
    }

    public function resetPassword($clinicId) {
        $clinic = Tenant::where('id', $clinicId)->firstOrFail();
        $helper = new AWSHelper();
        $helper->resetUserPassword(config('cognito.user_pool_id'), $clinic->user->email);
        return redirect()->route('edit-clinic', ['id' => $clinicId])
        ->with('success','Password reset successfully sent.');
    }

}