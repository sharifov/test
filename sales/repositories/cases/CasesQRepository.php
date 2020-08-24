<?php

namespace sales\repositories\cases;

use common\models\CaseSale;
use common\models\Employee;
use common\models\query\CaseSaleQuery;
use common\models\UserGroupAssign;
use sales\access\EmployeeDepartmentAccess;
use sales\access\EmployeeGroupAccess;
use sales\access\EmployeeProjectAccess;
use sales\entities\cases\Cases;
use sales\entities\cases\CasesQSearch;
use sales\entities\cases\CasesStatus;
use yii\db\ActiveQuery;
use yii\db\Expression;

class CasesQRepository
{

    /**
     * @param Employee $user
     * @return int
     */
    public function getPendingCount(Employee $user): int
    {
        return $this->getPendingQuery($user)->count();
    }

    /**
     * @param Employee $user
     * @return ActiveQuery
     */
    public function getPendingQuery(Employee $user): ActiveQuery
    {
        $query = CasesQSearch::find()->andWhere(['cs_status' => CasesStatus::STATUS_PENDING]);

        if ($user->isAdmin()) {
            return $query;
        }

        return $query->andWhere('0 = 1');
    }

    /**
     * @param Employee $user
     * @return int
     */
    public function getInboxCount(Employee $user): int
    {
        return $this->getInboxQuery($user)->count();
    }

    /**
     * @param Employee $user
     * @return ActiveQuery
     */
    public function getInboxQuery(Employee $user): ActiveQuery
    {
        $query = CasesQSearch::find()->andWhere(['cs_status' => CasesStatus::STATUS_PENDING]);

        if ($user->isAdmin()) {
            return $query;
        }

        $conditions = [];

        if ($user->isSupAgent() || $user->isExAgent()) {
            $conditions = $this->freeCase();
        }

        $query->andWhere($this->createSubQuery($user->id, $conditions, $checkDepPermission = false));

        return $query;
    }

    /**
     * @param Employee $user
     * @return int
     */
    public function getNeedActionCount(Employee $user): int
    {
        return $this->getNeedActionQuery($user)->count();
    }

    public function getNeedActionQuery(Employee $user): ActiveQuery
    {
        $query = CasesQSearch::find()->andWhere(['cs_need_action' => true])->andWhere(['<>', 'cs_status', CasesStatus::STATUS_PENDING]);

        if ($user->isAdmin()) {
            return $query;
        }

        $condition = [
            'OR',
            [
                'AND',
                ['cs_status' => CasesStatus::STATUS_PROCESSING],
                ['cs_user_id' => $user->id],
            ],
            ['cs_status' => CasesStatus::STATUS_FOLLOW_UP],
            ['cs_status' => CasesStatus::STATUS_TRASH],
            ['cs_status' => CasesStatus::STATUS_SOLVED],
        ];

        $query->andWhere($this->createSubQuery($user->id, $condition, $checkDepPermission = false));

        return $query;
    }

    /**
     * @param Employee $user
     * @return ActiveQuery
     */
    public function getNeedActionQueryNew(Employee $user): ActiveQuery /* TODO::  */
    {
        $query = CasesQSearch::find()
            ->andWhere(['cs_need_action' => true])
            ->andWhere(['<>', 'cs_status', CasesStatus::STATUS_PENDING]);
        $timeLeftSelect = [
            'time_left' => new Expression('if ((cs_deadline_dt IS NOT NULL), cs_deadline_dt, \'2100-01-01 00:00:00\')')
        ];

        $subQueries = [];
        if ($user->can('caseListOwner')) {
            $caseListOwnerQuery = clone ($query);
            $caseListOwnerQuery->select(['cases.*'])
                ->andWhere($this->createSubQuery($user->id, [], $checkDepPermission = false, true));
            $caseListOwnerQuery->addSelect($timeLeftSelect);
            $subQueries[] = $caseListOwnerQuery;
        }
        if ($user->can('caseListEmpty')) {
            $caseListEmptyQuery = clone ($query);
            $condition['cs_user_id'] = null;
            $caseListEmptyQuery->select(['cases.*'])
                ->andWhere($this->createSubQuery($user->id, $condition, $checkDepPermission = false, false));
            $caseListEmptyQuery->addSelect($timeLeftSelect);
            $subQueries[] = $caseListEmptyQuery;
        }
        if ($user->can('caseListAny')) {
            $caseListAnyQuery = clone ($query);
            $caseListAnyQuery->select(['cases.*'])
                ->andWhere($this->createSubQuery($user->id, [], $checkDepPermission = false, false));
            $caseListAnyQuery->addSelect($timeLeftSelect);
            $subQueries[] = $caseListAnyQuery;
        }
        if ($user->can('caseListGroup')) {
            $caseListGroupQuery = clone ($query);
            $conditions = ['cs_user_id' => $this->usersIdsInCommonGroups($user->id, 60)];
            $caseListGroupQuery->select(['cases.*'])
                ->andWhere($this->createSubQuery($user->id, $conditions, $checkDepPermission = false, false));
            $caseListGroupQuery->addSelect($timeLeftSelect);
            $subQueries[] = $caseListGroupQuery;
        }

        if (empty($subQueries)) {
            $query->where('0=1');
            return $query;
        }

        foreach ($subQueries as $key => $subQuery) {
            if ($key === 0) {
                $parentQuery = $subQuery;
                continue;
            }
            $parentQuery->union($subQuery);
        }
        $parentQuery->distinct();

        return $parentQuery;
    }

    /**
     * @param Employee $user
     * @return int
     */
    public function getFollowUpCount(Employee $user): int
    {
        return $this->getFollowUpQuery($user)->count();
    }

    /**
     * @param Employee $user
     * @return ActiveQuery
     */
    public function getFollowUpQuery(Employee $user): ActiveQuery
    {
        $query = CasesQSearch::find()->andWhere(['cs_status' => CasesStatus::STATUS_FOLLOW_UP]);

        if ($user->isAdmin()) {
            return $query;
        }

        $conditions = [];

        $query->andWhere($this->createSubQuery($user->id, $conditions, $checkDepPermission = false));

        return $query;
    }

    /**
     * @param Employee $user
     * @return int
     */
    public function getProcessingCount(Employee $user): int
    {
        return $this->getProcessingQuery($user)->count();
    }

    /**
     * @param Employee $user
     * @return ActiveQuery
     */
    public function getProcessingQuery(Employee $user): ActiveQuery
    {
        $query = CasesQSearch::find()->select('cases.*')->andWhere(['cs_status' => CasesStatus::STATUS_PROCESSING]);

        if ($user->isAdmin()) {
            return $query;
        }

        $conditions = [];

        if ($user->isSupAgent() || $user->isExAgent()) {
            $conditions = $this->isOwner($user->id);
        }

        if ($user->isExSuper() || $user->isSupSuper()) {
            $conditions = [
                'cs_user_id' => $this->usersIdsInCommonGroups($user->id)
            ];
        }

        $query->andWhere($this->createSubQuery($user->id, $conditions, $checkDepPermission = false));

        return $query;
    }

    /**
     * @param Employee $user
     * @return int
     */
    public function getSolvedCount(Employee $user): int
    {
        return $this->getSolvedQuery($user)->count();
    }

    /**
     * @param Employee $user
     * @return ActiveQuery
     */
    public function getSolvedQuery(Employee $user): ActiveQuery
    {
        $query = CasesQSearch::find()->where(['cs_status' => CasesStatus::STATUS_SOLVED]);

        if ($user->isAdmin()) {
            return $query;
        }

        $conditions = [];

        if ($user->isSupAgent() || $user->isExAgent()) {
            $conditions = $this->isOwner($user->id);
        }

        if ($user->isExSuper() || $user->isSupSuper()) {
            $conditions = [
                'cs_user_id' => $this->usersIdsInCommonGroups($user->id)
            ];
        }

        $query->andWhere($this->createSubQuery($user->id, $conditions, $checkDepPermission = false));

        return $query;
    }

    /**
     * @param Employee $user
     * @return int
     */
    public function getTrashCount(Employee $user): int
    {
        return $this->getTrashQuery($user)->count();
    }

    /**
     * @param Employee $user
     * @return ActiveQuery
     */
    public function getTrashQuery(Employee $user): ActiveQuery
    {
        $query = CasesQSearch::find()->andWhere(['cs_status' => CasesStatus::STATUS_TRASH]);

        if ($user->isAdmin()) {
            return $query;
        }

        $conditions = [];

        $query->andWhere($this->createSubQuery($user->id, $conditions, $checkDepPermission = false));

        return $query;
    }

    /**
     * @param Employee $user
     * @return ActiveQuery
     */
    public function getHotQuery(Employee $user): ActiveQuery
    {
        $query = CasesQSearch::find();

        if ($user->isAdmin()) {
            return $query;
        }

        $conditions = [];

        if ($user->isSupAgent() || $user->isExAgent()) {
            $conditions = $this->freeCase();
        }

        $query->andWhere($this->createSubQuery($user->id, $conditions));

        return $query;
    }

    /**
     * @param int $joinByStatus
     * @return CaseSaleQuery
     */
    public function getNexFlightDateSubQuery(int $joinByStatus = 0): CaseSaleQuery
    {
        $query = CaseSale::find()
            ->select([
                'css_cs_id',
                new Expression('
                    MIN(css_out_date) AS last_out_date'),
            ]);

        if ($joinByStatus) {
            $query->innerJoin(Cases::tableName() . ' AS cases',
                'case_sale.css_cs_id = cases.cs_id AND cases.cs_status = ' . $joinByStatus);
        }

        $query->where('css_out_date >= SUBDATE(CURDATE(), 1)')
            ->orWhere('css_in_date >= SUBDATE(CURDATE(), 1)')
            ->groupBy('css_cs_id');

        return $query;
    }

    /**
     * @param $userId
     * @param int $cacheDuration
     * @return ActiveQuery
     */
    private function usersIdsInCommonGroups($userId, int $cacheDuration = -1): ActiveQuery
    {
        return EmployeeGroupAccess::usersIdsInCommonGroupsSubQuery($userId, $cacheDuration);
    }

    /**
     * @param $userId
     * @return array
     */
    private function isOwner($userId): array
    {
        return ['cs_user_id' => $userId];
    }

    /**
     * @return array
     */
    private function freeCase(): array
    {
        return ['cs_user_id' => null];
    }

    /**
     * @param $userId
     * @return array
     */
    private function inProject($userId): array
    {
        return ['cs_project_id' => EmployeeProjectAccess::getProjectsSubQuery($userId)];
    }

    /**
     * @param $userId
     * @return array
     */
    private function inDepartment($userId): array
    {
        return [
            'cs_dep_id' => EmployeeDepartmentAccess::getDepartmentsSubQuery($userId)
        ];
    }

    private function createSubQuery($userId, $conditions, $checkDepPermission = true, bool $isOwner = true): array
    {
        $depConditions = [];
        if ($checkDepPermission) {
            $depConditions = $this->inDepartment($userId);
        }

        $result = [
            'or',
            [
                'and',
                $this->inProject($userId),
                $depConditions,
                $conditions
            ]
        ];

        if ($isOwner) {
            $result['cs_user_id'] = $userId;
        }

        return $result;
    }
}
