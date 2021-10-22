<?php

namespace sales\dto\boRequest\voluntaryRefund;

class CardDTO
{
    public string $holderName;
    public string $number;
    public ?string $type = null;
    public string $expirationDate;
    public string $cvv;

    public function __construct(string $holderName, string $number, ?string $type, string $expirationDate, string $cvv)
    {
        $this->holderName = $holderName;
        $this->number = $number;
        $this->type = $type;
        $this->expirationDate = $expirationDate;
        $this->cvv = $cvv;
    }
}
