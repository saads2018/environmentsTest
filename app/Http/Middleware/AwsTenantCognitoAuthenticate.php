<?php

namespace App\Http\Middleware;


use Ellaisys\Cognito\Http\Middleware\AwsCognitoAuthenticate;

use Illuminate\Http\Request;

use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Ellaisys\Cognito\Exceptions\InvalidTokenException;

use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Token\Parser;
use Lcobucci\JWT\Validation\Constraint\IssuedBy;
use Lcobucci\JWT\Validation\Constraint\HasClaimWithValue;
use Lcobucci\JWT\Validation\Validator;


class AwsTenantCognitoAuthenticate extends AwsCognitoAuthenticate
{

    /**
     * Attempt to authenticate a user via the token in the request.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @throws \Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException
     *
     * @return void
     */
    public function authenticate(Request $request, string $guard)
    {
        try {
            switch ($guard) {
                case 'web':
                    $user = $request->user();
                    if (!empty($user)) {

                    } //End if
                    break;
                
                default:
                    $this->checkForToken($request);
                    
                    if (! $this->cognito->parseToken()->authenticate()) {
                        throw new UnauthorizedHttpException('aws-cognito', 'User not found');
                    } //End if

                    $parser = new Parser(new JoseEncoder());
                    $token = $parser->parse($this->cognito->getToken());
                    $validator = new Validator();
                    $tenant = tenancy()->tenant;
                    $region = config('cognito.region');
                    $originalIssuer = "https://cognito-idp.{$region}.amazonaws.com/{$tenant->poolId}";
                    if (! $validator->validate($token, new IssuedBy($originalIssuer))) {
                        throw new InvalidTokenException();
                    }
                    if (! $validator->validate($token, new HasClaimWithValue('client_id', $tenant->app_client_id))) {
                        throw new UnauthorizedHttpException();
                    }

                    break;
            } //Switch ends
        } catch (Exception $e) {
            throw $e;
        } //Try-catch ends
    } //Function ends

}