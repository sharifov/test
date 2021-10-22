<?php

namespace sales\dto\boRequest\voluntaryRefund;

/**
 * Class RefundDTO
 * @package sales\dto\boRequest\voluntaryRefund
 *
 * @property string $orderId
 * @property float $refundCost
 * @property string $currency
 * @property TicketDTO[] $tickets
 */
class RefundDTO
{
    public string $orderId;
    public float $refundCost;
    public string $currency;
    public array $tickets;

    public function __construct(
        string $orderId,
        float $refundCost,
        string $currency,
        array $tickets
    ) {
        $this->orderId = $orderId;
        $this->refundCost = $refundCost;
        $this->currency = $currency;
        $this->tickets = $tickets;
    }
}
