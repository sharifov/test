<?php

namespace src\model\department\department;

/**
 * Class DefaultPhoneType
 *
 * @property string $value
 */
class DefaultPhoneType
{
    public const ONLY_GENERAL = 'Only general';
    public const ONLY_PERSONAL = 'Only personal';
    public const GENERAL_FIRST = 'General first';
    public const PERSONAL_FIRST = 'Personal first';

    public string $value;

    private function __construct(string $value)
    {
        $this->value = $value;
    }

    public static function createFromString(string $value): self
    {
        return new self($value);
    }

    public static function createOnlyGeneral(): self
    {
        return self::createFromString(self::ONLY_GENERAL);
    }

    public static function createOnlyPersonal(): self
    {
        return self::createFromString(self::ONLY_PERSONAL);
    }

    public static function createGeneralFirst(): self
    {
        return self::createFromString(self::GENERAL_FIRST);
    }

    public static function createPersonalFirst(): self
    {
        return self::createFromString(self::PERSONAL_FIRST);
    }

    public function isOnlyGeneral(): bool
    {
        return $this->value === self::ONLY_GENERAL;
    }

    public function isOnlyPersonal(): bool
    {
        return $this->value === self::ONLY_PERSONAL;
    }

    public function isGeneralFirst(): bool
    {
        return $this->value === self::GENERAL_FIRST;
    }

    public function isPersonalFirst(): bool
    {
        return $this->value === self::PERSONAL_FIRST;
    }
}
