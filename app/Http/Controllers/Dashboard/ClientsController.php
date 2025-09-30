<?php

namespace App\Http\Controllers\Dashboard;

use App\Helpers\AWSHelper;
use App\Http\Controllers\Controller;

use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Exception;

use App\Http\Requests\Clinic\IndexRequest;
use Ellaisys\Cognito\Auth\RegistersUsers;


class ClientsController extends Controller
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
        $users = User::get();
        return view('dashboard.clients.list')->with(compact('users'));
    }


    public function view($userId)
    {
        $user = User::where('id', $userId)->firstOrFail();
        return view('dashboard.clients.edit')->with(compact('user'));
    }

    public function create(Request $request) {
        
        $values = array_filter($request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|unique:users|string|max:255',
            'is-admin' => 'sometimes|nullable|boolean',
        ]));
        try {   
            $user = User::create($values);
            $collection = collect($values);
        
            $cognitoRegistered=$this->createCognitoUser($collection);
            if (!$cognitoRegistered) {
                $user->delete();
                return redirect()->route('clients.new')
                    ->with('status', 'error')
                    ->with('message', 'User creation failed');
            }

            if(array_key_exists('is-admin', $values) && $values['is-admin'] ) {
                $user->syncRoles(['admin']);
            } else {
                $user->removeRole('admin');
            }

            return redirect()->route('clients.edit', ['userId' => $user->id]);
        } catch(Exception $ex) {
            if(isset($user)){
                $user->delete();
            }
            return redirect()->route('clients.new')
                ->with('status', 'error')
                ->with('message', $ex->getMessage());
        }
    }

    public function update($userId, Request $request) {
        $user = User::where('id', $userId)->firstOrFail();
        $values = array_filter($request->validate([
            'name' => 'sometimes|string|max:255',
            'is-admin' => 'sometimes|boolean'
            ]));
        $user->update($values);
        if(array_key_exists('is-admin', $values) && $values['is-admin'] ) {
            $user->syncRoles(['admin']);
        } else {
            $user->removeRole('admin');
        }
        return redirect()->route('clients.edit', ['userId' => $user->id])
        ->with('success','User successfully updated.');
    }


    public function resetPassword($userId) {
        $user = User::where('id', $userId)->firstOrFail();
        $helper = new AWSHelper();
        $helper->resetUserPassword(config('cognito.user_pool_id'), $user->email);
        return redirect()->route('clients.edit', ['userId' => $userId])
        ->with('success','Password successfully reset.');;
    }


    public function deleteUser($userId) {
        $user = User::where('id', $userId)->firstOrFail();
        $helper = new AWSHelper();

        $helper->deleteUser(config('cognito.user_pool_id'), $user->email);
        $user->delete();

        return redirect()->route('clients.list')
        ->with('success','User successfully deleted.');
    }

}