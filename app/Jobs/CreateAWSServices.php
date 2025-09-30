<?php 

namespace App\Jobs;

use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Helpers\AWSHelper;
use App\Models\Tenant\User;
use App\Models\Tenant\PhysicianProfile;

class CreateAWSServices implements ShouldQueue {

	use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

	/** @var TenantWithDatabase|Model */
    protected $tenant;

	public function __construct(TenantWithDatabase $tenant)
    {
        $this->tenant = $tenant;
    }

    public function handle()
    {
        $helper = new AWSHelper;

        $pool = $helper->createPool($this->tenant->id, $this->tenant->name);
        $poolApp = $helper->createPoolUserApp($pool['UserPool']['Id']);
       	//migrate the user to new cognito pool
       	$helper->migrateUser($this->tenant->tempEmail, $this->tenant->tempName, $pool['UserPool']['Id']);

       	// Migrate same user to tenant database
		$this->tenant->run(function () {
			$physician = PhysicianProfile::create([
				'dob' => "1970-01-01",
				'gender' => 'm',
			]);
			$physician->user()->create([
		        'first_name' => $this->tenant->tempName,
				'last_name' => '',
		        'email' => $this->tenant->tempEmail,
		    ]);
		});

		$helper->createS3ForTenant($this->tenant->id);

       	unset($this->tenant->tempEmail);
		unset($this->tenant->tempName);
       	$this->tenant->update([
       		'poolId' => $pool['UserPool']['Id'],
       		'app_client_id' => $poolApp['UserPoolClient']['ClientId'],
       		'app_client_secret' => $poolApp['UserPoolClient']['ClientSecret']
       	]);
    }

}