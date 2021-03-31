<?php

namespace modules\order\src\entities\order\events;

interface OrderCancelable
{
    public function getId(): int;
}
