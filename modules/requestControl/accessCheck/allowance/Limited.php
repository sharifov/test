<?php

namespace modules\requestControl\accessCheck\allowance;

use modules\requestControl\interfaces\AllowanceInterface;
use modules\requestControl\accessCheck\RequestCountLedger;

/**
 * Class Limited
 * @package modules\requestControl\accessCheck\allowance
 */
class Limited implements AllowanceInterface
{
    private $local;
    private $global;

    /**
     * AllowLimits constructor.
     * @param array $items
     */
    public function __construct($items = [])
    {
        $this->local = self::reduceToLower($items, 'rcr_local');
        $this->global = self::reduceToLower($items, 'rcr_global');
    }

    /**
     * 1. Firstly check local data, if local request can be allowed -> next step;
     * 2. Check global data, if globally request count can be allowed -> next step;
     * 3. Return `true`;
     * @param RequestCountLedger $registry
     * @return bool
     */
    public function isAllow(RequestCountLedger $registry): bool
    {
        if ($this->local > 0 && $this->local <= $registry->getLocal()) {
            return false;
        }
        if ($this->global > 0 && $this->global <= $registry->getGlobal()) {
            return false;
        }
        return true;
    }


    /**
     * Find lowest value by key in items of received list.
     * @param array $items
     * @param string $key
     * @return int
     */
    private static function reduceToLower(array $items, string $key): int
    {
        $mapped_values = array_map(function ($item) use ($key) {
            return (int)$item[$key];
        }, $items);
        sort($mapped_values);
        return ($mapped_values[0]) ? $mapped_values[0] : 0;
    }
}
