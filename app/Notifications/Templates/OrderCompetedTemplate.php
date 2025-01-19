<?php

namespace App\Notifications\Templates;

use App\Classes\Enum\NotificationsKeys;
use Modules\Notifications\Services\Templates\Template;

class OrderCompetedTemplate extends Template
{
    public function getDBKey(): ?string
    {
        return NotificationsKeys::ORDER_COMPLETED;
    }

    public function getLangKey(): ?string
    {
        return 'notifications.order_completed';
    }

    public function tagsList(): array
    {
        return [
            'user_name',
        ];
    }
}
