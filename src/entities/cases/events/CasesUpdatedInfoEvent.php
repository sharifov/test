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
     * @param int $userId
     */
    public function __construct(Cases $case, ?int $userId)
    {
        $this->case = $case;
        $this->userId = $userId;
    }
}
