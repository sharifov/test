<?php

namespace src\model\leadRedial\assign;

use src\dispatchers\EventDispatcher;
use src\model\leadRedial\entity\CallRedialUserAccess;

/**
 * Class LeadRedialAssigner
 *
 * @property EventDispatcher $eventDispatcher
 */
class LeadRedialAssigner
{
    private EventDispatcher $eventDispatcher;

    public function __construct(EventDispatcher $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function assign(int $leadId, int $userId, \DateTimeImmutable $createdDt): void
    {
        $access = CallRedialUserAccess::create($leadId, $userId, $createdDt);

        CallRedialUserAccess::getDb()->createCommand(
            "insert into " . CallRedialUserAccess::tableName() . " (`crua_lead_id`, `crua_user_id`, `crua_created_dt`) values (:value, :value2, :value3)",
            [
                ':value' => $access->crua_lead_id,
                ':value2' => $access->crua_user_id,
                ':value3' => $access->crua_created_dt,
            ]
        )->execute();

        $access->setIsNewRecord(false);
        $this->eventDispatcher->dispatchAll($access->releaseEvents());
    }
}
