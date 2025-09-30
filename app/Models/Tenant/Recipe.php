<?php

namespace App\Models\Tenant;

use App\Helpers\AWSHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\AsEnumCollection;

use App\Traits\UUID;
use App\Enums\EducationCode;


class Recipe extends Model
{
    use HasFactory, UUID;

    public const TABLE_NAME = '.recipes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
    	'title',
        'servings',
        'cook_time',
        'image',
        'attachment',
        'ingredients',
        'steps',
        'codes',
    ];

    protected $casts = [
        'ingredients' => 'array',
        'steps' => 'array',
        'codes' => AsEnumCollection::class.':'.EducationCode::class,
    ];

    public static function getList($user, $request) {
        $CENTRAL_DB_NAME = config('database.connections.mysql.database');

        $centralQuery = \DB::table($CENTRAL_DB_NAME . self::TABLE_NAME);
        $tenantQuery = \DB::table(tenant('tenancy_db_name') . self::TABLE_NAME);

        $centralQuery = $centralQuery->whereNotExists(function($query) use ($CENTRAL_DB_NAME)
        {
            $query->select(\DB::raw(1))
                  ->from(tenant('tenancy_db_name') . self::TABLE_NAME, 'tenant')
                  ->whereRaw($CENTRAL_DB_NAME . self::TABLE_NAME . ".id" . " = " .  "tenant.id");
        });
        if(isset($request->searchTerm)) {
			$term = $request->searchTerm;
			$centralQuery = $centralQuery->where('title', 'like', "%$term%");
            $tenantQuery = $tenantQuery->where('title', 'like', "%$term%");
        }

		if($user->isPatient) {
			//get only diets that fall for patient codes
			$dxcode = $user->profile->dxcode;
			$centralQuery = $centralQuery->whereJsonContains("codes", $dxcode);
            $tenantQuery = $tenantQuery->whereJsonContains("codes", $dxcode);
		}

        $centralQuery = $centralQuery->union($tenantQuery);
        $mC = Recipe::fromQuery($centralQuery->toBoundSql());
        return $mC;
    }

    public static function getById($id) {
        $CENTRAL_DB_NAME = config('database.connections.mysql.database');

        $centralQuery = \DB::table($CENTRAL_DB_NAME . self::TABLE_NAME);
        $tenantQuery = \DB::table(tenant('tenancy_db_name') . self::TABLE_NAME);

        $centralQuery = $centralQuery->where("id", $id);
        $tenantQuery = $tenantQuery->where("id", $id);
        $centralQuery = $centralQuery->whereNotExists(function($query) use ($CENTRAL_DB_NAME)
        {
            $query->select(\DB::raw(1))
                    ->from(tenant('tenancy_db_name') . self::TABLE_NAME, 'tenant')
                    ->whereRaw($CENTRAL_DB_NAME . self::TABLE_NAME . ".id" . " = " .  "tenant.id");
        });

        $centralQuery = $centralQuery->union($tenantQuery);
        $mC = Recipe::fromQuery($centralQuery->toBoundSql());
        return $mC;
    }

    //Used only in corporate dashboard
    public function getImageUrlAttribute() {
        if($this->image) {
            $helper = new AWSHelper();
            $imgRef = str_replace("shared-", "", $this->image);
            $image = $helper->downloadLinkFile("shared/recipes/$imgRef");
            return $image;
        } else {
            return \Vite::asset('resources/images/dashboard/upload.svg');
        }
    }

    //Used only in corporate dashboard
    public function getAttachmentUrlAttribute() {
        if($this->attachment) {
            $helper = new AWSHelper();
            $ref = str_replace("shared-", "", $this->attachment);
            $url = $helper->downloadLinkFile("shared/recipes/$ref");
            return $url;
        } else {
            return null;
        }
    }
}