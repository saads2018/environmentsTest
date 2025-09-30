<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

class MacroServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Builder::macro('toBoundSql', function () {
            /* @var Builder $this */
            $bindings = array_map(
                fn ($parameter) => is_string($parameter) ? "'$parameter'" : $parameter,
                $this->getBindings()
            );
 
            return Str::replaceArray(
                '?',
                $bindings,
                $this->toSql()
            );
        });
 
        EloquentBuilder::macro('toBoundSql', function () {
            return $this->toBase()->toBoundSql();
        });

        /**
         * Paginate a standard Laravel Collection.
         *
         * @param int $perPage
         * @param int $total
         * @param int $page
         * @param string $pageName
         * @return array
         */
        Collection::macro('paginate', function($perPage, $total = null, $page = null, $pageName = 'page') {
            $page = $page ?: LengthAwarePaginator::resolveCurrentPage($pageName);
            return new LengthAwarePaginator(
                $this->forPage($page, $perPage),
                $total ?: $this->count(),
                $perPage,
                $page,
                [
                    'path' => LengthAwarePaginator::resolveCurrentPath(),
                    'pageName' => $pageName,
                ]
            );
        });
 
    }
}
