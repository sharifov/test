<?php

namespace sales\repositories\call;

use common\models\Call;
use common\models\Employee;
use common\models\Project;
use common\models\ProjectEmployeeAccess;
use common\models\UserGroupAssign;
use yii\db\ActiveQuery;

class CallSearchRepository
{

    /**
     * @param Employee $user
     * @return ActiveQuery
     */
    public function getSearchQuery($user): ActiveQuery
    {
        $query = Call::find()->with('cCreatedUser', 'cProject');

        if ($user->isAdmin()) {
            return $query;
        }

        $conditions = [];

        if ($user->isSupervision() || $user->isExSuper() || $user->isSupSuper()) {
            $conditions = [
                'c_created_user_id' => $this->usersIdsInCommonGroups($user->id),
            ];
            $query->andWhere(['IS NOT', 'c_parent_id', null]);
        }

        if ($user->isQa()) {
            $conditions = [
                'c_created_user_id' => $this->usersIdsInCommonGroups($user->id)
            ];
        }

        $query->andWhere($this->createSubQuery($user->id, $conditions));

        return $query;
    }

    /**
     * @param $userId
     * @return array
     */
    private function isOwner($userId): array
    {
        return ['c_created_user_id' => $userId];
    }

    /**
     * @param $userId
     * @return array
     */
    private function inProject($userId): array
    {
        return [
            'c_project_id' => Project::find()->select(Project::tableName() . '.id')->andWhere([
                'closed' => false,
                'id' => ProjectEmployeeAccess::find()->select(ProjectEmployeeAccess::tableName() . '.project_id')->andWhere([ProjectEmployeeAccess::tableName() . '.employee_id' => $userId])
            ])
        ];
    }

    /**
     * @param $userId
     * @return ActiveQuery
     */
    private function usersIdsInCommonGroups($userId): ActiveQuery
    {
        return UserGroupAssign::find()->select(['ugs_user_id'])->distinct('ugs_user_id')->andWhere([
            'ugs_group_id' => UserGroupAssign::find()->select(['ugs_group_id'])->andWhere(['ugs_user_id' => $userId])
        ]);
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