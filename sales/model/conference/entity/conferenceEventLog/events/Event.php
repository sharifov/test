<?php

namespace sales\model\conference\entity\conferenceEventLog\events;

interface Event
{
    public function isJoin(): bool;
    public function isConferenceEnd(): bool;
    public function getTimestamp(): \DateTimeImmutable;
}
