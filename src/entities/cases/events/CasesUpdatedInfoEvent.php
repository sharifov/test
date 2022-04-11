<?php

namespace src\entities\cases\events;

use src\entities\cases\Cases;

/**
 * Class CasesUpdatedInfoEvent
 *
 * @property Cases $case
 */
class CasesUpdatedInfoEvent
{
    public $case;
    public $userId;

    /**
     * @param Cases $case
     */
    public function __construct(Cases $case, $userId)
    {
        $this->case = $case;
        $this->userId = $userId;
    }
}
