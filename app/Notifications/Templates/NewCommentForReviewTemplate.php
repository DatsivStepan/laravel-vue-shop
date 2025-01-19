<?php

namespace App\Notifications\Templates;

use App\Classes\Enum\NotificationsKeys;
use Modules\Notifications\Services\Templates\Template;

class NewCommentForReviewTemplate extends Template
{
    public function getDBKey(): ?string
    {
        return NotificationsKeys::NEW_COMMENT_REVIEW;
    }

    public function getLangKey(): ?string
    {
        return 'notifications.' . NotificationsKeys::NEW_COMMENT_REVIEW;
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
