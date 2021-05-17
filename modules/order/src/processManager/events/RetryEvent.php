<?php

namespace modules\order\src\processManager\events;

use modules\order\src\processManager\OrderProcessManager;

/**
 * Class CreatedEvent
 *
 * @property OrderProcessManager $order
 * @property string $date
 */
class RetryEvent implements StatusChangable
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

    public function getStatusName(): string
    {
        return 'Retrying';
    }
}
