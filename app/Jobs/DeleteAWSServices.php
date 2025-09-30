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
use App\Models\User;

class DeleteAWSServices implements ShouldQueue {

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
        $helper->deletePool($this->tenant->poolId);
    }

    //Todo - delete user from shared pool as well.

}