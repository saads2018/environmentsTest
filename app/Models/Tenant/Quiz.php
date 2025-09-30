<?php

namespace App\Models\Tenant;

use App\Models\Tenant\QuizOrder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\AsEnumCollection;

use App\Traits\UUID;
use App\Enums\EducationCode;

use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Carbon;

class Quiz extends Model
{
    use HasFactory, UUID;

    public const TABLE_NAME = '.quizzes';
    public const ORDER_TABLE_NAME = '.quiz_order';

     /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'title',
    	'article',
        'questions',
        'codes',
        'physician_id',
    ];

    protected $casts = [
        'questions' => 'array',
        'codes' => AsEnumCollection::class.':'.EducationCode::class,
    ];
    public function patientsCompleted() {
        return $this->belongsToMany('App\Models\Tenant\PatientProfile', 'profile_quizzes', 'quiz_id', 'profile_id')->withPivot('score', 'answer_data');;
    }

    public function sortOrder() {
        return $this->hasMany(QuizOrder::class);
    }

    public static function getList($user, $request) {
        $CENTRAL_DB_NAME = config('database.connections.mysql.database');

        $centralQuery = \DB::table($CENTRAL_DB_NAME . self::TABLE_NAME);
        $tenantQuery = \DB::table(tenant('tenancy_db_name') . self::TABLE_NAME);

        //Order quizzes by their code & position
        $orderCentralQuery = \DB::table($CENTRAL_DB_NAME . self::ORDER_TABLE_NAME);
        $orderCentralQuery = $orderCentralQuery->select('order', 'quiz_id', 'code');

        $orderTenantQuery = \DB::table(tenant('tenancy_db_name') . self::ORDER_TABLE_NAME);
        $orderTenantQuery = $orderTenantQuery->select('order', 'quiz_id', 'code');

        $centralQuery = $centralQuery->whereNotExists(function($query) use ($CENTRAL_DB_NAME)
        {
            $query->select(\DB::raw(1))
                  ->from(tenant('tenancy_db_name') . self::TABLE_NAME, 'tenant')
                  ->whereRaw($CENTRAL_DB_NAME . self::TABLE_NAME . ".id" . " = " .  "tenant.id");
        });

        $orderCentralQuery = $orderCentralQuery->whereNotExists(function($query) use ($CENTRAL_DB_NAME)
        {
            $query->select(\DB::raw(1))
                ->from(tenant('tenancy_db_name') . self::ORDER_TABLE_NAME, 'tenant')
                ->whereRaw($CENTRAL_DB_NAME . self::ORDER_TABLE_NAME . ".quiz_id" . " = " .  "tenant.quiz_id");
        });

        if(isset($request->searchTerm)) {
			$term = $request->searchTerm;
			$centralQuery = $centralQuery->where('title', 'like', "%$term%");
            $tenantQuery = $tenantQuery->where('title', 'like', "%$term%");
        }

        if(isset($request->code)) {
            $code = $request->code;
            $centralQuery = $centralQuery->whereJsonContains("codes", $code);
            $tenantQuery = $tenantQuery->whereJsonContains("codes", $code);

            $orderCentralQuery = $orderCentralQuery->where('code', $code);
            $orderTenantQuery = $orderTenantQuery->where('code', $code);

        }

		if($user->isPatient) {
			//get only quizzes that fall for patient codes
			$dxcode = $user->profile->dxcode;
			$centralQuery = $centralQuery->whereJsonContains("codes", $dxcode);
            $tenantQuery = $tenantQuery->whereJsonContains("codes", $dxcode);

            $orderCentralQuery = $orderCentralQuery->where('code', $dxcode);
            $orderTenantQuery = $orderTenantQuery->where('code', $dxcode);

		}

        if(isset($request->code) || $user->isPatient) { 
        
            $orderCentralQuery = $orderCentralQuery->union($orderTenantQuery);

            $centralQuery = $centralQuery->leftJoinSub($orderCentralQuery, 'quiz_order', function (JoinClause $join) {
                $join->on('quiz_order.quiz_id', '=', 'quizzes.id');
            });

            $tenantQuery = $tenantQuery->leftJoinSub($orderTenantQuery, 'quiz_order', function (JoinClause $join) {
                $join->on('quiz_order.quiz_id', '=', 'quizzes.id');
            });

            if($user->isPatient) {
                $qResultsQuery = \DB::table(tenant('tenancy_db_name') . '.profile_quizzes')
                ->select('profile_id', 'quiz_id', 'created_at AS result_created_at')
                ->where('profile_id', $user->profile->id);
    
                $tenantQuery = $tenantQuery->leftJoinSub($qResultsQuery, 'profile_quizzes', function (JoinClause $join) {
                    $join->on('profile_quizzes.quiz_id', '=', 'quizzes.id');
                });

                $centralQuery = $centralQuery->leftJoinSub($qResultsQuery, 'profile_quizzes', function (JoinClause $join) {
                    $join->on('profile_quizzes.quiz_id', '=', 'quizzes.id');
                });
            }

            $centralQuery = $centralQuery->union($tenantQuery);
            $centralQuery = $centralQuery->orderBy('result_created_at', 'desc');
            $centralQuery = $centralQuery->orderBy('order');

        } else {
            $centralQuery = $centralQuery->union($tenantQuery);
        }

        // \Illuminate\Support\Facades\Log::info($centralQuery->toBoundSql());
        if($user->isPatient) {
            //get from first till n-th *completed* visit of patient
            // $apptCount = count($user->profile->rawAppointments->where('finish_time', '<=', Carbon::now()->toDateTimeString()));
            // get all completed quizzes + 1 for patient
            $quizLimit = \DB::table("profile_quizzes")->where('profile_id', $user->profile->id)->count() + 1;
            $centralQuery = $centralQuery->limit($quizLimit);
        }
        $mC = Quiz::fromQuery($centralQuery->toBoundSql());
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
        $mC = Quiz::fromQuery($centralQuery->toBoundSql());
        return $mC;
    }

}
