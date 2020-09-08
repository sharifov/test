<?php

namespace sales\model\conference\entity\aggregate;

use sales\model\conference\entity\conferenceEventLog\events\Event;
use sales\model\conference\entity\conferenceEventLog\events\ParticipantJoin;

/**
 * Class ConferenceLogAggregate
 *
 * @property Event[] $events
 * @property Participants $participants
 */
class ConferenceLogAggregate
{
    private array $events;

    private Participants $participants;

    /**
     * @param Event[] $events
     */
    public function __construct(array $events)
    {
        $this->events = $events;
        $this->participants = new Participants();
    }

    public function run(): void
    {
        foreach ($this->events as $event) {
            if ($event->isJoin()) {
                $this->processJoinEvent($event);
                $this->participants->processTalkTimer($event->getTimestamp());
            } elseif ($event->isConferenceEnd()) {
                $this->participants->leave($event->getTimestamp());
                $this->participants->processTalkTimer($event->getTimestamp());
            }
        }
    }

    public function getUsersReport(): array
    {
        return $this->participants->report();
    }

    private function processJoinEvent(ParticipantJoin $event): void
    {
        if ($event->participant_user_id) {
            $participantId = new ParticipantId($event->participant_user_id);
            $participantType = ParticipantType::byUser();
        } else {
            $participantId = new ParticipantId($event->CallSid);
            $participantType = ParticipantType::byDefault();
        }

        if ($this->participants->isExist($participantId)) {
            $participant = $this->participants->get($participantId);
            $participant->join($event->getTimestamp());
            return;
        }

        $participant = new Participant($participantId, ParticipantStatus::byJoin(), $participantType);
        $this->participants->add($participant);
        $participant->join($event->getTimestamp());
    }
}
