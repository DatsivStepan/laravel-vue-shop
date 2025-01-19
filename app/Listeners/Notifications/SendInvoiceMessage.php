<?php

namespace App\Listeners\Notifications;

use App\Notifications\InvoiceMessageNotification;

class SendInvoiceMessage
{
    public function handle($event)
    {
        //$event->order->id
        if (getSetting('attach_receipt')) {
            $event->order->user->notify(new InvoiceMessageNotification($event->order));
        }
    }
}
