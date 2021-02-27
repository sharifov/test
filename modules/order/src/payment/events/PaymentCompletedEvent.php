<?php

namespace modules\order\src\payment\events;

/**
 * Class PaymentCompletedEvent
 *
 * @property int $paymentId
 */
class PaymentCompletedEvent
{
    public int $paymentId;

    public function __construct(int $paymentId)
    {
        $this->paymentId = $paymentId;
    }
}
