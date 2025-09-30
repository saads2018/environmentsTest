<?php

namespace App\Providers;

use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //

        Collection::macro('byApptType', function ($type) {
            return $this->filter(function ($value) use ($type) {
                return $value->visit_type == $type;
            });
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        
        // Spatie/Roles
        Schema::defaultStringLength(125);

    }
}
