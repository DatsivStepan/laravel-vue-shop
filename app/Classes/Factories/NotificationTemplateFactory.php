<?php

namespace App\Classes\Factories;

use App\Classes\Enum\NotificationsKeys;
use App\Notifications\Templates\InvoiceMessageTemplate;
use App\Notifications\Templates\NewCommentForReviewTemplate;
use App\Notifications\Templates\NewReviewForProductTemplate;
use App\Notifications\Templates\OrderCompetedTemplate;
use App\Notifications\Templates\OrderRejectedTemplate;
use App\Notifications\Templates\ProductAvailableTemplate;
use Exception;
use Modules\Notifications\Services\TemplateFactory;
use Modules\Notifications\Services\Templates\Template;

class NotificationTemplateFactory implements TemplateFactory
{
    /**
     * @param $key
     * @param array $tags
     *
     * @return Template
     *
     * @throws Exception
     */
    public function make($key, array $tags): Template
    {
        switch ($key) {
            case NotificationsKeys::ORDER_COMPLETED:
                return new OrderCompetedTemplate($tags);
            case NotificationsKeys::PRODUCT_AVAILABLE:
                return new ProductAvailableTemplate($tags);
            case NotificationsKeys::NEW_COMMENT_REVIEW:
                return new NewCommentForReviewTemplate($tags);
            case NotificationsKeys::NEW_REVIEW:
                return new NewReviewForProductTemplate($tags);
            case NotificationsKeys::ORDER_REJECTED:
                return new OrderRejectedTemplate($tags);
            case NotificationsKeys::INVOICE_MESSAGE:
                return new InvoiceMessageTemplate($tags);
            default:
                throw new Exception(__('errors.notifications.templates.not_found'));
        }
    }
}
