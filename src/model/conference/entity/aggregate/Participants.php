<?php

namespace src\model\conference\entity\aggregate;

/**
 * Class Participants
 *
 * @property Participant[] $participants
 */
class Participants
{
    private array $participants = [];

    public function add(Participant $participant): void
    {
        if ($this->isExist($participant->getId())) {
            throw new \DomainException('Participant ' . $participant->getId()->getValue() . ' is already exist');
        }

        $this->participants[$participant->getId()->getValue()] = $participant;
    }

    public function count(): int
    {
        return count($this->participants);
    }

    public function isExist(ParticipantId $id): bool
    {
        return isset($this->participants[$id->getValue()]);
    }

    public function get(ParticipantId $id): Participant
    {
        if (!$this->isExist($id)) {
            throw new \DomainException('Not found participant ' . $id->getValue());
        }
        return $this->participants[$id->getValue()];
    }

    public function recalculateTalkDuration(\DateTimeImmutable $date): void
    {
        $countActiveParticipants = $this->getCountActiveParticipants();
        foreach ($this->participants as $participant) {
            if ($countActiveParticipants < 2) {
                if ($participant->isWaitFinishTalk()) {
                    $participant->finishTalk($date);
                }
                continue;
            }
            if ($participant->isWaitStartTalk() && $participant->isActive()) {
                $participant->startTalk($date);
                continue;
            }
            if ($participant->isWaitFinishTalk() && !$participant->isActive()) {
                $participant->finishTalk($date);
            }
        }
    }

    public function getCountActiveParticipants(): int
    {
        $count = 0;
        foreach ($this->participants as $participant) {
            if ($participant->isActive()) {
                $count++;
            }
        }
        return $count;
    }

    public function leave(\DateTimeImmutable $date): void
    {
        foreach ($this->participants as $participant) {
            if (!$participant->isLeave()) {
                $participant->leave($date);
            }
        }
    }

    public function getState(): array
    {
        $participants = [];
        foreach ($this->participants as $participant) {
            $participants[] = $participant->getState();
        }
        return $participants;
    }

    public function getResult(): array
    {
        $result = [];
        foreach ($this->participants as $participant) {
            $result[] = $participant->getResult();
        }
        return $result;
    }
}
