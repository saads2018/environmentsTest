<?php

namespace App\Models;

use App\Helpers\AWSHelper;
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;

class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase;

    public static function getCustomColumns(): array
    {
        return [
            'id',
            'user_id',
            'name',
            'created_at',
            'updated_at'
        ];
    }
    

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getLogoUrlAttribute() {
        if($this->logo) {
            $helper = new AWSHelper();
            $logo = $helper->downloadLinkFile("$this->id/logo/$this->logo");
            return $logo;
        } else {
            return null;
        }
    }

}