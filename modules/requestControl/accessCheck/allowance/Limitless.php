<?php

namespace modules\requestControl\accessCheck\allowance;

use modules\requestControl\interfaces\AllowanceInterface;
use modules\requestControl\accessCheck\RequestCountLedger;

/**
 * Object that declare behaviour when limit are not determined
 *
 * Class Limitless
 * @package modules\requestControl\accessCheck\allowance
 */
class Limitless implements AllowanceInterface
{
    /**
     * @param RequestCountLedger $registry
     * @return bool
     */
    public function isAllow(RequestCountLedger $registry): bool
    {
        return true;
    }
}
