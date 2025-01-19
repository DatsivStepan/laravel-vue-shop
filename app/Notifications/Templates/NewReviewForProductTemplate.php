<?php

namespace App\Notifications\Templates;

use App\Classes\Enum\NotificationsKeys;
use Modules\Notifications\Services\Templates\Template;

class NewReviewForProductTemplate extends Template
{
    public function getDBKey(): ?string
    {
        return NotificationsKeys::NEW_REVIEW;
    }

    public function getLangKey(): ?string
    {
        return 'notifications.' . NotificationsKeys::NEW_REVIEW;
    }

    public function tagsList(): array
    {
        return [
            'product',
            'sender_full_name',
            'recipient_full_name',
            'review_text'
        ];
    }
}
