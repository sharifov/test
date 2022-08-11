<?php

namespace common\models\query;

use common\models\Employee;
use common\models\UserGroup;
use common\models\UserGroupAssign;
use modules\shiftSchedule\src\entities\userShiftAssign\UserShiftAssign;
use modules\shiftSchedule\src\entities\userShiftSchedule\search\TimelineCalendarFilter;
use modules\shiftSchedule\src\entities\userShiftSchedule\UserShiftSchedule;
use yii\helpers\ArrayHelper;

/**
 * Class UserGroupQuery
 *
 * @see UserGroup
 */
class UserGroupQuery extends \yii\db\ActiveQuery
{
    public function enabled(): self
    {
        return $this->andWhere(['ug_disable' => 0]);
    }

    public static function getList(): array
    {
        $data = UserGroup::find()
            ->enabled()
            ->orderBy(['ug_name' => SORT_ASC])
            ->asArray()
            ->all();
        return ArrayHelper::map($data, 'ug_id', 'ug_name');
    }

    public static function getListByUser(int $id): array
    {
        $data = UserGroup::find()
            ->join('inner join', UserGroupAssign::tableName(), 'ug_id = ugs_group_id')
            ->enabled()
            ->andWhere(['ugs_user_id' => $id])
            ->orderBy(['ug_name' => SORT_ASC])
            ->asArray()
            ->all();
        return ArrayHelper::map($data, 'ug_id', 'ug_name');
    }

    /**
     * @param int $userId
     * @param array $groupIds
     * @return UserGroup[]
     */
    public static function findUserGroups(?int $userId, array $groupIds = []): array
    {
        $query = UserGroup::find()
            ->join('inner join', UserGroupAssign::tableName(), 'ug_id = ugs_group_id')
            ->enabled()
            ->orderBy(['ug_name' => SORT_ASC]);
        if ($userId) {
            $query->andWhere(['ugs_user_id' => $userId]);
        }
        if ($groupIds) {
            $query->andWhere(['ug_id' => $groupIds]);
        }
        return $query->all();
    }

    /**
     * @param TimelineCalendarFilter $form
     * @return UserGroup[]
     * @throws \yii\db\Exception
     */
    public static function findUserGroupsAndAssignedUsers(TimelineCalendarFilter $form): array
    {
        $query = UserGroup::find()
            ->select(['ug_id', 'ug_key', 'ug_name', 'ugs_user_id', 'username', 'email'])
            ->innerJoin(UserGroupAssign::tableName(), 'ug_id = ugs_group_id')
            ->innerJoin(Employee::tableName(), 'ugs_user_id = id')
            ->andWhere(['<>', 'status', Employee::STATUS_DELETED])
            ->enabled()
            ->orderBy(['ug_name' => SORT_ASC]);
        if ($form->userGroups) {
            $query->andWhere(['ug_id' => $form->userGroups]);
        }
        if ($form->usersIds) {
            $query->andWhere(['ugs_user_id' => $form->usersIds]);
        }
        if ($form->userShift) {
            $query->andWhere(['ugs_user_id' => UserShiftAssign::find()->select('usa_user_id')->andWhere(['usa_sh_id' => $form->userShift])]);
        }
        if (!$form->displayUsersWithoutEvents) {
            $query->innerJoin(UserShiftSchedule::tableName(), 'uss_user_id = id');
            if ($form->startDateTime && $form->startDateTimeCondition) {
                $query->andWhere([$form->getStartDateTimeConditionOperator(), 'uss_start_utc_dt', date('Y-m-d H:i', strtotime($form->startDateTime))]);
            } else {
                $query->andWhere(['>=', 'uss_start_utc_dt', date('Y-m-d H:i:s', strtotime($form->startDate))]);
            }
            if ($form->endDateTime && $form->endDateTimeCondition) {
                $query->andWhere([$form->getEndDateTimeConditionOperator(), 'uss_end_utc_dt', date('Y-m-d H:i', strtotime($form->endDateTime))]);
            } else {
                $query->andWhere(['<=', 'uss_start_utc_dt', date('Y-m-d 23:59:59', strtotime($form->endDate))]);
            }
            $query->groupBy(['ug_id', 'ugs_user_id', 'ug_name']);
        }
        return $query->asArray()->createCommand()->queryAll();
    }
}
