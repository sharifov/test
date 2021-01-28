<?php

namespace sales\model\call\services\currentQueueCalls;

/**
 * Class QueueCalls
 *
 * @property IncomingQueueCall[] $hold
 * @property IncomingQueueCall[] $incoming
 * @property OutgoingQueueCall[] $outgoing
 * @property ActiveQueueCall[] $active
 * @property ActiveConference[] $conference
 * @property PriorityQueueCall[] $priority
 * @property string $lastActiveQueue
 */
class QueueCalls
{
    public const LAST_ACTIVE_INCOMING = 'incoming';
    public const LAST_ACTIVE_OUTGOING = 'outgoing';
    public const LAST_ACTIVE_ACTIVE = 'active';

    public array $hold;
    public array $incoming;
    public array $outgoing;
    public array $active;
    public array $conference;
    public array $priority;

    public string $lastActiveQueue;

    public function __construct(
        array $hold,
        array $incoming,
        array $outgoing,
        array $active,
        array $conference,
        array $priority
    ) {
        $this->hold = $hold;
        $this->incoming = $incoming;
        $this->outgoing = $outgoing;
        $this->active = $active;
        $this->conference = $conference;
        $this->priority = $priority;
    }

    public function isEmpty(): bool
    {
        return !$this->incoming && !$this->outgoing && !$this->active && !$this->hold && !$this->priority;
    }

    public function isLastIncoming(): bool
    {
        return $this->lastActiveQueue === self::LAST_ACTIVE_INCOMING;
    }

    public function isLastOutgoing(): bool
    {
        return $this->lastActiveQueue === self::LAST_ACTIVE_OUTGOING;
    }

    public function isLastActive(): bool
    {
        return $this->lastActiveQueue === self::LAST_ACTIVE_ACTIVE;
    }

    public function toArray(): array
    {
        if ($this->isEmpty()) {
            return [
                'isEmpty' => true
            ];
        }

        $hold = [];
        foreach ($this->hold as $item) {
            $hold[] = $item->getData();
        }

        $incoming = [];
        foreach ($this->incoming as $item) {
            $incoming[] = $item->getData();
        }

        $outgoing = [];
        foreach ($this->outgoing as $item) {
            $outgoing[] = $item->getData();
        }

        $active = [];
        foreach ($this->active as $item) {
            $active[] = $item->getData();
        }

        $conferences = [];
        foreach ($this->conference as $item) {
            $conferences[] = $item->getData();
        }

        $priority = [];
        foreach ($this->priority as $item) {
            $priority[] = $item->getData();
        }

        return [
            'isEmpty' => false,
            'hold' => $hold,
            'incoming' => $incoming,
            'outgoing' => $outgoing,
            'active' => $active,
            'conferences' => $conferences,
            'priority' => $priority,
            'lastActive' => $this->lastActiveQueue
        ];
    }
}
