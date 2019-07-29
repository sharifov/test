<?php

namespace sales\repositories\call;

use common\models\CallSession;
use sales\dispatchers\EventDispatcher;
use sales\repositories\NotFoundException;
use sales\repositories\Repository;

/**
 * Class CallSessionRepository
 * @package sales\repositories\call
 * @method null|CallSession getBySid($sid)
 */
class CallSessionRepository extends Repository
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