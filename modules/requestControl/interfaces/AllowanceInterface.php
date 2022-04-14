<?php

namespace modules\requestControl\interfaces;

use modules\requestControl\accessCheck\RequestCountLedger;

/**
 * Interface AllowanceInterface
 * @package modules\requestControl\interfaces
 */
interface AllowanceInterface
{
    public function isAllow(RequestCountLedger $registry): bool;
}
