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

    private function __construct(ParticipantId $id, ParticipantType $type)
    {
        $this->id = $id;
        $this->type = $type;
        $this->status = ParticipantStatus::init();
        $this->duration = new Durations();
        $this->talkDuration = new Durations();
        $this->holdDuration = new Durations();
    }

    public static function byJoin(ParticipantId $id, ParticipantType $type, \DateTimeImmutable $date): self
    {
        $participant = new self($id, $type);
        $participant->join($date);
        return $participant;
    }

    public static function byLeave(ParticipantId $id, ParticipantType $type, \DateTimeImmutable $date): self
    {
        $participant = new self($id, $type);
        $participant->leave($date);
        return $participant;
    }

    public static function byHold(ParticipantId $id, ParticipantType $type, \DateTimeImmutable $date): self
    {
        $participant = new self($id, $type);
        $participant->hold($date);
        return $participant;
    }

    public static function byUnHold(ParticipantId $id, ParticipantType $type, \DateTimeImmutable $date): self
    {
        $participant = new self($id, $type);
        $participant->unHold($date);
        return $participant;
    }

    public function startTalk(\DateTimeImmutable $date): void
    {
        if (!$this->talkDuration->isEmpty() && $this->talkDuration->currentIsActive()) {
            throw new \DomainException('Participant ' . $this->getId()->getValue() . ' is already start talk.');
        }

        $this->talkDuration->addStart($date);
    }

    public function finishTalk(\DateTimeImmutable $date): void
    {
        $this->talkDuration->addEnd($date);
    }

    public function isWaitFinishTalk(): bool
    {
        return !$this->talkDuration->isEmpty() && $this->talkDuration->currentIsActive();
    }

    public function isWaitStartTalk(): bool
    {
        return $this->talkDuration->isEmpty() || !$this->talkDuration->currentIsActive();
    }

    public function hold(\DateTimeImmutable $date): void
    {
        if ($this->status->isHold()) {
            throw new \DomainException('Participant is already hold.');
        }
        $this->status->hold($date);
        $this->holdDuration->addStart($date);
    }

    public function unHold(\DateTimeImmutable $date): void
    {
        if ($this->status->isUnHold()) {
            throw new \DomainException('Participant is already unHold.');
        }
        $this->status->unHold($date);
        $this->holdDuration->addEnd($date);
    }

    public function isWaitFinishHoldDuration(): bool
    {
        return !$this->holdDuration->isEmpty() && $this->holdDuration->currentIsActive();
    }

    public function getId(): ParticipantId
    {
        return $this->id;
    }

    public function join(\DateTimeImmutable $date): void
    {
        $this->status->join($date);
        $this->duration->addStart($date);
    }

    public function isActive(): bool
    {
        return $this->status->isActive();
    }

    public function leave(\DateTimeImmutable $date): void
    {
        $this->status->leave($date);
        $this->duration->addEnd($date);
        if ($this->isWaitFinishTalk()) {
            $this->finishTalk($date);
        }
        if ($this->isWaitFinishHoldDuration()) {
            $this->holdDuration->addEnd($date);
        }
    }

    public function isLeave(): bool
    {
        return $this->status->isLeave();
    }

    public function getTalkDuration(): Durations
    {
        return $this->talkDuration;
    }

    public function getHoldDuration(): Durations
    {
        return $this->holdDuration;
    }

    public function getDuration(): Durations
    {
        return $this->duration;
    }

    public function isUser(): bool
    {
        return $this->type->isUser();
    }

    public function getState(): array
    {
        return [
            'id' => $this->id->getValue(),
            'status' => $this->status->getHistory(),
            'type' => $this->type->getValue(),
            'duration' => $this->duration->getState(),
            'talkDuration' => $this->talkDuration->getState(),
            'holdDuration' => $this->holdDuration->getState(),
        ];
    }

    public function getResult(): array
    {
        return [
            'id' => $this->id->getValue(),
            'status' => $this->status->getHistory(),
            'type' => $this->type->getValue(),
            'duration' => $this->getDuration()->getResult(),
            'talkDuration' => $this->getTalkDuration()->getResult(),
            'holdDuration' => $this->getHoldDuration()->getResult(),
        ];
    }
}
