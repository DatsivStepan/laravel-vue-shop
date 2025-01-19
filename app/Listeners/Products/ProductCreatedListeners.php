<?php

namespace App\Listeners\Products;

use App\Events\Products\ProductCreated;

class ProductCreatedListeners
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
     * @param ProductCreated $event
     * @return void
     */
    public function handle(ProductCreated $event)
    {

    }
}
