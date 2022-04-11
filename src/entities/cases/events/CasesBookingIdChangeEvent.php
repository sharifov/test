<?php

namespace src\entities\cases\events;

use src\entities\cases\Cases;

/**
 * Class CasesBookingIdChangeEvent
 * @property Cases $cases
 * @property string $username
 */
class CasesBookingIdChangeEvent
{
    public $cases;
    public $username;

    /**
     * CasesBookingIdChangeEvent constructor.
     * @param Cases $cases
     * @param string $username
     */
    public function __construct(Cases $cases, string $username)
    {
        $this->cases  = $cases;
        $this->username = $username;
    }
}
