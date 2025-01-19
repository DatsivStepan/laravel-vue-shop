<?php

namespace App\Notifications\Templates;

use App\Classes\Enum\NotificationsKeys;
use Modules\Notifications\Services\Templates\Template;

class ProductAvailableTemplate extends Template
{
    public function getDBKey(): ?string
    {
        return NotificationsKeys::PRODUCT_AVAILABLE;
    }

    public function getLangKey(): ?string
    {
        return 'notifications.product_available';
    }

    public function tagsList(): array
    {
        return [
            'product',
            'user_full_name',
        ];
    }
}
