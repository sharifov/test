<?php

namespace src\entities\cases\events;

use src\entities\cases\Cases;

/**
 * Class CasesOwnerChangeEvent
 * @property Cases $cases
 * @property int|null $oldOwner
 * @property int $newOwner
 */
class CasesOwnerChangeEvent
{
    public $cases;
    public $oldOwner;
    public $newOwner;
    public $creatorId;

    /**
     * CasesOwnerChangeEvent constructor.
     * @param Cases $cases
     * @param int|null $oldOwner
     * @param int $newOwner
     * @param int $creatorId
     */
    public function __construct(Cases $cases, ?int $oldOwner, int $newOwner, ?int $creatorId)
    {
        $this->cases  = $cases;
        $this->oldOwner = $oldOwner;
        $this->newOwner = $newOwner;
        $this->creatorId = $creatorId;
    }
}
