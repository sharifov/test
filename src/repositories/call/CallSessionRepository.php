<?php

namespace src\repositories\call;

use common\models\CallSession;
use src\dispatchers\EventDispatcher;
use src\repositories\NotFoundException;

/**
 * Class CallSessionRepository
 * @package src\repositories\call
 */
class CallSessionRepository
{
    private $eventDispatcher;

    /**
     * CallSessionRepository constructor.
     * @param EventDispatcher $eventDispatcher
     */
    public function __construct(EventDispatcher $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param $sid
     * @return CallSession
     */
    public function findBySid($sid): CallSession
    {
        if ($callSession = CallSession::findOne(['cs_cid' => $sid])) {
            return $callSession;
        }
        throw new NotFoundException('CallSession is not found');
    }

    /**
     * @param CallSession $callSession
     * @return int
     */
    public function save(CallSession $callSession): int
    {
        if (!$callSession->save(false)) {
            throw new \RuntimeException('Saving error');
        }
        $this->eventDispatcher->dispatchAll($callSession->releaseEvents());
        return $callSession->cs_id;
    }

    /**
     * @param CallSession $callSession
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function remove(CallSession $callSession): void
    {
        if (!$callSession->delete()) {
            throw new \RuntimeException('Removing error');
        }
        $this->eventDispatcher->dispatchAll($callSession->releaseEvents());
    }
}
