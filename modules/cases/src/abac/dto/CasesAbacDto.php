<?php

namespace modules\cases\src\abac\dto;

use sales\auth\Auth;
use sales\entities\cases\Cases;

class CasesAbacDto extends \stdClass
{
    public int $owner;

    public function __construct(?Cases $case)
    {
        if ($case) {
            $this->owner = (int)$case->isOwner(Auth::id());
        }
    }
}
