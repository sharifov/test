<?php

namespace modules\order\src\processManager\clickToBook\events;

/**
 * Class FlightProductProcessedErrorEvent
 *
 * @property int $orderId
 */
class FlightProductProcessedErrorEvent
{
    public int $orderId;

    public function __construct(int $orderId)
    {
        $this->orderId = $orderId;
    }
}
