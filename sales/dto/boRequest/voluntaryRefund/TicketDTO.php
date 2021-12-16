<?php

namespace sales\dto\boRequest\voluntaryRefund;

/**
 * Class TicketDTO
 * @package sales\dto\boRequest\voluntaryRefund
 *
 * @property string $number
 * @property float $airlinePenalty
 * @property float $processingFee
 * @property float $refundable
 */
class TicketDTO
{
    public string $number;
    public float $airlinePenalty;
    public float $processingFee;
    public float $refundable;

    public function __construct(string $number, float $airlinePenalty, float $processingFee, float $refundable)
    {
        $this->number = $number;
        $this->airlinePenalty = $airlinePenalty;
        $this->processingFee = $processingFee;
        $this->refundable = $refundable;
    }
}
