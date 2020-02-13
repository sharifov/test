<?php

namespace sales\guards\cases;

use sales\entities\cases\Cases;

class CaseTakeGuard
{
    public function guard(Cases $case): void
    {
        if ($case->isSolved()) {
            throw new \DomainException('Case is solved. Take denied.');
        }
    }
}
