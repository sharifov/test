<?php

namespace sales\repositories\call;

use common\models\CallSession;
use sales\dispatchers\EventDispatcher;

class CallSessionRepository
{
    private $eventDispatcher;

    public function __construct(EventDispatcher $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param string $sid
     * @return CallSession|null
     */
    public function getBySid(string $sid): ?CallSession
    {
        return CallSession::find()->andWhere(['cs_cid' => $sid])->limit(1)->one();
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