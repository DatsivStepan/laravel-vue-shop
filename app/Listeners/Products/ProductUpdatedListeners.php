<?php

namespace App\Listeners\Products;

use App\Events\Products\ProductUpdated;
use App\Repositories\Admin\ProductWaitlistRepository;

class ProductUpdatedListeners
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param ProductUpdated $event
     * @return void
     */
    public function handle(ProductUpdated $event)
    {
        $changesAttribute = $event->product->getChanges();
        if (array_key_exists('seats', $changesAttribute) && ($waitlists = $event->product->getWaitlist())) {
            (new ProductWaitlistRepository)->sendNotifications($waitlists);
        }
    }
}
