<?php

namespace src\dto\boRequest\voluntaryRefund;

/**
 * Class RefundVoluntaryDTO
 * @package src\dto\boRequest
 *
 * @property string $apiKey
 * @property string $bookingId
 *
 * @property RefundDTO $refund
 * @property BillingDTO|null $billing
 * @property PaymentDTO|null $payment
 */
class VoluntaryRefundDTO
{
    public string $apiKey;
    public string $bookingId;

    public RefundDTO $refund;
    public ?BillingDTO $billing = null;
    public ?PaymentDTO $payment = null;

    public function __construct(string $apiKey, string $bookingId, RefundDTO $refund, ?BillingDTO $billing, ?PaymentDTO $payment)
    {
        $this->apiKey = $apiKey;
        $this->bookingId = $bookingId;

        $this->refund = $refund;
        $this->billing = $billing;
        $this->payment = $payment;
    }
}
