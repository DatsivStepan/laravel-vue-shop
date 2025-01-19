<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class InvalidOrderRefundPaymentStatus extends Exception
{
    public function __construct($message = "", $code = 500, Throwable $previous = null)
    {
        $message = $message ?: __('errors.refund.only_completed_order_can_be_refund');

        parent::__construct($message, $code, $previous);
    }

}
