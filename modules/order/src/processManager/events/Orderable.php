<?php

namespace modules\order\src\processManager\events;

interface Orderable
{
    public function getOrderId(): int;
    public function getDate(): string;
}
