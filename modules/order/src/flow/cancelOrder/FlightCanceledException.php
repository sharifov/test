<?php

namespace modules\order\src\flow\cancelOrder;

use Throwable;

class FlightCanceledException extends CanceledException
{
    public function __construct($message = "Unable to process flight cancellation.", $code = 40, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
