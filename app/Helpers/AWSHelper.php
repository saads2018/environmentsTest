<?php

namespace App\Helpers;

use Aws\CognitoIdentityProvider\CognitoIdentityProviderClient;

use Aws\Sdk;

class AWSHelper {

	// TODO - all these actions can be moved to Lambda as the best practice

	protected $awsClient;

	/**
     * Create a new helper instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->awsClient = new Sdk(
        	[
				'version' => config('cognito.version'),
    			'region' => config('cognito.region'),
		    	'credentials' => config('cognito.credentials'),
			]);
    }

	public function createPool($name, $clinicName) {
		$cognitoClient = $this->awsClient->createCognitoIdentityProvider();
		$nameTag = str_replace(' ', '-', $clinicName);
		$nameTag = preg_replace('/[^A-Za-z0-9\-]/', '', $nameTag);
		return $cognitoClient->createUserPool([
		    'AccountRecoverySetting' => [
		        'RecoveryMechanisms' => [
		            [
		                'Name' => 'verified_email',
		                'Priority' => 1,
		            ],
		        ],
		    ],
		    'AdminCreateUserConfig' => [
		        'AllowAdminCreateUserOnly' => false,
		    ],
			'EmailConfiguration' => [
				'EmailSendingAccount' => 'DEVELOPER',
				'SourceArn' => 'arn:aws:ses:us-east-2:960460839327:identity/denis@naviwellgroup.com'
			],
			'LambdaConfig' => [
				'CustomMessage' => 'arn:aws:lambda:us-east-2:960460839327:function:TenantEmailCustomizer',
			],
		    'PoolName' => $name,
		    'UsernameConfiguration' => [
		        'CaseSensitive' => false,
		    ],
			'UserPoolTags' => [
				'name' => $nameTag,
				'subdomain' => $name,
			],
		]);

	}

	public function deletePool($poolId) {
		try {
			$cognitoClient = $this->awsClient->createCognitoIdentityProvider();
			$cognitoClient->deleteUserPool([
				'UserPoolId' => $poolId
			]);
		} catch(\Exception $ex) {}
    }

	public function deleteUser($poolId, $email) {
		try {
			$cognitoClient = $this->awsClient->createCognitoIdentityProvider();
			$cognitoClient->adminDeleteUser([
				'UserPoolId' => $poolId,
    			'Username' => $email,
			]);
		} catch(\Exception $ex) {}
	}

	public function createPoolUserApp($poolId) {
		$cognitoClient = $this->awsClient->createCognitoIdentityProvider();
		return $cognitoClient->createUserPoolClient([
			'AccessTokenValidity' => 1,
			'IdTokenValidity' => 1,
			'RefreshTokenValidity' => 180,
			'TokenValidityUnits' => [
				'AccessToken' => 'days',
				'IdToken' => 'days',
				'RefreshToken' => 'days',
			],
		    'ClientName' => 'laravel-app',
		    'ExplicitAuthFlows' => ['ALLOW_ADMIN_USER_PASSWORD_AUTH', 'ALLOW_REFRESH_TOKEN_AUTH'],
		    'GenerateSecret' => true,
		    'UserPoolId' => $poolId,
		]);
	}

	public function migrateUser($email, $name, $poolId) {
		$cognitoClient = $this->awsClient->createCognitoIdentityProvider();
		$user = $cognitoClient->adminCreateUser([
		    'DesiredDeliveryMediums' => ['EMAIL'],
		    'ForceAliasCreation' => true,
		    'UserAttributes' => [
		        [
		            'Name' => 'email',
		            'Value' => $email,
		        ],
				[
					'Name' => 'name',
					'Value' => $name,
				]
		    ],
		    'UserPoolId' => $poolId,
		    'Username' => $email,
		]);

	}

	public function resetUserPassword($poolId, $email){
		$cognitoClient = $this->awsClient->createCognitoIdentityProvider();
		try {
			$result = $cognitoClient->adminResetUserPassword([
				'UserPoolId' => $poolId,
				'Username' => $email,
			]);
		} catch (\Exception $ex) {
			$cognitoClient->adminCreateUser([
				'DesiredDeliveryMediums' => ['EMAIL'],
				'MessageAction' => 'RESEND',
				'UserPoolId' => $poolId,
				'Username' => $email,
			]);
		}
	}

	// S3 management
	public function createS3ForTenant($folderName) {
		$s3Client = $this->awsClient->createS3();
		$result = $s3Client->putObject([
			'Bucket' => 'naviwell-tenants',
			'Key'    => "{$folderName}/",
            'Body'   => "",
		]);
	}



	public function uploadPDF($filename, $patientId) {
		$s3Client = $this->awsClient->createS3();
		$pathToFile = base_path() . '/storage/app/pdf/' . $filename;
		$tenantId = tenant('id');
		$result = $s3Client->putObject([
			'Bucket' => 'naviwell-tenants',
			'Key'    => "{$tenantId}/users/{$patientId}/reports/{$filename}",
            'SourceFile' => $pathToFile,
			'ContentType' => 'application/pdf'
		]);
		//delete file from local
		unlink($pathToFile);
	}

	public function uploadSharedFile($filename, $type) {
		$s3Client = $this->awsClient->createS3();
		$pathToFile = base_path() . '/storage/app/uploads/' . $filename;
		$result = $s3Client->putObject([
			'Bucket' => 'naviwell-tenants',
			'Key'    => "shared/" . $type . "/" . $filename,
            'SourceFile' => $pathToFile,
			'ContentType' => mime_content_type($pathToFile)
		]);
		//delete file from local
		unlink($pathToFile);
	}

	public function uploadTenantFile($tenantId, $filename, $type) {
		$s3Client = $this->awsClient->createS3();
		$pathToFile = base_path() . '/storage/app/uploads/' . $filename;
		$result = $s3Client->putObject([
			'Bucket' => 'naviwell-tenants',
			'Key'    => "{$tenantId}/" . $type . "/" . $filename,
            'SourceFile' => $pathToFile,
			'ContentType' => mime_content_type($pathToFile)
		]);
		//delete file from local
		unlink($pathToFile);
	}

	public function downloadLinkFile($params) {
		$s3Client = $this->awsClient->createS3();
		$fileKey = "$params";

		$cmd = $s3Client->getCommand('GetObject', [
			'Bucket' => 'naviwell-tenants',
			'Key' => $fileKey
		]);
		
		$request = $s3Client->createPresignedRequest($cmd, '+20 minutes');

		$presignedUrl = (string)$request->getUri();

		return $presignedUrl;
	}
	
}