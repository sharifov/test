<?php

namespace modules\order\src\processManager\clickToBook\commands\createManager;

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
