<?php

namespace sales\model\call\services\reserve;

/**
 * Class Key
 *
 * @property string $value
 */
class Key
{
    private const KEY_PREFIX = 'accept_call_';

    private string $value;

    public function __construct(int $callId)
    {
        $this->value = self::KEY_PREFIX . $callId;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
