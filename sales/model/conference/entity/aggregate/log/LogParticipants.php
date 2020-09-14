<?php

namespace sales\model\conference\entity\aggregate\log;

class LogParticipants implements Log
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
        return true;
    }

    public function isResult(): bool
    {
        return false;
    }
}
