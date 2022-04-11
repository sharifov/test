<?php

namespace src\entities\cases\events;

use src\entities\cases\Cases;

/**
 * Class CasesBookingIdChangeEvent
 * @property Cases $case
 * @property int $userId
 */
class CasesBookingIdChangeEvent
{
    public $case;
    public $userId;

    /**
     * CasesBookingIdChangeEvent constructor.
     * @param Cases $case
     * @param int $userId
     */
    public function __construct(Cases $case, ?int $userId)
    {
        $this->case  = $case;
        $this->userId = $userId;
    }
}
