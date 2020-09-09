<?php

namespace sales\model\conference\entity\conferenceEventLog\events;

interface Event
{
    public function isParticipantJoin(): bool;
    public function isConferenceStart(): bool;
    public function isConferenceEnd(): bool;
    public function isParticipantLeave(): bool;
    public function isParticipantHold(): bool;
    public function isParticipantUnHold(): bool;
    public function isParticipantMute(): bool;
    public function isParticipantUnMute(): bool;
    public function getTimestamp(): \DateTimeImmutable;
}
