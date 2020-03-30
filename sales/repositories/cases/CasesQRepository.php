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

        $query->andWhere($this->createSubQuery($user->id, $conditions));

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

    /**
     * @param Employee $user
     * @return ActiveQuery
     */
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

        $query->andWhere($this->createSubQuery($user->id, $condition));

        return $query;
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

        $query->andWhere($this->createSubQuery($user->id, $conditions));

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
        $query = CasesQSearch::find()->andWhere(['cs_status' => CasesStatus::STATUS_PROCESSING]);

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

        $query->andWhere($this->createSubQuery($user->id, $conditions));

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

        $query->andWhere($this->createSubQuery($user->id, $conditions));

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

        $query->andWhere($this->createSubQuery($user->id, $conditions));

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
     * @return CaseSaleQuery
     */
    public function getLastFlightDatesSubQuery(): CaseSaleQuery
    {
        return CaseSale::find()
            ->select([
                'css_cs_id',
                new Expression('
                    MIN(if(css_in_date >= SUBDATE(CURDATE(), 1), css_in_date, NULL)) AS last_in_date'),
                new Expression('
                    MIN(if(css_out_date >= SUBDATE(CURDATE(), 1), css_out_date, NULL)) AS last_out_date'),
            ])
            ->where('css_out_date >= SUBDATE(CURDATE(), 1)')
            ->orWhere('css_in_date >= SUBDATE(CURDATE(), 1)')
            ->groupBy('css_cs_id');
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
                    MIN(if(css_out_date >= SUBDATE(CURDATE(), 1), css_out_date, css_in_date)) as nextFlight'),
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
     * @return ActiveQuery
     */
    private function usersIdsInCommonGroups($userId): ActiveQuery
    {
        return EmployeeGroupAccess::usersIdsInCommonGroupsSubQuery($userId);
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

    /**
     * @param $userId
     * @param $conditions
     * @return array
     */
    private function createSubQuery($userId, $conditions): array
    {
        return [
            'or',
            $this->isOwner($userId),
            [
                'and',
                $this->inProject($userId),
                $this->inDepartment($userId),
                $conditions
            ]
        ];
    }


}
