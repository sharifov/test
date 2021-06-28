<?php

namespace modules\cases\src\abac\dto;

use sales\auth\Auth;
use sales\entities\cases\Cases;

/**
 * @property bool $is_owner
 */
class CasesAbacDto extends \stdClass
{
    public bool $is_owner;

    public function __construct(?Cases $case)
    {
        if ($case) {
            $this->is_owner = $case->isOwner(Auth::id());
        }
    }
}
