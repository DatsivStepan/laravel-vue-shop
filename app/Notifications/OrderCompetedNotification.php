<?php

namespace App\Notifications;

use App\Models\Orders;
use App\Notifications\Templates\OrderCompetedTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Notifications\Notifications\Notification;
use Modules\Notifications\Services\Templates\Template;
use App\Classes\Enum\Order\PaymentType;
use App\Services\OrderService;
use Modules\Notifications\Services\Attachments\Attachment;

class OrderCompetedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /** @var Orders */
    private $order;

    public function __construct(Orders $order)
    {
        $this->order = $order;

        $this->addAttachments();

        parent::__construct();
    }

    public function getTagsDict(): array
    {
        return [
            'user_name' => $this->order->user->name
        ];
    }

    protected function getTemplate(): Template
    {
        return new OrderCompetedTemplate($this->getTagsDict());
    }

    protected function addAttachments()
    {
        if ($this->order->checkPaymentType(PaymentType::INVOICE)) {
            $service = app(OrderService::class);

            $route = route('get_pdf',
                [
                    'id' => getSetting('connection_id'),
                    'url' => $service->generateInvoice($this->order)
                ]
            );

            $this->attachments = [
                new Attachment($route, [
                    'as' => 'invoice_' . $this->order->id . '.pdf',
                    'mime' => 'application/pdf',
               ])
            ];
        }
    }
}
