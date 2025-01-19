<?php

namespace App\Notifications;

use App\Models\Orders;
use App\Notifications\Templates\OrderCompetedTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Notifications\Notifications\Notification;
use Modules\Notifications\Services\Templates\Template;

class InvoiceMessageNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /** @var Orders */
    private $order;

    public function __construct(Orders $order)
    {
        $this->order = $order;

        parent::__construct();
    }

    public function getTagsDict(): array
    {
        return [
            'user_name' => $this->order->user->name,
            'order_id' => $this->order->id,
        ];
    }

    protected function getTemplate(): Template
    {
        return new OrderCompetedTemplate($this->getTagsDict());
    }
}
