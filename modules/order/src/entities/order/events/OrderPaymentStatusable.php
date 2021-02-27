<?php

namespace modules\order\src\entities\order\events;

interface OrderPaymentStatusable
{
    public function getStatusName(): string;
    public function getOrderId(): int;
    public function getDate(): string;
}
