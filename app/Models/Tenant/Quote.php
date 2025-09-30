<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\UUID;
use Carbon\Carbon;

class Quote extends Model
{
    use HasFactory, UUID;

    public const TABLE_NAME = '.quotes';

    protected $fillable = [
        'id',
    	'text',
        'scheduled_at'
    ];

    protected $casts = [
        'scheduled_at' => 'date',
    ];

    public static function getList($request) {
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
			$centralQuery = $centralQuery->where('text', 'like', "%$term%");
            $tenantQuery = $tenantQuery->where('text', 'like', "%$term%");
        }

        $centralQuery = $centralQuery->union($tenantQuery);
        $centralQuery = $centralQuery->orderBy('created_at');
        $mC = Quote::fromQuery($centralQuery->toBoundSql());

        $firstQuote = $mC->first();
        $now = Carbon::now();
        $currentWeekNumber = $now->weekOfYear;
        $quoteCount = count($mC);
        $stepOne = intval($currentWeekNumber / $quoteCount);
        $stepTwo = $stepOne * $quoteCount;
        $weekNo = $stepTwo+1;
        $now->setISODate($now->year, $weekNo);
        if($firstQuote != null) {
            foreach ($mC as &$quote) {                
                $quote->scheduled_at = $now->startOfWeek();
                $now->addWeek();
            }
        }
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
        $mC = Quote::fromQuery($centralQuery->toBoundSql());
        return $mC;
    }

    public static function getOfTheDay($weekStartDate) {
        $CENTRAL_DB_NAME = config('database.connections.mysql.database');

        $centralQuery = \DB::table($CENTRAL_DB_NAME . self::TABLE_NAME);
        $tenantQuery = \DB::table(tenant('tenancy_db_name') . self::TABLE_NAME);

        $centralQuery = $centralQuery->whereNotExists(function($query) use ($CENTRAL_DB_NAME)
        {
            $query->select(\DB::raw(1))
                  ->from(tenant('tenancy_db_name') . self::TABLE_NAME, 'tenant')
                  ->whereRaw($CENTRAL_DB_NAME . self::TABLE_NAME . ".id" . " = " .  "tenant.id");
        });

        $centralQuery = $centralQuery->union($tenantQuery);
        $centralQuery = $centralQuery->orderBy('created_at');
        $mC = Quote::fromQuery($centralQuery->toBoundSql());

        $firstQuote = $mC->first();
        $now = Carbon::now();
        $currentWeekNumber = $now->weekOfYear;
        $quoteCount = count($mC);
        $stepOne = intval($currentWeekNumber / $quoteCount);
        $stepTwo = $stepOne * $quoteCount;
        $stepThree = $currentWeekNumber - $stepTwo;
        $weekNo = $stepThree+1;
        $now->setISODate($now->year, $weekNo);
        if($firstQuote != null) {
            foreach ($mC as &$quote) {                
                $quote->scheduled_at = $now->startOfWeek();
            }
        }
        return $mC;
    }

}
