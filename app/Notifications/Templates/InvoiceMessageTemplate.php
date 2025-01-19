<?php

namespace App\Notifications\Templates;

use App\Classes\Enum\NotificationsKeys;
use Modules\Notifications\Services\Templates\Template;

class InvoiceMessageTemplate extends Template
{
    public function getDBKey(): ?string
    {
        return NotificationsKeys::INVOICE_MESSAGE;
    }

    public function getLangKey(): ?string
    {
        return 'notifications.invoice_message';
    }

    public function tagsList(): array
    {
        return [
            'user_name',
            'order_id',
        ];
    }
}
