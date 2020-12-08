<?php

namespace sales\model\department\department;

/**
 * Class Type
 *
 * @property string|null $value
 */
class Type
{
    public const LEAD = 'lead';
    public const CASE = 'case';

    private ?string $value;

    public function __construct(?string $value)
    {
        $this->value = $value;
    }

    public function isLead(): bool
    {
        return $this->value === self::LEAD;
    }

    public function isCase(): bool
    {
        return $this->value === self::CASE;
    }

    public function isEmpty(): bool
    {
        return $this->value ? false : true;
    }
}
