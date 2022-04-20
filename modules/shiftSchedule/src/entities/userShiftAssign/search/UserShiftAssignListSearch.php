<?php

namespace modules\shiftSchedule\src\entities\userShiftAssign\search;

use common\models\Employee;
use common\models\ProjectEmployeeAccess;
use common\models\UserGroupAssign;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use modules\shiftSchedule\src\entities\userShiftAssign\UserShiftAssign;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * Class UserShiftAssignListSearch
 */
class UserShiftAssignListSearch extends Employee
{
    public $userId;
    public $shiftId;
    public $userGroupId;
    public $projectId;
    public $role;

    public function rules(): array
    {
        return [
            [['shiftId', 'userId', 'userGroupId', 'projectId'], 'integer'],
            [['role'], 'string'],
        ];
    }

    /**
     * @param $params
     * @return ActiveDataProvider
     */
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
        $this->filterQuery($query);

        return $dataProvider;
    }

    public function searchIds($params): array
    {
        $query = static::find();

        $this->load($params);
        if (!$this->validate()) {
            $query->where('0=1');
            return [];
        }
        $query->select('id');
        $query = $this->filterQuery($query);

        return ArrayHelper::map($query->asArray()->all(), 'id', 'id');
    }

    private function filterQuery(Query $query): Query
    {
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
        if ($this->role) {
            $query->andWhere(['IN', 'employees.id', array_keys(Employee::getListByRole($this->role))]);
        }
        $query->andFilterWhere([
            'employees.id' => $this->userId,
        ]);
        return $query;
    }
}
