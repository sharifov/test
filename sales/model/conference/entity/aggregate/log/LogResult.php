<?php

namespace sales\model\conference\entity\aggregate\log;

class LogResult implements Log
{
    public $participants;

    public function __construct(array $participants)
    {
        $this->participants = $participants;
    }

    public function isEvent(): bool
    {
        return false;
    }

    public function isParticipants(): bool
    {
        return false;
    }

    public function isResult(): bool
    {
        return true;
    }
}
