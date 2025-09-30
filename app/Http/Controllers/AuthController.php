<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Request;

use Ellaisys\Cognito\AwsCognitoClaim;
use Ellaisys\Cognito\Auth\AuthenticatesUsers;
use Ellaisys\Cognito\Auth\ChangePasswords;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;

use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Facades\Validator;

use Illuminate\Routing\Controller as BaseController;

use Exception;
use Ellaisys\Cognito\Exceptions\AwsCognitoException;
use Ellaisys\Cognito\Exceptions\NoLocalUserException;
use Symfony\Component\HttpKernel\Exception\HttpException;

use Ellaisys\Cognito\Auth\RegistersUsers;
use App\Models\User;


class AuthController extends BaseController
{
    use AuthenticatesUsers; 
    use ChangePasswords;
    use RegistersUsers;


    public function login(Request $request)
    {
        try
        {
            //Create credentials object
            $collection = collect($request->all());

            $response = $this->attemptLogin($collection, 'web');

            if ($response===true) {
                $request->session()->regenerate();

                return redirect(route('admin.home'));

            } else  {
                return $response;
                if($response instanceof Illuminate\Validation\ValidationException) {
                    return redirect()
                    ->back()
                    ->withInput($request->only('email', 'remember'))
                    ->withErrors([
                        'email' => 'Incorrect email and/or password!',
                    ]);
                }
                    
            }
        } catch (Exception $e) {
            Log::error($e->getMessage());
            $response = $this->sendFailedLoginResponse($collection, $e);
            return $response;
        }
    }

}
