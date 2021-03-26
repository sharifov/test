<?php

namespace modules\order\src\processManager\phoneToBook\events;

interface Orderable
{
    public function getOrderId(): int;
    public function getDate(): string;
}
