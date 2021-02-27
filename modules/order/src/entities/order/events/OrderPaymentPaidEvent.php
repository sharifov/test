<?php

namespace modules\order\src\entities\order\events;

/**
 * Class OrderPaymentPaidEvent
 *
 * @property int $orderId
 * @property string $date
 */
class OrderPaymentPaidEvent implements OrderPaymentStatusable
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
        return 'Paid';
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
