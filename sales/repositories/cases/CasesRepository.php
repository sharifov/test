<?php

namespace sales\repositories\cases;

use sales\dispatchers\EventDispatcher;
use sales\entities\cases\Cases;
use sales\repositories\NotFoundException;
use sales\repositories\Repository;

/**
 * Class CasesRepository
 * @property EventDispatcher $eventDispatcher
 * @method null|Cases get(int $id)
 */
class CasesRepository extends Repository
{
    private $eventDispatcher;

    /**
     * CasesRepository constructor.
     * @param EventDispatcher $eventDispatcher
     */
    public function __construct(EventDispatcher $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param int $id
     * @return Cases
     */
    public function find(int $id): Cases
    {
        if ($case = Cases::findOne($id)) {
            return $case;
        }
        throw new NotFoundException('Case is not found');
    }

    /**
     * @param Cases $case
     * @return int
     */
    public function save(Cases $case): int
    {
        if (!$case->save(false)) {
            throw new \RuntimeException('Saving error');
        }
        $this->eventDispatcher->dispatchAll($case->releaseEvents());
        return $case->cs_id;
    }

    /**
     * @param Cases $case
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function remove(Cases $case): void
    {
        if (!$case->delete()) {
            throw new \RuntimeException('Removing error');
        }
        $this->eventDispatcher->dispatchAll($case->releaseEvents());
    }
}
