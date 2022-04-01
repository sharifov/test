<?php
/**
 * User: shakarim
 * Date: 4/1/22
 * Time: 5:53 PM
 */

namespace modules\requestControl\accessCheck\allowance;

use modules\requestControl\interfaces\AllowanceInterface;
use modules\requestControl\accessCheck\RequestCountLedger;

class Limited implements AllowanceInterface
{
    private $local = 0;
    private $global = 0;

    /**
     * AllowLimits constructor.
     * @param array $items
     */
    public function __construct($items = [])
    {
        $this->local = self::reduce_to_lower($items, 'local');
        $this->global = self::reduce_to_lower($items, 'global');
    }

    /**
     *
     * 1. Firstly check local data, if local request can be allowed -> next step;
     * 2. Check global data, if globally request count can be allowed -> next step;
     * 3. Return `true`;
     *
     * @param RequestCountLedger $registry
     * @return bool
     */
    public function isAllow(RequestCountLedger $registry): bool
    {
        if ($this->local > 0 && $this->local < $registry->getLocal())
            return false;
        if ($this->global > 0 && $this->global < $registry->getGlobal())
            return false;
        return true;
    }


    /**
     * Find lowest value by key in items of received list.
     *
     * @param array $items
     * @param string $key
     * @return mixed
     */
    private static function reduce_to_lower(array $items, string $key)
    {
        return array_reduce(
            $items,
            function($acc, $item) use ($key) {
                return (gettype($acc) === 'NULL' || (int)$acc > $item[$key]) ? $item[$key] : $acc;
            },
            null
        );
    }
}