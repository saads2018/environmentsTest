<?php

namespace App\Http\Controllers\Dashboard;

use App\Helpers\AWSHelper;
use App\Http\Controllers\Controller;

use App\Models\Tenant;

use App\Models\Tenant\PhysicianProfile as TenantPhysicians;
use App\Models\Tenant\User as TenantUser;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Exception;

use App\Http\Requests\Clinic\IndexRequest;
use Ellaisys\Cognito\Auth\RegistersUsers;

use Spatie\Permission\Models\Role;


class UsersController extends Controller
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
        $physicians = $this->getClinicPhysicians($clinic); 

        return view('dashboard.users.list')->with(compact('tenants', 'physicians', 'clinic'));
    }

    public function ownerList(IndexRequest $request)
    {

        $user = auth()->user();
        $clinic = Tenant::where('user_id', $user->id)->firstOrFail();
        $physicians = $this->getClinicPhysicians($clinic); 
        return view('ownerDashboard.users.list')->with(compact('physicians', 'clinic'));
    }

    public function view($clinicId, $patientId)
    {
        $clinic = Tenant::where('id', $clinicId)->firstOrFail();
        $physician = $this->getPhysicianByClinic($clinic, $patientId);
        return view('dashboard.users.edit')->with(compact('clinic', 'physician'));
    }

    public function ownerView($physicianId)
    {
        $user = auth()->user();
        $clinic = Tenant::where('user_id', $user->id)->firstOrFail();
        $physician = $this->getPhysicianByClinic($clinic, $physicianId);
        $roles = $clinic->run(function () {
            return Role::get();
        });
        return view('ownerDashboard.users.edit')->with(compact('clinic', 'physician', 'roles'));
    }

    public function adminView($clinicId, $physicianId)
    {
        $clinic = Tenant::where('id', $clinicId)->firstOrFail();
        $physician = $this->getPhysicianByClinic($clinic, $physicianId);
        $roles = $clinic->run(function () {
            return Role::get();
        });
        return view('ownerDashboard.users.edit')->with(compact('clinic', 'physician', 'roles'));
    }

    public function getClinicPhysicians($clinic) {
        return $clinic->run(function () {
            $physicians = TenantPhysicians::with('user')->get();
            return $physicians;
        }); 
    }

    public function getPhysicianByClinic($clinic, $phId) {
        return $clinic->run(function () use($phId) {
            $ph = TenantPhysicians::with('user', 'user.roles')->where('id', $phId)->firstOrFail();
            return $ph;
        }); 
    }

    public function createUserAdminForm($clinicId) {
        $clinic = Tenant::where('id', $clinicId)->firstOrFail();
        $roles = $clinic->run(function () {
            return Role::get();
        });
        return view('dashboard.clinics.users.create')->with(compact('clinic', 'roles'));
    }

    public function createUserAdmin($clinicId, Request $request) {
        $clinic = Tenant::where('id', $clinicId)->firstOrFail();
        return $this->createUser($clinic, true, $request);
    }

    public function create(Request $request) {
        $user = auth()->user();
        $clinic = Tenant::where('user_id', $user->id)->firstOrFail();
        return $this->createUser($clinic, false, $request);
    }

    public function update($id, Request $request) {
        $user = auth()->user();
        $clinic = Tenant::where('user_id', $user->id)->firstOrFail();
        return $clinic->run(function () use ($request, $id) {
            
            $values = array_filter($request->validate([
                'user.first_name' => 'sometimes|string|max:255',
                'user.last_name' => 'sometimes|string|max:255',
                'user.image' => 'sometimes|nullable|mimes:jpeg,png,jpg,gif',
                'user.phone' => 'sometimes|string|max:255',
                'profile.dob' => 'sometimes|date|date_format:Y-m-d',
                'profile.gender' => ['sometimes', Rule::in(['m', 'f'])],
                'role' => 'sometimes|nullable|string|max:255',
            ]));

            $profile = TenantPhysicians::where('id', $id)->firstOrFail();
		    $profile->user()->update($values['user']);

            if(array_key_exists('role', $values)) {
                $role = Role::where('id', $values['role'])->first();
                if($role) {
                    $profile->user->syncRoles([$role]);
                }
            }

		    $profile->update($values['profile']);
            return redirect()->route('users.edit.owner', ['physicianId' => $profile->id]);


        });
    }


    public function adminUpdate($clinicId, $id, Request $request) {
        $clinic = Tenant::where('id', $clinicId)->firstOrFail();
        return $clinic->run(function () use ($request, $clinicId, $id) {
            
            $values = array_filter($request->validate([
                'user.first_name' => 'sometimes|string|max:255',
                'user.last_name' => 'sometimes|string|max:255',
                'user.image' => 'sometimes|nullable|mimes:jpeg,png,jpg,gif',
                'user.phone' => 'sometimes|string|max:255',
                'profile.dob' => 'sometimes|date|date_format:Y-m-d',
                'profile.gender' => ['sometimes', Rule::in(['m', 'f'])],
                'role' => 'sometimes|nullable|string|max:255',
            ]));

            $profile = TenantPhysicians::where('id', $id)->firstOrFail();
		    $profile->user()->update($values['user']);

            if(array_key_exists('role', $values)) {
                $role = Role::where('id', $values['role'])->first();
                if($role) {
                    $profile->user->syncRoles([$role]);
                }
            }

		    $profile->update($values['profile']);
            return redirect()->route('users.edit.admin', ['clinicId' => $clinicId, 'physicianId' => $profile->id]);


        });
    }


    public function resetPassword($physicianId) {
        $user = auth()->user();
        $clinic = Tenant::where('user_id', $user->id)->firstOrFail();
        $physician = $this->getPhysicianByClinic($clinic, $physicianId);
        $helper = new AWSHelper();
        $helper->resetUserPassword($clinic->poolId, $physician->user->email);
        return redirect()->route('users.edit.owner', ['physicianId' => $physicianId]);
    }

    public function adminResetPassword($clinicId, $physicianId) {
        $clinic = Tenant::where('id', $clinicId)->firstOrFail();
        $physician = $this->getPhysicianByClinic($clinic, $physicianId);
        $helper = new AWSHelper();
        $helper->resetUserPassword($clinic->poolId, $physician->user->email);
        return redirect()->route('users.edit.admin', ['clinicId' => $clinicId, 'physicianId' => $physicianId]);
    }

    public function deleteUser($physicianId) {
        $user = auth()->user();
        $clinic = Tenant::where('user_id', $user->id)->firstOrFail();
        $physician = $this->getPhysicianByClinic($clinic, $physicianId);
        $helper = new AWSHelper();
        $helper->deleteUser($clinic->poolId, $physician->user->email);
        $clinic->run(function () use ($physician) {
            $physician->user->delete();
            $physician->delete();
        });

        return redirect()->route('users.list.owner')
        ->with('success','User successfully deleted.');
    }

    public function adminDeleteUser($clinicId, $physicianId) {
        $clinic = Tenant::where('id', $clinicId)->firstOrFail();
        $physician = $this->getPhysicianByClinic($clinic, $physicianId);
        $helper = new AWSHelper();
        $helper->deleteUser($clinic->poolId, $physician->user->email);
        $clinic->run(function () use ($physician) {
            $physician->user->delete();
            $physician->delete();
        });
        return redirect()->route('edit-clinic', ['id' => $clinicId])
        ->with('success','User successfully deleted.');
    }

    private function createUser($clinic, $isAdmin, Request $request) {
        return $clinic->run(function () use ($request, $clinic, $isAdmin) {
            try {
                $values = array_filter($request->validate([
                    'user.first_name' => 'required|string|max:255',
                    'user.last_name' => 'required|string|max:255',
                    'user.image' => 'sometimes|nullable|mimes:jpeg,png,jpg,gif',
                    'user.email' => 'required|email|max:255|unique:users,email',
                    'user.phone' => 'required|string|max:255',
                    'profile.dob' => 'required|date|date_format:Y-m-d',
                    'profile.gender' => ['required', Rule::in(['m', 'f'])],
                    'role' => 'sometimes|string|max:255',
                ]));
                
                $physicicanProfile = TenantPhysicians::create($values['profile']);
                $data = $values['user'];
			    $data['name'] = $data['first_name'] . " " . $data['last_name'];
			    $collection = collect($data);
			
                $cognitoRegistered=$this->createCognitoUser($collection);
                if (!$cognitoRegistered) {
                    $physicicanProfile->delete();
                    if($isAdmin){
                        return redirect()->route('users.new.admin', ['clinicId' => $clinic->id])
                        ->with('status', 'error')
                        ->with('message', 'User creation failed');
                    } else {
                        return redirect()->route('users.new.owner')
                        ->with('status', 'error')
                        ->with('message', 'User creation failed');
                    }
                    
                }
                unset($data['password']);
                unset($data['name']);
                $user = TenantUser::create($data);
                
                $physicicanProfile->user()->save($user);

                if(array_key_exists('role', $values)) {
                    $role = Role::where('id', $values['role'])->first();
                    if($role) {
                        $physicicanProfile->user->syncRoles([$role]);
                    }
                }

                if($isAdmin){
                    return redirect()->route('users.edit.admin', ['clinicId' => $clinic->id, 'physicianId' => $physicicanProfile->id]);
                } else {
                    return redirect()->route('users.edit.owner', ['physicianId' => $physicicanProfile->id]); 
                }
                
            } catch(Exception $ex) {
                if(isset($physicicanProfile)){
                    $physicicanProfile->delete();
                }

                if($isAdmin){
                    return redirect()->route('users.new.admin', ['clinicId' => $clinic->id])
                    ->with('status', 'error')
				    ->with('message', $ex->getMessage());
                } else {
                    return redirect()->route('users.new.owner')
                    ->with('status', 'error')
				    ->with('message', $ex->getMessage());
                }

                
            }
        });
    }

}