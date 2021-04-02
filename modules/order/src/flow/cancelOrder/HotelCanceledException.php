<?php

namespace modules\order\src\flow\cancelOrder;

use Throwable;

class HotelCanceledException extends CanceledException
{
    public function __construct($message = "Unable to process hotel cancellation.", $code = 50, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
