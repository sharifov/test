<?php

namespace sales\model\conference\entity\aggregate;

class ParticipantStatus
{
    public const JOIN = 'join';
    public const HOLD = 'hold';
    public const UNHOLD = 'unHold';
    public const LEAVE = 'leave';

    private string $value;

    private function __construct(string $value)
    {
        $this->value = $value;
    }

    public static function byJoin(): self
    {
        return new self(self::JOIN);
    }

    public static function byLeave(): self
    {
        return new self(self::LEAVE);
    }

    public function isActive(): bool
    {
        return $this->value === self::JOIN || $this->value === self::UNHOLD;
    }

    public function join(): void
    {
        $this->value = self::JOIN;
    }

    public function isJoin(): bool
    {
        return $this->value === self::JOIN;
    }

    public function hold(): void
    {
        $this->value = self::HOLD;
    }

    public function unHold(): void
    {
        $this->value = self::UNHOLD;
    }

    public function leave(): void
    {
        $this->value = self::LEAVE;
    }

    public function isLeave(): bool
    {
        return $this->value === self::LEAVE;
    }
}
