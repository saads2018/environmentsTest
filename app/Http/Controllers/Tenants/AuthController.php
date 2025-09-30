<?php

namespace App\Http\Controllers\Tenants;

use Auth;
use Ellaisys\Cognito\AwsCognitoClient;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Request;

use Ellaisys\Cognito\AwsCognitoClaim;
use Ellaisys\Cognito\Auth\AuthenticatesUsers;
use Ellaisys\Cognito\Auth\ChangePasswords;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;

use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Facades\Validator;

use App\Http\Controllers\ApiController;

use Exception;
use Ellaisys\Cognito\Exceptions\AwsCognitoException;
use Ellaisys\Cognito\Exceptions\NoLocalUserException;
use Aws\CognitoIdentityProvider\Exception\CognitoIdentityProviderException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Ellaisys\Cognito\Auth\RefreshToken;
use Ellaisys\Cognito\Auth\RegistersUsers;

use Ellaisys\Cognito\Auth\SendsPasswordResetEmails;
use Ellaisys\Cognito\Auth\ResetsPasswords;

use App\Models\Tenant\User;

use App\Helpers\AWSHelper;


class AuthController extends ApiController
{
    use AuthenticatesUsers;
    use RegistersUsers;
    use SendsPasswordResetEmails;

    use ChangePasswords, RefreshToken, ResetsPasswords {
        ChangePasswords::rules as passwordRules;
        RefreshToken::rules as refreshRules;
        ResetsPasswords::rules as resetRules;
        RefreshToken::rules insteadof ChangePasswords;
        RefreshToken::rules insteadof ResetsPasswords;
        ChangePasswords::reset as reset;
        ResetsPasswords::reset as resetPassword;
        ChangePasswords::reset insteadof ResetsPasswords;

    }

    public function register(Request $request)
    {
        $cognitoRegistered = false;

        // $this->validator($request->all())->validate();

        $data = $request->only('name', 'email', 'password');

        //Create credentials object
        $collection = collect($request->all());

        //Register User in Cognito
        $cognitoRegistered = $this->createCognitoUser($collection);
        if ($cognitoRegistered == true) {
            unset($data['password']);
            // User::create($data);
            return $this->successResponse('User created');
        } else {
            return $this->errorResponse('User creation failed', 500);
        }
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    protected function loginValidator(array $data)
    {
        return Validator::make($data, [
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string'],
        ]);
    }

    public function login(Request $request)
    {
        $validator = $this->loginValidator($request->all());
        if ($validator->fails()) {
            return $this->errorResponse($validator->messages(), 422);
        }
        //Create credentials object
        $collection = collect($request->all());

        if ($claim = $this->attemptLogin($collection, 'api', 'email', 'password', true)) {

            if ($claim instanceof AwsCognitoClaim) {

                $loginData = $claim->getData();
                return $this->successResponse(['auth' => $loginData, 'user' => $this->getRemoteUser()]);
            } else {
                if (is_array($claim)) {
                    return $this->successResponse('', $claim['status']);
                } else {
                    $claimData = $claim->getData();
                    $errorMessage = '';
                    switch ($claimData->error) {
                        case 'cognito.validation.auth.failed':
                            $errorMessage = "Authentication failed";
                            break;
                        case 'cognito.validation.auth.user_unauthorized':
                            $errorMessage = "Invalid email or password";
                        default:
                            $errorMessage = "Invalid email or password";
                            break;
                    }
                    return $this->errorResponse($errorMessage, 400);

                }
            } //End if
        } else {
            return $this->errorResponse('Incorrect email or password', 400);
        }
    }

    /**
     * Generate a new token using refresh token.
     * 
     * @throws \HttpException
     * 
     * @return mixed
     */
    public function refreshToken(Request $request)
    {

        $validator = $request->validate([
            'email' => 'required|email',
            'refresh_token' => 'required'
        ]);

        try {
            if ($claim = $this->refresh($request, 'email', 'refresh_token')) {
                if (is_array($claim)) {
                    return $this->successResponse($claim);
                } else {
                    if ($claim->getData()->error == 'cognito.validation.invalid_username') {
                        return $this->errorResponse($claim->getData(), 400);
                    }
                }
            }
        } catch (CognitoIdentityProviderException $exception) {
            return $this->errorResponse('Invalid refresh token.', 400);
        }

    }

    public function logout()
    {
        Auth::guard('api')->logout();
        return $this->successResponse(null);
    }

    public function user()
    {
        try {
            $user = $this->getRemoteUser();
            return $this->successResponse($user);
        } catch (Exception $ex) {
            return $this->errorResponse($ex->getMessage(), 400);
        }
    }

    public function updateUser(Request $request)
    {
        $userId = auth()->guard('api')->user()->id;
        $user = User::where('id', $userId)->firstOrFail();
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'image' => 'sometimes|nullable|string',
            'dob' => 'required|date',
        ]);
        $user->update([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'image' => $validated['image']
        ]);
        $user->profile->update(['dob' => $validated['dob']]);
        return $this->successResponse($user);
    }

    /**
     * Attempt to log the user into the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function getRemoteUser()
    {
        try {
            $response = auth()->guard('api')->user();
        } catch (NoLocalUserException $e) {
            $response = $this->createLocalUser($credentials);
        } catch (Exception $e) {
            return $e;
        }

        return $response;
    } //Function ends


    /**
     * Action to update the user password
     * 
     * @param  \Illuminate\Http\Request  $request
     */
    public function actionChangePassword(Request $request)
    {
        try {
            //Validate request
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'string|min:8',
                'new_password' => 'required|confirmed|min:8',
            ]);
            $validator->validate();

            // Get Current User
            $userCurrent = auth()->guard('api')->user();

            if ($this->reset($request)) {
                return $this->successResponse('Password changed');
            } else {
                return $this->errorResponse('Password change failed', 400);
            }
        } catch (Exception $e) {
            $message = 'Error sending the reset mail.';
            if ($e instanceof ValidationException) {
                $message = $e->errors();
            } else if ($e instanceof CognitoIdentityProviderException) {
                $message = $e->getAwsErrorMessage();
            } else {
                $message = $e->getMessage();
            }
            return $this->errorResponse($message, 400);
        }
    }


    public function resetPassword(Request $request)
    {
        //Cognito reset link
        $response = $this->sendCognitoResetLinkEmail($request->email);
        if ($response) {
            return $this->successResponse($response);
        }

        return $this->errorResponse('Password reset failed', 400);

    }

    public function setPassword(Request $request)
    {
        $response = '';

        try {

            $rules = [
                'code' => 'required',
                'email' => 'required|email',
                'password' => 'required|confirmed|min:8',
            ];

            //Validate request
            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            } //End if

            //Create AWS Cognito Client
            $client = app()->make(AwsCognitoClient::class);

            //Get User Data
            $user = $client->getUser($request->email);

            //Check user status and change password
            if (
                ($user['UserStatus'] == AwsCognitoClient::USER_STATUS_CONFIRMED) ||
                ($user['UserStatus'] == AwsCognitoClient::RESET_REQUIRED_PASSWORD)
            ) {
                $response = $client->resetPassword($request->code, $request->email, $request->password);
            } else {
                $response = false;
            } //End if

        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 400);
        } //Try-Catch ends

        if($response == 'passwords.reset'){
            return $this->successResponse($response);
        }

        return $this->errorResponse('Password reset failed', 400);
        

    }

}