<?php

namespace modules\order\src\payment\events;

/**
 * Class PaymentRefundedEvent
 *
 * @property int $paymentId
 */
class PaymentRefundedEvent implements Paymentable
{
    public int $paymentId;

    public function __construct(int $paymentId)
    {
        $this->paymentId = $paymentId;
    }

    public function getPaymentId(): int
    {
        return $this->paymentId;
    }
}
