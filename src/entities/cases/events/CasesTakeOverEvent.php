<?php

namespace src\entities\cases\events;

use src\entities\cases\Cases;

/**
 * Class CasesTakeOverEvent
 * @property Cases $cases
 * @property int|null $oldOwner
 * @property int $newOwner
 */
class CasesTakeOverEvent
{
    public $cases;
    public $oldOwner;
    public $newOwner;

    /**
     * CasesTakeOverEvent constructor.
     * @param Cases $cases
     * @param int|null $oldOwner
     * @param int $newOwner
     */
    public function __construct(Cases $cases, int $oldOwner, int $newOwner)
    {
        $this->cases  = $cases;
        $this->oldOwner = $oldOwner;
        $this->newOwner = $newOwner;
    }
}
