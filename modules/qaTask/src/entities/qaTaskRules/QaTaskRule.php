<?php

namespace modules\qaTask\src\entities\qaTaskRules;

/**
 * Class QaTaskRule
 *
 * @property bool $enabled
 * @property $value
 */
class QaTaskRule
{
    private $enabled;
    private $value;

    public function __construct(bool $enabled, $value)
    {
        $this->enabled = $enabled;
        $this->value = $value;
    }

    public function isEnabled(): bool
    {
        return $this->enabled === true;
    }

    public function getValue()
    {
        return $this->value;
    }
}
