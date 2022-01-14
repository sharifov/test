<?php

namespace src\entities\cases\events;

use src\entities\cases\Cases;

/**
 * Class CasesCreatedEvent
 *
 * @property Cases $case
 */
class CasesCreatedEvent
{
    public $case;

    /**
     * @param Cases $case
     */
    public function __construct(Cases $case)
    {
        $this->case = $case;
    }
}
