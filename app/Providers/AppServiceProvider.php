<?php

namespace App\Providers;

use App\Classes\Factories\NotificationTemplateFactory;
use App\Repositories\UserRepository;
use App\Repositories\UserRepositoryMySQL;
use App\Services\Admin\CouponService;
use App\Services\Admin\DiscountService;
use App\Services\Taxes\Tax;
use App\Services\Taxes\UserTaxes;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Modules\Notifications\Services\TemplateFactory;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('path.public', function () {
            return base_path() . '/../public_html';
        });

        $this->app->bind(CouponService::class, \App\Services\Admin\Coupon\CouponService::class);
        $this->app->bind(DiscountService::class, \App\Services\Admin\Discount\DiscountService::class);
        $this->app->bind(UserRepository::class, UserRepositoryMySQL::class);
        $this->app->bind(Tax::class, UserTaxes::class);
        $this->app->bind(TemplateFactory::class, NotificationTemplateFactory::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
    }
}
