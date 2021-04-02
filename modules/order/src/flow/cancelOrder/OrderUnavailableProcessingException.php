<?php

namespace modules\order\src\flow\cancelOrder;

use Throwable;

class OrderUnavailableProcessingException extends CanceledException
{
    public function __construct($message = "The order is not available for processing.", $code = 30, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
