<?php

namespace sales\model\leadRedial\queue;

class TestLeadRedialQueue implements LeadRedialQueue
{
    public function getCall(int $userId): ?RedialCall
    {
        return new RedialCall(
            '+14157693509',
            1468,
            '+37369305726',
            2,
            513195
        );
    }
}
