<?php

namespace sales\model\leadRedial\services;

class NullLeadRedialQueue implements LeadRedialQueue
{
    public function getCall(int $userId): ?RedialCall
    {
        return null;
    }
}
