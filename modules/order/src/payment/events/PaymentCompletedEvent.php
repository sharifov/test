<?php

namespace modules\order\src\payment\events;

/**
 * Class PaymentCompletedEvent
 *
 * @property int $paymentId
 */
class PaymentCompletedEvent implements Paymentable
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
