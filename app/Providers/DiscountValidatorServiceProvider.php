<?php

namespace App\Providers;

use App\Services\Discount\Validator;
use Illuminate\Support\ServiceProvider;

class DiscountValidatorServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('DiscountValidator', function () {
            return new Validator();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
