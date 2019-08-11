<?php

namespace sales\entities\cases\events;

use sales\entities\cases\Cases;

/**
 * Class CasesOwnerFreedEvent
 * @property Cases $cases
 * @property int|null $oldOwner
 */
class CasesOwnerFreedEvent
{

    public $cases;
    public $oldOwner;

    /**
     * CasesOwnerFreedEvent constructor.
     * @param Cases $cases
     * @param int|null $oldOwner
     */
    public function __construct(Cases $cases, ?int $oldOwner)
    {
        $this->cases = $cases;
        $this->oldOwner = $oldOwner;
    }
}