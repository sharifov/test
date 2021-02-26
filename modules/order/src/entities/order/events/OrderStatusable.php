<?php

namespace modules\order\src\entities\order\events;

interface OrderStatusable
{
    public function getStatusName(): string;
    public function getOrderId(): int;
    public function getDate(): string;
}
