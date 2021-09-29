<?php

namespace sales\model\leadRedial\queue;

interface LeadRedialQueue
{
    public function getCall(int $userId): ?RedialCall;
}
