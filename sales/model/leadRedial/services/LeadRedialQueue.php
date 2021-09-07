<?php

namespace sales\model\leadRedial\services;

interface LeadRedialQueue
{
    public function getCall(int $userId): ?RedialCall;
}
