<?php

namespace src\entities\cases\events;

use src\entities\cases\Cases;

/**
 * Class CasesUpdatedEvent
 *
 * @property Cases $case
 */
class CasesUpdatedEvent
{
    public $case;
    public $username;

    /**
     * @param Cases $case
     */
    public function __construct(Cases $case, $username)
    {
        $this->case = $case;
        $this->username = $username;
    }
}
