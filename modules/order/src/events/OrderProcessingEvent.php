<?php

namespace modules\order\src\events;

use modules\order\src\entities\order\Order;
use yii\base\Component;

/**
 * Class OrderProcessingEvent
 * @package modules\order\src\events
 *
 * @property Order $order
 */
class OrderProcessingEvent extends Component
{
    public $order;

    public function __construct(Order $order, $config = [])
    {
        parent::__construct($config);
        $this->order = $order;
    }
}
