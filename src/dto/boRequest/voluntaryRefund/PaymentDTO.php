<?php

namespace src\dto\boRequest\voluntaryRefund;

/**
 * Class PaymentDTO
 * @package src\dto\boRequest\voluntaryRefund
 */
class PaymentDTO
{
    public string $type;
    public CardDTO $card;

    public function __construct(string $type, CardDTO $card)
    {
        $this->type = $type;
        $this->card = $card;
    }
}
