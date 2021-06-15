<?php

namespace sales\repositories\cases;

use common\models\CaseSale;
use common\models\Employee;
use common\models\query\CaseSaleQuery;
use common\models\UserGroupAssign;
use sales\access\EmployeeDepartmentAccess;
use sales\access\EmployeeGroupAccess;
use sales\access\EmployeeProjectAccess;
use sales\auth\Auth;
use sales\entities\cases\Cases;
use sales\entities\cases\CasesQSearch;
use sales\entities\cases\CasesStatus;
use yii\db\ActiveQuery;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;

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

        $query->andWhere($this->createSubQuery($user->id, $conditions, $checkDepPermission = true));

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
        $query = CasesQSearch::find()
            ->andWhere(['cs_need_action' => true])
            ->andWhere(['<>', 'cs_status', CasesStatus::STATUS_PENDING]);

        $query->andWhere($this->createSubQueryForNeedAction($user->id, [], $checkDepPermission = true));

        if ($user->can('caseListAny')) {
            return $query;
        }

        $userIds = [];
        if ($user->can('caseListOwner')) {
            $userIds[] = $user->id;
        }
        if ($user->can('caseListEmpty')) {
            $userIds[] = null;
        }
        if ($user->can('caseListGroup')) {
            $userIds = ArrayHelper::merge($userIds, EmployeeGroupAccess::getUsersIdsInCommonGroups($user->id));
        }

        $query->andWhere(['IN', 'cs_user_id', $userIds]);

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

        $query->andWhere($this->createSubQuery($user->id, $conditions, $checkDepPermission = true));

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

        $all = Auth::can('cases-q/processing/list/all');
        $owner = Auth::can('cases-q/processing/list/owner');
        $group = Auth::can('cases-q/processing/list/group');
        $empty = Auth::can('cases-q/processing/list/empty');

        if (!$all && !$owner && !$group && !$empty) {
            $query->where('0 = 1');
            return $query;
        }

        if (!$all) {
            $query->andWhere([
                'OR',
                $owner ? $this->isOwner($user->id) : [],
                $group ? ['cs_user_id' => $this->usersIdsInCommonGroups($user->id)] : [],
                $empty ? $this->freeCase() : []
            ]);
        }

        $conditions = [];

        $query->andWhere($this->createSubQuery($user->id, $conditions, $checkDepPermission = true));

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

        $all = Auth::can('cases-q/solved/list/all');
        $owner = Auth::can('cases-q/solved/list/owner');
        $group = Auth::can('cases-q/solved/list/group');
        $empty = Auth::can('cases-q/solved/list/empty');

        if (!$all && !$owner && !$group && !$empty) {
            $query->where('0 = 1');
            return $query;
        }

        if (!$all) {
            $query->andWhere([
                'OR',
                $owner ? $this->isOwner($user->id) : [],
                $group ? ['cs_user_id' => $this->usersIdsInCommonGroups($user->id)] : [],
                $empty ? $this->freeCase() : []
            ]);
        }

        $conditions = [];

        $query->andWhere($this->createSubQuery($user->id, $conditions, $checkDepPermission = true));

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

        $all = Auth::can('cases-q/trash/list/all');
        $owner = Auth::can('cases-q/trash/list/owner');
        $group = Auth::can('cases-q/trash/list/group');
        $empty = Auth::can('cases-q/trash/list/empty');

        if (!$all && !$owner && !$group && !$empty) {
            $query->where('0 = 1');
            return $query;
        }

        if (!$all) {
            $query->andWhere([
                'OR',
                $owner ? $this->isOwner($user->id) : [],
                $group ? ['cs_user_id' => $this->usersIdsInCommonGroups($user->id)] : [],
                $empty ? $this->freeCase() : []
            ]);
        }

        $conditions = [];

        $query->andWhere($this->createSubQuery($user->id, $conditions, $checkDepPermission = true));

        return $query;
    }

    /**
     * @param Employee $user
     * @return int
     */
    public function getUnidentifiedCount(Employee $user): int
    {
        return $this->getUnidentifiedQuery($user)->count();
    }

    public function getUnidentifiedQuery(Employee $user): ActiveQuery
    {
        $query = CasesQSearch::find()->andWhere(['cs_status' => [CasesStatus::STATUS_PENDING, CasesStatus::STATUS_PROCESSING, CasesStatus::STATUS_FOLLOW_UP]]);
        $query->joinWith(['client', 'caseSale']);
        $query->andWhere(['css_cs_id' => null]);

        if (!$user->isAdmin()) {
            $query->andWhere(['cs_project_id' => array_keys(EmployeeProjectAccess::getProjects($user))]);
            $query->andWhere(['cs_dep_id' => array_keys(EmployeeDepartmentAccess::getDepartments($user))]);
        }
        return $query;
    }

    /**
     * @param Employee $user
     * @return int
     */
    public function getFirstPriorityCount(Employee $user): int
    {
        //return $this->getFirstPriorityQuery($user)->count();
        return 0;
    }

    public function getFirstPriorityQuery(Employee $user): ActiveQuery
    {
        $query = CasesQSearch::find()->andWhere(['cs_status' => [CasesStatus::STATUS_PENDING, CasesStatus::STATUS_PROCESSING, CasesStatus::STATUS_FOLLOW_UP]]);
        $query->joinWith(['client', 'caseSale as cs']);
        $query->andWhere(['not', ['cs.css_cs_id' => null]]);

        if (!$user->isAdmin()) {
            $query->andWhere(['cs_project_id' => array_keys(EmployeeProjectAccess::getProjects($user))]);
            $query->andWhere(['cs_dep_id' => array_keys(EmployeeDepartmentAccess::getDepartments($user))]);
        }
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
            $query->innerJoin(
                Cases::tableName() . ' AS cases',
                'case_sale.css_cs_id = cases.cs_id AND cases.cs_status = ' . $joinByStatus
            );
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

    /**
     * @param $userId
     * @param $conditions
     * @param bool $checkDepPermission
     * @return array
     */
    private function createSubQueryForNeedAction($userId, $conditions, $checkDepPermission = true): array
    {
        $depConditions = [];
        if ($checkDepPermission) {
            $depConditions = $this->inDepartment($userId);
        }

        return [
            'or',
            [
                'and',
                $this->inProject($userId),
                $depConditions,
                $conditions
            ]
        ];
    }

    private function createSubQuery($userId, $conditions, $checkDepPermission = true): array
    {
        $depConditions = [];
        if ($checkDepPermission) {
            $depConditions = $this->inDepartment($userId);
        }

        return [
            'or',
            $this->isOwner($userId),
            [
                'and',
                $this->inProject($userId),
                $depConditions,
                $conditions
            ]
        ];
    }
}
