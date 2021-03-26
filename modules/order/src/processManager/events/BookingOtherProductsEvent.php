<?php

namespace modules\order\src\processManager\events;

/**
 * Class BookingOtherProductsEvent
 *
 * @property int $orderId
 * @property string $date
 */
class BookingOtherProductsEvent implements StatusChangable
{
    public int $orderId;
    public string $date;

    public function __construct(int $orderId, string $date)
    {
        $this->orderId = $orderId;
        $this->date = $date;
    }

    public function getOrderId(): int
    {
        return $this->orderId;
    }

    public function getStatusName(): string
    {
        return 'Booking other products';
    }

    public function getDate(): string
    {
        return $this->date;
    }
}
