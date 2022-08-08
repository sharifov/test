<?php

namespace src\repositories\lead;

use common\models\Employee;
use common\models\Lead;
use common\models\ProfitSplit;
use common\models\Quote;
use common\models\TipsSplit;
use common\models\UserDepartment;
use common\models\UserGroup;
use common\models\UserGroupAssign;
use common\models\UserProfile;
use modules\featureFlag\FFlag;
use modules\lead\src\abac\dto\LeadAbacDto;
use src\access\EmployeeDepartmentAccess;
use src\access\EmployeeGroupAccess;
use src\access\EmployeeProjectAccess;
use src\model\leadDataKey\services\LeadDataKeyDictionary;
use Yii;
use yii\db\ActiveQuery;
use modules\lead\src\abac\LeadAbacObject;
use yii\helpers\ArrayHelper;

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
     * @return ActiveQuery
     */
    public function getClosedQuery(Employee $user): ActiveQuery
    {
        $query = Lead::find()->andWhere([Lead::tableName() . '.status' => Lead::STATUS_CLOSED]);

        if ($user->isAdmin()) {
            return $query;
        }

        return $query->andWhere($this->isOwner($user->id));
    }

    /**
     * @param Employee $user
     * @return int
     */
    public function getClosedCount(Employee $user): int
    {
        return $this->getClosedQuery($user)->count();
    }

    /**
     * @param Employee $user
     * @return int
     */
    public function getBusinessInboxCount(Employee $user): int
    {
        /** @fflag FFlag::FF_KEY_SMART_LEAD_DISTRIBUTION_ENABLE, Smart Lead Distribution Enable */
        if (Yii::$app->featureFlag->isEnable(FFlag::FF_KEY_SMART_LEAD_DISTRIBUTION_ENABLE) === true) {
            $leadsByAgentSkill = $this->countBusinessLeadsByAgentSkill();
            $employeeSkill = $user->userProfile->up_skill;

            if (empty($employeeSkill)) {
                return $leadsByAgentSkill[UserProfile::SKILL_TYPE_SENIOR];
            } elseif ($employeeSkill === UserProfile::SKILL_TYPE_JUNIOR && $leadsByAgentSkill[UserProfile::SKILL_TYPE_JUNIOR] <= 0) {
                $employeeSkill = UserProfile::SKILL_TYPE_MIDDLE;
            }

            return $leadsByAgentSkill[$employeeSkill] ?? 0;
        }

        return $this->getBusinessInboxQuery($user)->count();
    }

    public function getBusinessInboxQuery(?Employee $user = null): ActiveQuery
    {
        $query = Lead::find()
            ->orWhere([
                'and',
                ['project_id' => 7],
                ['l_is_test' => false],
                ['status' => Lead::STATUS_PENDING],
                ['<>', 'l_call_status_id', 5]
            ])
            ->orWhere([
                'and',
                ['cabin' => Lead::CABIN_BUSINESS],
                ['l_is_test' => false],
                ['status' => Lead::STATUS_PENDING],
                ['<>', 'l_call_status_id', 5]
            ])
            ->orWhere([
                'and',
                ['cabin' => Lead::CABIN_FIRST],
                ['l_is_test' => false],
                ['status' => Lead::STATUS_PENDING],
                ['<>', 'l_call_status_id', 5]
            ]);

        return $query;
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

    public function getAlternativeCount(Employee $user): int
    {
        return $this->getAlternativeQuery($user)->count();
    }

    public function getAlternativeQuery(Employee $user): ActiveQuery
    {
        $query = Lead::find()->andWhere([Lead::tableName() . '.status' => Lead::STATUS_ALTERNATIVE])
            ->andWhere(['<>', 'l_call_status_id', Lead::CALL_STATUS_QUEUE])
            ->andWhere(['<>', 'l_is_test', true]);

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

        /** @fflag FFlag::FF_KEY_BOOKED_QUEUE_CONDITION_BY_DEPARTMENT, Booked Queue condition by department enable */
        if (Yii::$app->featureFlag->isEnable(FFlag::FF_KEY_BOOKED_QUEUE_CONDITION_BY_DEPARTMENT) && $user->userDepartments) {
            $conditions = ['IN', Lead::tableName() . '.l_dep_id', ArrayHelper::map($user->userDepartments, 'ud_dep_id', 'ud_dep_id')];
        }

        /** @fflag FFlag::FF_KEY_BOOKED_QUEUE_CONDITION_BY_DEPARTMENT, Booked Queue condition for agent only owner */
        if (Yii::$app->featureFlag->isEnable(FFlag::FF_KEY_BOOKED_QUEUE_CONDITION_AGENT_IS_OWNER)) {
            if ($user->isAgent()) {
                $query->andWhere($this->isOwner($user->id));
            } else {
                $query->andWhere($this->createSubQuery($user->id, $conditions));
            }
        } else {
            $query->andWhere($this->createSubQuery($user->id, $conditions));
        }

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

        /** @abac null, LeadAbacObject::QUERY_SOLD_ALL, LeadAbacObject::ACTION_ACCESS, Access to all sold leads */
        if (\Yii::$app->abac->can(null, LeadAbacObject::QUERY_SOLD_ALL, LeadAbacObject::ACTION_ACCESS)) {
            return $query;
        }

        $conditions = [];
        $displayFlag = false;

        /** @abac null, LeadAbacObject::QUERY_SOLD_ON_COMMON_PROJECTS, LeadAbacObject::ACTION_ACCESS, Access sold leads in common projects */
        if (\Yii::$app->abac->can(null, LeadAbacObject::QUERY_SOLD_PROJECTS, LeadAbacObject::ACTION_ACCESS)) {
            $query->andWhere($this->createInProjectSubQuery($user->id, $conditions));
            $displayFlag = true;
        }

        /** @abac null, LeadAbacObject::QUERY_SOLD_ON_COMMON_DEPARTMENTS, LeadAbacObject::ACTION_ACCESS, Access sold leads in common departments */
        if (\Yii::$app->abac->can(null, LeadAbacObject::QUERY_SOLD_DEPARTMENTS, LeadAbacObject::ACTION_ACCESS)) {
            $query->andWhere($this->createInDepartmentsSubQuery($user->id, $conditions));
            $displayFlag = true;
        }

        /** @abac null, LeadAbacObject::QUERY_SOLD_ON_COMMON_GROUPS, LeadAbacObject::ACTION_ACCESS, Access sold leads in common groups */
        if (\Yii::$app->abac->can(null, LeadAbacObject::QUERY_SOLD_GROUPS, LeadAbacObject::ACTION_ACCESS)) {
            $query->andWhere($this->createInGroupsSubQuery($user->id, $conditions));
            $displayFlag = true;
        }

        /** @abac null, LeadAbacObject::QUERY_SOLD_IS_OWNER, LeadAbacObject::ACTION_ACCESS, Access sold leads where user id owner */
        if (\Yii::$app->abac->can(null, LeadAbacObject::QUERY_SOLD_IS_OWNER, LeadAbacObject::ACTION_ACCESS)) {
            $query->andWhere([Lead::tableName() . '.employee_id' => $user->id]);
            $query->orWhere([ProfitSplit::tableName() . '.ps_user_id' => $user->id]);
            $query->orWhere([TipsSplit::tableName() . '.ts_user_id' => $user->id]);
            //$query->orWhere($this->inSplit($user->id));
            $query->leftJoin('profit_split', 'ps_lead_id = leads.id and ps_user_id = ' . $user->id);
            $query->leftJoin('tips_split', 'ts_lead_id = leads.id and ts_user_id = ' . $user->id);
            $displayFlag = true;
        }
        $lead = new Lead();
        $lead->employee_id = null;
        /** @abac null, LeadAbacObject::QUERY_SOLD_IS_EMPTY_OWNER, LeadAbacObject::ACTION_ACCESS, Access sold leads where lead have empty owner */
        if (\Yii::$app->abac->can(new LeadAbacDto($lead, 0), LeadAbacObject::QUERY_SOLD_IS_EMPTY_OWNER, LeadAbacObject::ACTION_QUERY_AND)) {
            $query->andWhere([Lead::tableName() . '.employee_id' => null]);
            $displayFlag = true;
        }

        /** @abac null, LeadAbacObject::QUERY_SOLD_IS_EMPTY_OWNER, LeadAbacObject::ACTION_ACCESS, Access sold leads where lead have empty owner */
        if (\Yii::$app->abac->can(new LeadAbacDto($lead, 0), LeadAbacObject::QUERY_SOLD_IS_EMPTY_OWNER, LeadAbacObject::ACTION_QUERY_OR)) {
            $query->orWhere([Lead::tableName() . '.employee_id' => null])->andWhere([Lead::tableName() . '.status' => Lead::STATUS_SOLD]);
            $displayFlag = true;
        }

        /*if ($user->isAgent() || $user->isExAgent()) {
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

        $query->andWhere($this->createSubQuery($user->id, $conditions));*/

        //var_dump($query->createCommand()->getRawSql());die();

        if (!$displayFlag) {
            $query->where('0=1');
        }

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
            ->andWhere(['IS NOT', 'l_duplicate_lead_id', null]);

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

        if ($user->isAgent()) {
            $conditions = ['and',
                [
                    Lead::tableName() . '.employee_id' => $user->id
                ]
            ];

            $query->andWhere($conditions);

            return $query;
        }

        $query->andWhere($this->createSubQuery($user->id, $conditions));

        return $query;
    }

    public function getExtraQueueCount(): int
    {
        return $this->getExtraQueueQuery()->count();
    }

    public function getBusinessExtraQueueCount(): int
    {
        return $this->getExtraQueueQuery(Lead::STATUS_BUSINESS_EXTRA_QUEUE)->count();
    }

    public function getExtraQueueQuery(string $status = Lead::STATUS_EXTRA_QUEUE): ActiveQuery
    {
        return Lead::find()
            ->andWhere([Lead::tableName() . '.status' => $status]);
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
     * @param $userId
     * @return array
     */
    private function inDepartment($userId): array
    {
        return [Lead::tableName() . '.employee_id' => EmployeeDepartmentAccess::usersIdsInCommonDepartmentsSubQuery($userId)];
    }

    /**
     * @param $userId
     * @return array
     */
    private function inGroups($userId): array
    {
        return [Lead::tableName() . '.employee_id' => $this->usersIdsInCommonGroups($userId)];
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
     * @return ActiveQuery
     */
    private function usersIdsInCommonDepartments($userId): ActiveQuery
    {
        return EmployeeDepartmentAccess::usersIdsInCommonDepartmentsSubQuery($userId);
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

    /**
     * @param $userId
     * @param $conditions
     * @return array
     */
    private function createInProjectSubQuery($userId, $conditions): array
    {
        return [
            'and',
            $this->inProject($userId),
            $conditions
        ];
    }

    /**
     * @param $userId
     * @param $conditions
     * @return array
     */
    private function createInDepartmentsSubQuery($userId, $conditions): array
    {
        return [
            'and',
            $this->inDepartment($userId),
            $conditions
        ];
    }

    /**
     * @param $userId
     * @param $conditions
     * @return array
     */
    private function createInGroupsSubQuery($userId, $conditions): array
    {
        return [
            'and',
            $this->inGroups($userId),
            $conditions
        ];
    }

    public function countBusinessLeadsByAgentSkill(): array
    {
        $setting = Yii::$app->params['settings']['smart_lead_distribution_by_agent_skill_and_points'];
        $data = [];

        foreach ($setting['business'] as $skillID => $points) {
            $query = $this->getBusinessInboxQuery()
                ->asArray()
                ->select('COUNT(leads.id) as amount');

            $query->leftJoin(
                'lead_data',
                'leads.id = lead_data.ld_lead_id AND lead_data.ld_field_key = :key',
                [
                    'key' => LeadDataKeyDictionary::KEY_LEAD_RATING_POINTS_DYNAMIC,
                ]
            );

            $query->andWhere([
                'OR',
                [
                    'BETWEEN',
                    'lead_data.ld_field_value',
                    $points['from'],
                    $points['to']
                ],
                [
                    'lead_data.ld_field_value' => null,
                ],
            ]);

            $query->column();

            $data[$skillID] = (int)$query->one()['amount'];
        }

        return $data;
    }
}
