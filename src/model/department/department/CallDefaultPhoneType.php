<?php

namespace src\model\department\department;

/**
 * Class CallDefaultPhoneType
 *
 * @property string $value
 */
class CallDefaultPhoneType
{
    public const GENERAL = 'general';
    public const PERSONAL = 'personal';

    public string $value;

    private function __construct(string $value)
    {
        $this->value = $value;
    }

    public static function createFromString(string $value): self
    {
        if ($value === self::GENERAL) {
            return self::createGeneral();
        }
        return self::createPersonal();
    }

    public static function createGeneral(): self
    {
        return new self(self::GENERAL);
    }

    public static function createPersonal(): self
    {
        return new self(self::PERSONAL);
    }

    public function isGeneral(): bool
    {
        return $this->value === self::GENERAL;
    }

    public function isPersonal(): bool
    {
        return $this->value === self::PERSONAL;
    }
}
