<?php

namespace sales\repositories\lead;

use common\models\Employee;
use common\models\Lead;
use common\models\ProfitSplit;
use common\models\Project;
use common\models\ProjectEmployeeAccess;
use common\models\TipsSplit;
use common\models\UserGroupAssign;
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

        $conditions = $this->getConditions($user->id);

        if ($user->isAgent()) {
            $conditions['own']['enable'] = true;
            $conditions['inProjectsAgent']['enable'] = true;
        }
        if ($user->isSupervision()) {
            $conditions['own']['enable'] = true;
            $conditions['inProjects']['enable'] = true;
            $conditions['inGroups']['enable'] = true;
        }

        $query->andWhere($this->createSubQuery($conditions));

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

        $conditions = $this->getConditions($user->id);

        if ($user->isAgent()) {
            $conditions['own']['enable'] = true;
            $conditions['inProjectsAgent']['enable'] = true;
        }
        if ($user->isSupervision()) {
            $conditions['own']['enable'] = true;
            $conditions['inProjects']['enable'] = true;
            $conditions['inGroups']['enable'] = true;
        }

        $query->andWhere($this->createSubQuery($conditions));

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

        $conditions = $this->getConditions($user->id);

        if ($user->isAgent()) {
            $conditions['own']['enable'] = true;
        }
        if ($user->isSupervision()) {
            $conditions['own']['enable'] = true;
            $conditions['inGroups']['enable'] = true;
        }

        $query->andWhere($this->createSubQuery($conditions));

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

        $conditions = $this->getConditions($user->id);

        if ($user->isAgent()) {
            $conditions['own']['enable'] = true;
            $conditions['inProjects']['enable'] = true;
        }
        if ($user->isSupervision()) {
            $conditions['own']['enable'] = true;
            $conditions['inProjects']['enable'] = true;
            $conditions['inGroups']['enable'] = true;
        }

        $query->andWhere($this->createSubQuery($conditions));

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

        $conditions = $this->getConditions($user->id);

        if ($user->isAgent()) {
            $conditions['own']['enable'] = true;
            $conditions['inSplit']['enable'] = true;
        }
        if ($user->isSupervision()) {
            $conditions['own']['enable'] = true;
            $conditions['inSplit']['enable'] = true;
            $conditions['inProjectsSupervision']['enable'] = true;
        }
        if ($user->isQa()) {
            $conditions['inProjects']['enable'] = true;
            $conditions['inGroups']['enable'] = true;
        }

        $query->andWhere($this->createSubQuery($conditions));

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

        $conditions = $this->getConditions($user->id);

        if ($user->isSupervision()) {
            $conditions['inProjects']['enable'] = true;
            $conditions['inGroups']['enable'] = true;
        }
        if ($user->isQa()) {
            $conditions['inProjects']['enable'] = true;
            $conditions['inGroups']['enable'] = true;
        }

        $query->andWhere($this->createSubQuery($conditions));

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

        if ($user->isAdmin()) {
            return $query;
        }

        if ($user->isSupervision()) {
            return $query;
        }

        return $query->andWhere('0 = 1');
    }

    /**
     * @param $userId
     * @return array
     */
    private function getConditions($userId): array
    {
        return [
            'own' => [
                'enable' => false,
                'query' => ['employee_id' => $userId]
            ],
            'inProjects' => [
                'enable' => false,
                'query' => [
                    'project_id' => Project::find()->select('id')->andWhere([
                        'closed' => false,
                        'id' => ProjectEmployeeAccess::find()->select('project_id')->andWhere(['employee_id' => $userId])
                    ])
                ]
            ],
            'inProjectsAgent' => [
                'enable' => false,
                'query' => [
                    'employee_id' => null,
                    'project_id' => Project::find()->select('id')->andWhere([
                        'closed' => false,
                        'id' => ProjectEmployeeAccess::find()->select('project_id')->andWhere(['employee_id' => $userId])
                    ])
                ]
            ],
            'inProjectsSupervision' => [
                'enable' => false,
                'query' => ['and',
                    [
                        'project_id' => Project::find()->select('id')->andWhere([
                            'closed' => false,
                            'id' => ProjectEmployeeAccess::find()->select('project_id')->andWhere(['employee_id' => $userId])
                        ])
                    ],
                    ['or',
                        [
                            'employee_id' => UserGroupAssign::find()->select('ugs_user_id')->distinct()->andWhere([
                                'ugs_group_id' => UserGroupAssign::find()->select(['ugs_group_id'])->andWhere(['ugs_user_id' => $userId])
                            ])
                        ],
                        [
                            Lead::tableName() . '.id' => ProfitSplit::find()->select('ps_lead_id')->andWhere([
                                'ps_user_id' => UserGroupAssign::find()->select('ugs_user_id')->distinct()->andWhere([
                                    'ugs_group_id' => UserGroupAssign::find()->select(['ugs_group_id'])->andWhere(['ugs_user_id' => $userId])
                                ])
                            ])
                        ]
                    ]
                ]
            ],
            'inGroups' => [
                'enable' => false,
                'query' => [
                    'employee_id' => UserGroupAssign::find()->select('ugs_user_id')->distinct()->andWhere([
                        'ugs_group_id' => UserGroupAssign::find()->select(['ugs_group_id'])->andWhere(['ugs_user_id' => $userId])
                    ])
                ]
            ],
            'inSplit' => [
                'enable' => false,
                'query' => ['or',
                    [Lead::tableName() . '.id' => ProfitSplit::find()->select('ps_lead_id')->andWhere(['ps_user_id' => $userId])],
                    [Lead::tableName() . '.id' => TipsSplit::find()->select('ts_lead_id')->andWhere(['ts_user_id' => $userId])]
                ]
            ]
        ];
    }

    private function createSubQuery(array $conditions)
    {
        $used = false;
        $subQuery = ['or'];
        foreach ($conditions as $condition) {
            if ($condition['enable']) {
                $used = true;
                $subQuery[] = $condition['query'];
            }
        }
        if ($used) {
            return $subQuery;
        }
        return '0 = 1';
    }
}
