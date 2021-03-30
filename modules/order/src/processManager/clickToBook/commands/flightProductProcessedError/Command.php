<?php

namespace modules\order\src\processManager\clickToBook\commands\flightProductProcessedError;

/**
 * Class Command
 *
 * @property int $orderId
 */
class Command
{
    public int $orderId;

    public function __construct(int $orderId)
    {
        $this->orderId = $orderId;
    }
}
