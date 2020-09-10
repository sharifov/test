<?php

namespace sales\model\conference\entity\aggregate;

use sales\model\conference\entity\aggregate\log\Logs;
use sales\model\conference\entity\conferenceEventLog\events\Event;
use sales\model\conference\entity\conferenceEventLog\events\ParticipantHold;
use sales\model\conference\entity\conferenceEventLog\events\ParticipantJoin;
use sales\model\conference\entity\conferenceEventLog\events\ParticipantLeave;
use sales\model\conference\entity\conferenceEventLog\events\ParticipantUnHold;
use yii\helpers\VarDumper;

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

    public Logs $logs;

    /**
     * @param Event[] $events
     */
    public function __construct(array $events)
    {
        $this->events = $events;
        $this->participants = new Participants();
        $this->logs = new Logs();
    }

    public function run(): void
    {
        $this->logState();

        foreach ($this->events as $event) {

            [$participantId, $participantType] = $this->getParticipantProperties($event);

            if ($event->isConferenceStart() || $event->isConferenceEnd()) {
                $this->logs->add(new \sales\model\conference\entity\aggregate\log\LogEvent($event::NAME, null, $event->raw));
            } else {
                $this->logs->add(new \sales\model\conference\entity\aggregate\log\LogEvent($event::NAME, $participantId->getValue(), $event->raw));
            }

            if ($event->isConferenceStart()) {

            } elseif ($event->isConferenceEnd()) {

                $this->participants->leave($event->getTimestamp());

            } elseif ($event->isParticipantJoin()) {

                $this->processJoinEvent($event, $participantId, $participantType);

            } elseif ($event->isParticipantLeave()) {

                $this->processLeaveEvent($event, $participantId, $participantType);

            } elseif ($event->isParticipantHold()) {

                $this->processHoldEvent($event, $participantId, $participantType);

            } elseif ($event->isParticipantUnHold()) {

                $this->processUnHoldEvent($event, $participantId, $participantType);

            } elseif ($event->isParticipantMute()) {

            } elseif ($event->isParticipantUnMute()) {

            }

            $this->participants->recalculateTalkDuration($event->getTimestamp());

            $this->logState();

        }

        $this->logResult();
    }

    private function processJoinEvent(ParticipantJoin $event, ParticipantId $participantId, ParticipantType $participantType): void
    {
        if ($this->participants->isExist($participantId)) {
            $participant = $this->participants->get($participantId);
            $participant->join($event->getTimestamp());
            return;
        }

        $participant = Participant::byJoin($participantId, $participantType, $event->getTimestamp(), $event->participant_user_id);
        $this->participants->add($participant);
    }

    private function processLeaveEvent(ParticipantLeave $event, ParticipantId $participantId, ParticipantType $participantType): void
    {
       if ($this->participants->isExist($participantId)) {
            $participant = $this->participants->get($participantId);
            $participant->leave($event->getTimestamp());
            return;
        }

        $participant = Participant::byLeave($participantId, $participantType, $event->getTimestamp(), $event->participant_user_id);
        $this->participants->add($participant);
    }

    private function processHoldEvent(ParticipantHold $event, ParticipantId $participantId, ParticipantType $participantType): void
    {
        if ($this->participants->isExist($participantId)) {
            $participant = $this->participants->get($participantId);
            $participant->hold($event->getTimestamp());
            return;
        }

        $participant = Participant::byHold($participantId, $participantType, $event->getTimestamp(), $event->participant_user_id);
        $this->participants->add($participant);
    }

    private function processUnHoldEvent(ParticipantUnHold $event, ParticipantId $participantId, ParticipantType $participantType): void
    {
        if ($this->participants->isExist($participantId)) {
            $participant = $this->participants->get($participantId);
            $participant->unHold($event->getTimestamp());
            return;
        }

        $participant = Participant::byUnHold($participantId, $participantType, $event->getTimestamp(), $event->participant_user_id);
        $this->participants->add($participant);
    }

    private function logState(): void
    {
        $this->logs->add(new \sales\model\conference\entity\aggregate\log\LogParticipants($this->participants->getState()));
    }

    private function logResult(): void
    {
        $this->logs->add(new \sales\model\conference\entity\aggregate\log\LogResult($this->participants->getResult()));
    }

    private function getParticipantProperties($event): array
    {
        $participantId = null;
        $participantType = null;
        if (!$event->isConferenceEnd() && !$event->isConferenceStart()) {
            $participantId = new ParticipantId($event->participant_identity);
            if ($event->participant_user_id) {
                $participantType = ParticipantType::byUser();
            } else {
                $participantType = ParticipantType::byDefault();
            }
        }
        return [$participantId, $participantType];
    }
}
