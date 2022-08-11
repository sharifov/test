<?php

namespace src\entities\cases\events;

use src\entities\cases\Cases;

/**
 * Class CasesMultipleChangeStatusProcessingEvent
 * @property Cases $cases
 * @property int|null $oldOwner
 * @property int $newOwner
 */
class CasesMultipleChangeStatusProcessingEvent
{
    public $cases;
    public $oldOwner;
    public $newOwner;
    public $creatorId;

    /**
     * CasesMultipleChangeStatusProcessingEvent constructor.
     * @param Cases $cases
     * @param int|null $oldOwner
     * @param int $newOwner
     * @param int $creatorId
     */
    public function __construct(Cases $cases, ?int $oldOwner, int $newOwner, int $creatorId)
    {
        $this->cases  = $cases;
        $this->oldOwner = $oldOwner;
        $this->newOwner = $newOwner;
        $this->creatorId = $creatorId;
    }
}
