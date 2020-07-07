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

    public string $lastActiveQueue;

    public function __construct(array $hold, array $incoming, array $outgoing, array $active, array $conference)
    {
        $this->hold = $hold;
        $this->incoming = $incoming;
        $this->outgoing = $outgoing;
        $this->active = $active;
        $this->conference = $conference;
    }

    public function isEmpty(): bool
    {
        return !$this->incoming && !$this->outgoing && !$this->active && !$this->hold;
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
}
