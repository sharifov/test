<?php

namespace sales\model\leadRedial\entity;

use sales\dispatchers\EventDispatcher;
use sales\repositories\NotFoundException;

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

    public function save(CallRedialUserAccess $access): void
    {
        if (!$access->save(false)) {
            throw new \RuntimeException('Saving error.');
        }
        $this->eventDispatcher->dispatchAll($access->releaseEvents());
    }

    public function remove(CallRedialUserAccess $access): void
    {
        if (!$access->remove()) {
            throw new \RuntimeException('Removing error.');
        }
        $this->eventDispatcher->dispatchAll($access->releaseEvents());
    }
}
