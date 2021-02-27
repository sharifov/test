<?php

namespace modules\order\src\entities\order\events;

/**
 * Class OrderPreparedEvent
 *
 * @property int $orderId
 * @property string $date
 */
class OrderPreparedEvent implements OrderStatusable
{
    public int $orderId;
    public string $date;

    public function __construct(int $orderId, string $date)
    {
        $this->orderId = $orderId;
        $this->date = $date;
    }

    public function getStatusName(): string
    {
        return 'Prepare';
    }

    public function getOrderId(): int
    {
        return $this->orderId;
    }

    public function getDate(): string
    {
        return $this->date;
    }
}
