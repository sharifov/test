<?php

namespace sales\model\leadRedial\entity;

use sales\dispatchers\EventDispatcher;
use sales\repositories\NotFoundException;
use yii\helpers\VarDumper;

/**
 * Class CallRedialUserAccessRepository
 *
 * @property EventDispatcher $eventDispatcher
 */
class CallRedialUserAccessRepository
{
    private EventDispatcher $eventDispatcher;

    public function __construct(EventDispatcher $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function find(int $leadId, int $userId): CallRedialUserAccess
    {
        $access = CallRedialUserAccess::find()->andWhere(['crua_lead_id' => $leadId, 'crua_user_id' => $userId])->one();
        if ($access) {
            return $access;
        }
        throw new NotFoundException('Call redial access not found. LeadId: ' . $leadId . ' UserId: ' . $userId);
    }

    public function get(int $leadId, int $userId): ?CallRedialUserAccess
    {
        $access = CallRedialUserAccess::find()->andWhere(['crua_lead_id' => $leadId, 'crua_user_id' => $userId])->one();
        if ($access) {
            return $access;
        }
        return null;
    }

    public function save(CallRedialUserAccess $access): void
    {
        if (!$access->save(false)) {
            throw new \RuntimeException('Saving error.');
        }
        $this->eventDispatcher->dispatchAll($access->releaseEvents());
    }

    public function remove(CallRedialUserAccess $access): void
    {
        $result = $access->remove();
        if ($result === 0) {
            return;
        }
        if (!$result) {
            throw new \RuntimeException('Removing error. Result = ' . VarDumper::dumpAsString($result));
        }
        $this->eventDispatcher->dispatchAll($access->releaseEvents());
    }
}
