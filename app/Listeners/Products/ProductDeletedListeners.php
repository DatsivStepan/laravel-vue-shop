<?php

namespace App\Listeners\Products;

use App\Events\Products\ProductDeleted;
use App\Jobs\DeleteProductRelations;

class ProductDeletedListeners
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
     * @param ProductDeleted $event
     * @return void
     */
    public function handle(ProductDeleted $event)
    {
        DeleteProductRelations::dispatch($event->productId);
    }
}
