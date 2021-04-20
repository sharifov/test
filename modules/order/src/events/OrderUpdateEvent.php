<?php

namespace modules\order\src\events;

use yii\base\Component;

/**
 * Class OrderUpdateEvent
 * @package modules\order\src\events
 *
 * @property int $orderId
 */
class OrderUpdateEvent extends Component
{
    public int $orderId;

    public function __construct(int $orderId, $config = [])
    {
        parent::__construct($config);

        $this->orderId = $orderId;
    }
}
