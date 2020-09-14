<?php

namespace sales\model\conference\entity\aggregate;

class ParticipantId
{
    private string $value;

    public function __construct($value)
    {
        if (!$value) {
            throw new \DomainException('Id cannot be blank');
        }
        $this->value = (string)$value;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
