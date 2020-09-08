<?php

namespace sales\model\conference\entity\aggregate;

class Participant
{
    private ParticipantId $id;

    private ParticipantStatus $status;

    private ParticipantType $type;

    private Durations $duration;
    private Durations $talkDuration;
    private Durations $holdDuration;

    public function __construct(ParticipantId $id, ParticipantStatus $status, ParticipantType $type)
    {
        $this->id = $id;
        $this->type = $type;
        $this->status = $status;
        $this->duration = new Durations();
        $this->talkDuration = new Durations();
        $this->holdDuration = new Durations();
    }

    public function startTalk(\DateTimeImmutable $date): void
    {
        if ($this->isStartedTalk()) {
            throw new \DomainException('Participant ' . $this->getId()->getValue() . ' is already start talk');
        }

        $this->talkDuration->addStart($date);
    }

    public function endTalk(\DateTimeImmutable $date): void
    {
        if (!$this->isStartedTalk()) {
            throw new \DomainException('Participant ' . $this->getId()->getValue() . ' is already ended talk');
        }

        $this->talkDuration->addEnd($date);
    }

    public function isStartedTalk(): bool
    {
        return $this->talkDuration->isStarted();
    }

    public function getId(): ParticipantId
    {
        return $this->id;
    }

    public function join(\DateTimeImmutable $date): void
    {
        $this->status->join();
        $this->duration->addStart($date);
    }

    public function isActive(): bool
    {
        return $this->status->isActive();
    }

    public function leave(\DateTimeImmutable $date): void
    {
        $this->status->leave();
        $this->duration->addEnd($date);
    }

    public function isLeave(): bool
    {
        return $this->status->isLeave();
    }

    public function getTalkDuration(): int
    {
        return $this->talkDuration->getValue();
    }

    public function getHoldDuration(): int
    {
        return $this->holdDuration->getValue();
    }

    public function getDuration(): int
    {
        return $this->duration->getValue();
    }

    public function isUser(): bool
    {
        return $this->type->isUser();
    }
}
