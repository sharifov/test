<?php

namespace sales\model\conference\entity\aggregate\log;

class LogEvent implements Log
{
    public $type;
    public $participantId;
    public $raw;

    public function __construct(string $type, ?string $participantId, array $raw)
    {
        $this->type = $type;
        $this->participantId = $participantId;
        $this->raw = $raw;
    }

    public function isEvent(): bool
    {
        return true;
    }

    public function isParticipants(): bool
    {
        return false;
    }

    public function isResult(): bool
    {
        return false;
    }
}
