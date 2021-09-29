<?php

namespace sales\model\leadRedial\queue;

class NullLeadRedialQueue implements LeadRedialQueue
{
    public function getCall(int $userId): ?RedialCall
    {
        return null;
    }
}
