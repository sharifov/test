<?php

namespace sales\model\conference\useCase\saveParticipantStats;

class Command
{
    public string $conferenceSid;
    public ?int $conferenceId = null;

    public function __construct(string $conferenceSid, ?int $conferenceId)
    {
        $this->conferenceSid = $conferenceSid;
        $this->conferenceId = $conferenceId;
    }
}
