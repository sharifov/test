<?php

namespace sales\model\conference\entity\conferenceEventLog;

use sales\model\conference\entity\conferenceEventLog\events\ConferenceEnd;
use sales\model\conference\entity\conferenceEventLog\events\ConferenceStart;
use sales\model\conference\entity\conferenceEventLog\events\Event;
use sales\model\conference\entity\conferenceEventLog\events\ParticipantHold;
use sales\model\conference\entity\conferenceEventLog\events\ParticipantJoin;
use sales\model\conference\entity\conferenceEventLog\events\ParticipantLeave;
use sales\model\conference\entity\conferenceEventLog\events\ParticipantMute;
use sales\model\conference\entity\conferenceEventLog\events\ParticipantUnHold;
use sales\model\conference\entity\conferenceEventLog\events\ParticipantUnMute;

class EventFactory
{
    public static function create(string $eventType, string $json): Event
    {
        if ($eventType === ConferenceEnd::NAME) {
            return ConferenceEnd::fromJson($json);
        }
        if ($eventType === ConferenceStart::NAME) {
            return ConferenceStart::fromJson($json);
        }
        if ($eventType === ParticipantHold::NAME) {
            return ParticipantHold::fromJson($json);
        }
        if ($eventType === ParticipantJoin::NAME) {
            return ParticipantJoin::fromJson($json);
        }
        if ($eventType === ParticipantLeave::NAME) {
            return ParticipantLeave::fromJson($json);
        }
        if ($eventType === ParticipantMute::NAME) {
            return ParticipantMute::fromJson($json);
        }
        if ($eventType === ParticipantUnHold::NAME) {
            return ParticipantUnHold::fromJson($json);
        }
        if ($eventType === ParticipantUnMute::NAME) {
            return ParticipantUnMute::fromJson($json);
        }
        throw new \InvalidArgumentException('Undefined eventType: ' . $eventType);
    }
}
