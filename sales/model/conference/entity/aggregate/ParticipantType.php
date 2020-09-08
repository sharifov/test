<?php

namespace sales\model\conference\entity\aggregate;

class ParticipantType
{
    public const USER = 'user';
    public const DEFAULT = 'default';

    private string $value;

    private function __construct(string $value)
    {
        $this->value = $value;
    }

    public static function byUser(): self
    {
        return new self(self::USER);
    }

    public static function byDefault(): self
    {
        return new self(self::DEFAULT);
    }

    public function isUser(): bool
    {
        return $this->value === self::USER;
    }

    public function isDefault(): bool
    {
        return $this->value === self::DEFAULT;
    }
}
