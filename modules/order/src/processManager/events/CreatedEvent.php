<?php

namespace modules\order\src\processManager\events;

use modules\order\src\processManager\OrderProcessManager;

/**
 * Class CreatedEvent
 *
 * @property OrderProcessManager $order
 * @property string $date
 */
class CreatedEvent implements Orderable
{
    public OrderProcessManager $order;
    public string $date;

    public function __construct(OrderProcessManager $order, string $date)
    {
        $this->order = $order;
        $this->date = $date;
    }

    public function getOrderId(): int
    {
        return $this->order->opm_id;
    }

    public function getDate(): string
    {
        return $this->date;
    }
}
