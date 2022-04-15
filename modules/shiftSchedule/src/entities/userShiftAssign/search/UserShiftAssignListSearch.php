<?php

namespace modules\shiftSchedule\src\entities\userShiftAssign\search;

use common\models\Employee;
use common\models\ProjectEmployeeAccess;
use common\models\UserGroupAssign;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use modules\shiftSchedule\src\entities\userShiftAssign\UserShiftAssign;

/**
 * Class UserShiftAssignListSearch
 */
class UserShiftAssignListSearch extends Employee
{
    public $userId;
    public $shiftId;
    public $userGroupId;
    public $projectId;

    public function rules(): array
    {
        return [
            [
                [
                    'shiftId', 'userId', 'userGroupId', 'projectId',
                ],
                'integer',
            ],
        ];
    }

    public function search($params): ActiveDataProvider
    {
        $query = Employee::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['username' => SORT_ASC]],
            'pagination' => ['pageSize' => 50],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        if ($this->shiftId) {
            $query->innerJoin(
                UserShiftAssign::tableName(),
                'usa_user_id = employees.id AND usa_sh_id = :shiftId',
                ['shiftId' => $this->shiftId],
            );
        }
        if ($this->userGroupId) {
            $query->innerJoin(
                UserGroupAssign::tableName(),
                'ugs_user_id = employees.id AND ugs_group_id = :groupId',
                ['groupId' => $this->userGroupId]
            );
        }
        if ($this->projectId) {
            $query->innerJoin(
                ProjectEmployeeAccess::tableName(),
                'employee_id = employees.id AND project_id = :projectId',
                ['projectId' => $this->projectId]
            );
        }

        $query->andFilterWhere([
            'employees.id' => $this->userId,
        ]);

        return $dataProvider;
    }
}
