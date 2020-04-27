<?php

namespace sales\repositories\lead;

use common\models\Employee;
use common\models\Lead;
use common\models\ProfitSplit;
use common\models\TipsSplit;
use common\models\UserGroup;
use common\models\UserGroupAssign;
use sales\access\EmployeeGroupAccess;
use sales\access\EmployeeProjectAccess;
use yii\db\ActiveQuery;

class LeadBadgesRepository
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
        $query = Lead::find()->andWhere([Lead::tableName() . '.status' => Lead::STATUS_PENDING]);

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

        $query = Lead::find()->andWhere([Lead::tableName() . '.status' => Lead::STATUS_PENDING])->andWhere(['<>', 'l_call_status_id', Lead::CALL_STATUS_QUEUE]);

        if ($user->isAdmin()) {
            return $query;
        }

        $conditions = [];

        if ($user->isAgent() || $user->isExAgent()) {
            $conditions = $this->freeLead();
        }

        $query->andWhere($this->createSubQuery($user->id, $conditions));

        return $query;
    }

    /**
     * @param Employee $user
     * @return int
     */
    public function getFailedBookingsCount(Employee $user): int
    {
        return $this->getFailedBookingsQuery($user)->count();
    }

    /**
     * @param Employee $user
     * @return ActiveQuery
     */
    public function getFailedBookingsQuery(Employee $user): ActiveQuery
    {
        $query = Lead::find()->andWhere([Lead::tableName() . '.status' => Lead::STATUS_BOOK_FAILED])->andWhere(['<>', 'l_call_status_id', Lead::CALL_STATUS_QUEUE]);

        if ($user->isAdmin()) {
            return $query;
        }

        $conditions = [];

        if ($user->isAgent() || $user->isExAgent()) {
            $conditions = $this->freeLead();
        }

        $query->andWhere($this->createSubQuery($user->id, $conditions));

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
        $query = Lead::find()->andWhere([Lead::tableName() . '.status' => Lead::STATUS_FOLLOW_UP]);

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
    public function getBonusCount(Employee $user): int
    {
        return $this->getFollowUpQuery($user)->count();
    }

    /**
     * @param Employee $user
     * @return ActiveQuery
     */
    public function getBonusQuery(Employee $user): ActiveQuery
    {
        $query = Lead::find()->andWhere([Lead::tableName() . '.status' => Lead::STATUS_FOLLOW_UP]);

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
        $query = Lead::find()->andWhere([Lead::tableName() . '.status' => array_keys(Lead::getProcessingStatuses())]);

        if ($user->isAdmin()) {
            return $query;
        }

        $conditions = [];

        if ($user->isAgent() || $user->isExAgent()) {
            $conditions = $this->isOwner($user->id);
        }

        if ($user->isSupervision() || $user->isExSuper()) {
            $conditions = [
                Lead::tableName() . '.employee_id' => $this->usersIdsInCommonGroups($user->id)
            ];
        }

        $query->andWhere($this->createSubQuery($user->id, $conditions));

        return $query;
    }

    /**
     * @param Employee $user
     * @return int
     */
    public function getBookedCount(Employee $user): int
    {
        return $this->getBookedQuery($user)->count();
    }

    /**
     * @param Employee $user
     * @return ActiveQuery
     */
    public function getBookedQuery(Employee $user): ActiveQuery
    {
        $query = Lead::find()->andWhere([Lead::tableName() . '.status' => Lead::STATUS_BOOKED]);

        if ($user->isAdmin()) {
            return $query;
        }

        if ($user->isAgent() || $user->isSupervision()) {

            $employees = Employee::find()->select('id')
                ->andWhere(['or',
                    [
                        'id' => $user->id,
                    ],
                    [
                        'id' => EmployeeGroupAccess::getUsersIdsInCommonGroups($user->id),
                    ],
                    [
                        'id' => UserGroupAssign::find()->select(['ugs_user_id'])->andWhere([
                            'ugs_group_id' =>
                                UserGroup::find()->select('ug_id')->andWhere([
                                    'ug_user_group_set_id' =>
                                        UserGroup::find()->select(['ug_user_group_set_id'])->andWhere([
                                            'ug_id' =>
                                                UserGroupAssign::find()->select(['ugs_group_id'])->andWhere([
                                                    'ugs_user_id' => $user->id
                                                ])
                                        ])->andWhere(['IS NOT', 'ug_user_group_set_id', null])->andWhere(['ug_disable' => 0])
                                ])
                        ]),
                    ]
                ])
                ->asArray()->indexBy('id')->all();

            $query->andWhere([Lead::tableName() . '.employee_id' => array_keys($employees)]);

        }

//        if ($myGroups = $user->getUserGroupList()) {
//            $ruleGroups = [20 => 'Avengers', 21 => 'Revelation', 22 => 'Gunners'];
//            foreach ($ruleGroups as $ruleGroup) {
//                if (in_array($ruleGroup, $myGroups, true)) {
//                    $usersIds = UserGroupAssign::find()->select('ugs_user_id')->andWhere(['ugs_group_id' => array_keys($ruleGroups)])->indexBy('ugs_user_id')->column();
//                    $usersIds = Employee::find()->select('id')->andWhere(['id' => array_keys($usersIds)])->active()->indexBy('id')->column();
//                    if ($usersIds) {
//                        $query->andWhere([Lead::tableName() . '.employee_id' => array_keys($usersIds)]);
//                    }
//                    break;
//                }
//            }
//        }

        $conditions = [];

        $query->andWhere($this->createSubQuery($user->id, $conditions));

        return $query;
    }

    /**
     * @param Employee $user
     * @return int
     */
    public function getSoldCount(Employee $user): int
    {
        return $this->getSoldQuery($user)->count();
    }

    /**
     * @param Employee $user
     * @return ActiveQuery
     */
    public function getSoldQuery(Employee $user): ActiveQuery
    {
        $query = Lead::find()->where([Lead::tableName() . '.status' => Lead::STATUS_SOLD]);

        if ($user->isAdmin()) {
            return $query;
        }

        $conditions = [];

        if ($user->isAgent() || $user->isExAgent()) {
            $conditions = $this->inSplit($user->id);
        }

        if ($user->isSupervision() || $user->isExSuper()) {
            $conditions = ['or',
                $this->inSplit($user->id),
                $this->inSplit($this->usersIdsInCommonGroups($user->id)),
                [
                    Lead::tableName() . '.employee_id' => $this->usersIdsInCommonGroups($user->id)
                ]
            ];
        }

        $query->andWhere($this->createSubQuery($user->id, $conditions));

        return $query;
    }

    /**
     * @param Employee $user
     * @return int
     */
    public function getDuplicateCount(Employee $user): int
    {
        return $this->getDuplicateQuery($user)->count();
    }

    /**
     * @param Employee $user
     * @return ActiveQuery
     */
    public function getDuplicateQuery(Employee $user): ActiveQuery
    {
        $query = Lead::find()
            ->andWhere([Lead::tableName() . '.status' => Lead::STATUS_TRASH])
            ->andWhere(['IS NOT', 'l_duplicate_lead_id', NULL]);

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
        $query = Lead::find()->andWhere([Lead::tableName() . '.status' => Lead::STATUS_TRASH]);

        if ($user->isAdmin()) {
            return $query;
        }

        $conditions = [];

        $query->andWhere($this->createSubQuery($user->id, $conditions));

        return $query;
    }

    /**
     * @param $userId
     * @return array
     */
    private function inSplit($userId): array
    {
        return ['or',
            [Lead::tableName() . '.id' => ProfitSplit::find()->select('ps_lead_id')->andWhere(['ps_user_id' => $userId])],
            [Lead::tableName() . '.id' => TipsSplit::find()->select('ts_lead_id')->andWhere(['ts_user_id' => $userId])]
        ];
    }

    /**
     * @param $userId
     * @return array
     */
    private function isOwner($userId): array
    {
        return [Lead::tableName() . '.employee_id' => $userId];
    }

    /**
     * @param $userId
     * @return array
     */
    private function inProject($userId): array
    {
        return ['project_id' => EmployeeProjectAccess::getProjectsSubQuery($userId)];
    }

    /**
     * @return array
     */
    private function freeLead(): array
    {
        return [Lead::tableName() . '.employee_id' => null];
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
                $conditions
            ]
        ];
    }

}
