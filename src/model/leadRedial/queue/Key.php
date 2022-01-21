<?php

namespace src\model\leadRedial\queue;

/**
 * Class Key
 *
 * @property string $value
 */
class Key
{
    private const PREFIX = 'lead_redial_reservation_';

    private string $value;

    public function __construct(int $leadId)
    {
        $this->value = self::PREFIX . $leadId;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
