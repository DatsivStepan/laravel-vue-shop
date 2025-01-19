<?php

namespace App\Providers;

use App\Services\Admin\User\UserSettingsService;
use Illuminate\Support\ServiceProvider;

class UserSettingsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('UserSettings', function () {
            return new UserSettingsService();
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
