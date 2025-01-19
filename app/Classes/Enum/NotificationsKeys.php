<?php

namespace App\Classes\Enum;

class NotificationsKeys extends AbstractEnum
{
    public const ORDER_COMPLETED = 'order-completed';
    public const ORDER_REJECTED = 'order-rejected';
    public const PRODUCT_AVAILABLE = 'product-available';
    public const NEW_REVIEW = 'new-review';
    public const NEW_COMMENT_REVIEW = 'new-comment-review';
    public const INVOICE_MESSAGE = 'invoice-message';
}
