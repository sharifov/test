<?php

namespace sales\repositories\lead;

use common\models\Reason;
use sales\dispatchers\EventDispatcher;
use sales\repositories\NotFoundException;

class LeadReasonRepository
{
    private $eventDispatcher;

    public function __construct(EventDispatcher $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param $id
     * @return Reason
     */
    public function get($id): Reason
    {
        if ($reason = Reason::findOne($id)) {
            return $reason;
        }
        throw new NotFoundException('Reason is not found');
    }

    /**
     * @param Reason $reason
     * @return int
     */
    public function save(Reason $reason): int
    {
        if (!$reason->save(false)) {
            throw new \RuntimeException('Saving error');
        }
        $this->eventDispatcher->dispatchAll($reason->releaseEvents());
        return $reason->id;
    }

    /**
     * @param Reason $reason
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function remove(Reason $reason): void
    {
        if (!$reason->delete()) {
            throw new \RuntimeException('Removing error');
        }
        $this->eventDispatcher->dispatchAll($reason->releaseEvents());
    }
}