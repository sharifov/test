<?php

namespace modules\order\src\payment\events;

/**
 * Class PaymentRefundedEvent
 *
 * @property int $paymentId
 */
class PaymentRefundedEvent
{
    public int $paymentId;

    public function __construct(int $paymentId)
    {
        $this->paymentId = $paymentId;
    }
}
