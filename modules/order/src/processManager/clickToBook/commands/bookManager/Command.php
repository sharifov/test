<?php

namespace modules\order\src\processManager\clickToBook\commands\bookManager;

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
