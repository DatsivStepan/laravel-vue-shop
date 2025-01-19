<?php

namespace App\Notifications\Templates;

use App\Classes\Enum\NotificationsKeys;
use Modules\Notifications\Services\Templates\Template;

class OrderRejectedTemplate extends Template
{
    public function getDBKey(): ?string
    {
        return NotificationsKeys::ORDER_REJECTED;
    }

    public function getLangKey(): ?string
    {
        return 'notifications.order_rejected';
    }

    public function tagsList(): array
    {
        return [
            'user_name',
        ];
    }
}
