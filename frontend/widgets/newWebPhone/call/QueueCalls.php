<?php

namespace frontend\widgets\newWebPhone\call;

/**
 * Class QueueCalls
 *
 * @property IncomingQueueCall[] $incoming
 * @property OutgoingQueueCall[] $outgoing
 * @property ActiveQueueCall[] $active
 */
class QueueCalls
{
    public const LAST_ACTIVE_INCOMING = 'incoming';
    public const LAST_ACTIVE_OUTGOING = 'outgoing';
    public const LAST_ACTIVE_ACTIVE = 'active';

    public array $incoming;
    public array $outgoing;
    public array $active;

    public string $lastActiveQueue;

    public function __construct(array $incoming, array $outgoing, array $active)
    {
        $this->incoming = $incoming;
        $this->outgoing = $outgoing;
        $this->active = $active;
    }

    public function isActive(): bool
    {
        return $this->incoming || $this->outgoing || $this->active;
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
