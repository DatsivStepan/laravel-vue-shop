<?php

namespace App\Providers;

use App\Events\Order\CompletedOrder;
use App\Events\Order\PendedOrder;
use App\Events\Products\ProductCreated;
use App\Events\Products\ProductDeleted;
use App\Events\Products\ProductUpdated;
use App\Listeners\Discount\MakeInactive;
use App\Listeners\Notifications\SendInvoiceMessage;
use App\Listeners\Products\ProductCreatedListeners;
use App\Listeners\Products\ProductDeletedListeners;
use App\Listeners\Products\ProductUpdatedListeners;
use App\Listeners\WaitList\DeleteProductFromWaitList;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],

        ProductCreated::class => [
            ProductCreatedListeners::class,
        ],
        ProductUpdated::class => [
            ProductUpdatedListeners::class,
        ],
        ProductDeleted::class => [
            ProductDeletedListeners::class,
        ],
        PendedOrder::class => [
            MakeInactive::class,
            SendInvoiceMessage::class,
        ],
        CompletedOrder::class => [
            DeleteProductFromWaitList::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
