<?php

namespace App\Listeners\Payments;

use App\Events\Payments\Deleted;

class DeletedPayment
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
     * @param Deleted $event
     * @return void
     */
    public function handle(Deleted $event)
    {
        //
    }
}
