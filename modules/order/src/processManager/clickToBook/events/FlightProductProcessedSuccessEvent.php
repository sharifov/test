<?php

namespace modules\order\src\processManager\clickToBook\events;

/**
 * Class FlightProductProcessedSuccessEvent
 *
 * @property int $orderId
 */
class FlightProductProcessedSuccessEvent
{
    public int $orderId;

    public function __construct(int $orderId)
    {
        $this->orderId = $orderId;
    }
}
