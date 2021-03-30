<?php

namespace modules\order\src\processManager\clickToBook\commands\flightProductProcessedError;

use modules\order\src\processManager\clickToBook\ErrorOrder;

/**
 * Class Handler
 *
 * @property ErrorOrder $errorOrder
 */
class Handler
{
    private ErrorOrder $errorOrder;

    public function __construct(ErrorOrder $errorOrder)
    {
        $this->errorOrder = $errorOrder;
    }

    public function handle(Command $command): void
    {
        $this->errorOrder->error($command->orderId, 'ClickToBook AutoProcessing Error. Flight Product Processed Error.');
    }
}
