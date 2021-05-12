<?php

namespace sales\model\call\services\reserve;

/**
 * Class Key
 *
 * @property string $value
 */
class Key
{
    private const PREFIX_ACCEPT_CALL = 'accept_call_';
    private const PREFIX_WARM_TRANSFER = 'warm_transfer_';

    private string $value;

    private function __construct(string $value)
    {
        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public static function byAcceptCall(int $callId): self
    {
        return new self(self::PREFIX_ACCEPT_CALL . $callId);
    }

    public static function byWarmTransfer(int $callId): self
    {
        return new self(self::PREFIX_WARM_TRANSFER . $callId);
    }
}
