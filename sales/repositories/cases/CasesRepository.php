<?php

namespace sales\repositories\cases;

use sales\dispatchers\EventDispatcher;
use sales\entities\cases\Cases;
use sales\repositories\NotFoundException;
use sales\repositories\Repository;

/**
 * Class CasesRepository
 *
 * @property EventDispatcher $eventDispatcher
 * @method null|Cases get(int $id)
 * @method null|Cases getByClient(int $clientId)
 * @method null|Cases getByClientProjectDepartment(int $clientId, int $projectId, ?int $departmentId)
 * @method null|Cases getByGid(string $gid)
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
     * @param int $clientId
     * @return Cases
     */
    public function findByClient(int $clientId): Cases
    {
        if ($case = Cases::find()
            ->andWhere(['cs_client_id' => $clientId])
            ->andWhere(['<>', Cases::STATUS_TRASH, 'cs_status'])
            ->andWhere(['<>', Cases::STATUS_SOLVED, 'cs_status'])
            ->orderBy(['cs_id' => SORT_DESC])
            ->limit(1)->one()) {
            return $case;
        }
        throw new NotFoundException('Case is not found');
    }


    /**
     * @param int $clientId
     * @param int $projectId
     * @param int|null $departmentId
     * @return Cases
     */
    public function findByClientProjectDepartment(int $clientId, int $projectId, ?int $departmentId): Cases
    {
        if ($case = Cases::find()
            ->andWhere(['cs_client_id' => $clientId, 'cs_project_id' => $projectId, 'cs_dep_id' => $departmentId])
            ->andWhere(['NOT IN', 'cs_status', [Cases::STATUS_TRASH, Cases::STATUS_SOLVED]])
            ->orderBy(['cs_id' => SORT_DESC])
            ->limit(1)->one()) {
            return $case;
        }
        throw new NotFoundException('Case is not found');
    }

    /**
     * @param string $gid
     * @return Cases
     */
    public function findByGid(string $gid): Cases
    {
        if ($case = Cases::find()->andWhere(['cs_gid' => $gid])->limit(1)->one()) {
            return $case;
        }
        throw new NotFoundException('Case is not found');
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
        $case->cs_updated_dt = date('Y-m-d H:i:s');
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
