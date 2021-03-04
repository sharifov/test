<?php

namespace modules\order\src\payment\events;

interface Paymentable
{
    public function getPaymentId(): int;
}
