<?php

namespace sales\model\department\department;

class DefaultPhoneType
{
    public const ONLY_GENERAL = 'Only general';
    public const ONLY_PERSONAL = 'Only personal';
    public const GENERAL_FIRST = 'General first';
    public const PERSONAL_FIRST = 'Personal first';

    public string $value;

    public function __construct(string $value)
    {
        $this->value = $value;
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
