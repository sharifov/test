<?php

namespace src\repositories\cases;

use src\dispatchers\EventDispatcher;
use src\entities\cases\Cases;
use src\entities\cases\CasesStatus;
use src\model\cases\CaseCodeException;
use src\repositories\NotFoundException;

/**
 * Class CasesRepository
 *
 * @property EventDispatcher $eventDispatcher
 */
class CasesRepository
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
        if (
            $case = Cases::find()
            ->andWhere(['cs_client_id' => $clientId])
            ->andWhere(['<>', CasesStatus::STATUS_TRASH, 'cs_status'])
            ->andWhere(['<>', CasesStatus::STATUS_SOLVED, 'cs_status'])
            ->orderBy(['cs_id' => SORT_DESC])
            ->limit(1)->one()
        ) {
            return $case;
        }
        throw new NotFoundException('Case is not found');
    }

    public function getByClient(int $clientId): ?Cases
    {
        try {
            return $this->findByClient($clientId);
        } catch (NotFoundException $e) {
            return null;
        }
    }

    /**
     * @param int $clientId
     * @return Cases
     */
    public function findByClientWithAnyStatus(int $clientId): Cases
    {
        if (
            $case = Cases::find()
            ->andWhere(['cs_client_id' => $clientId])
            ->orderBy(['cs_id' => SORT_DESC])
            ->limit(1)->one()
        ) {
            return $case;
        }
        throw new NotFoundException('Case is not found');
    }

    public function getByClientWithAnyStatus(int $clientId): ?Cases
    {
        try {
            return $this->findByClientWithAnyStatus($clientId);
        } catch (NotFoundException $e) {
            return null;
        }
    }

    /**
     * @param int $clientId
     * @param int $projectId
     * @param int|null $departmentId
     * @return Cases
     */
    public function findByClientProjectDepartment(int $clientId, int $projectId, ?int $departmentId): Cases
    {
        if (
            $case = Cases::find()
            ->andWhere(['cs_client_id' => $clientId, 'cs_project_id' => $projectId, 'cs_dep_id' => $departmentId])
            ->andWhere(['NOT IN', 'cs_status', [CasesStatus::STATUS_TRASH, CasesStatus::STATUS_SOLVED]])
            ->orderBy(['cs_id' => SORT_DESC])
            ->limit(1)->one()
        ) {
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
     * @param string $gid
     * @return Cases
     */
    public function findFreeByGid(string $gid): Cases
    {
        if ($case = Cases::find()->andWhere(['cs_gid' => $gid])->limit(1)->one()) {
            if ($case->lead) {
                throw new \DomainException('Case is already assigned to Lead');
            }
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
        throw new NotFoundException('Case is not found', CaseCodeException::CASE_NOT_FOUND);
    }

    /**
     * @param Cases $case
     * @return int
     */
    public function save(Cases $case): int
    {
        $now = date('Y-m-d H:i:s');
        $case->cs_updated_dt = $now;
        $case->cs_last_action_dt = $now;
        if (!$case->save(false)) {
            throw new \RuntimeException('Case saving error', CaseCodeException::CASE_SAVE);
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
            throw new \RuntimeException('Removing error', CaseCodeException::CASE_REMOVE);
        }
        $this->eventDispatcher->dispatchAll($case->releaseEvents());
    }

    /**
     * @param string $phone
     * @return array
     */
    public function findOpenCasesByPhone(string $phone)
    {
        if (
            $cases = Cases::find()
            ->join('join', 'client_phone', 'cs_client_id = client_id and phone = :phone', ['phone' => $phone])
            ->where(['cs_status' => [CasesStatus::STATUS_PENDING, CasesStatus::STATUS_PROCESSING, CasesStatus::STATUS_FOLLOW_UP]])
            ->all()
        ) {
            return $cases;
        }
        throw new NotFoundException('Cases is not found');
    }

    public function getOpenCasesByPhone(string $phone)
    {
        try {
            return $this->findOpenCasesByPhone($phone);
        } catch (NotFoundException $e) {
            return null;
        }
    }
}
