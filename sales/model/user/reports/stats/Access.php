<?php

namespace sales\model\user\reports\stats;

use common\models\Department;
use common\models\Employee;
use common\models\UserDepartment;
use common\models\UserGroup;
use common\models\UserGroupAssign;
use yii\db\Query;
use yii\rbac\Role;

/**
 * Class Access
 *
 * @property $departmentsLimitedAccess
 * @property $departments
 * @property $groupsLimitedAccess
 * @property $groups
 * @property $usersLimitedAccess
 * @property $users
 * @property $roles
 */
class Access
{
    public bool $departmentsLimitedAccess = true;
    public array $departments = [];

    public bool $groupsLimitedAccess = true;
    public array $groups = [];

    public bool $usersLimitedAccess = true;
    public array $users = [];

    public array $roles = [];

    public function __construct(Employee $user)
    {
        $departments = $user->getUserDepartmentList();
        if (!$departments) {
            $departments = Department::getList();
            $this->departmentsLimitedAccess = false;
        }
        $this->departments = $departments;

        $groups = $user->getUserGroupList();
        if (!$groups) {
            if ($this->departmentsLimitedAccess) {
                $groups = (new Query())
                    ->select(['distinct(ug_name)'])
                    ->from(UserGroupAssign::tableName())
                    ->innerJoin([
                        'dep_users' =>
                            (new Query())
                                ->select(['distinct(ud_user_id)'])
                                ->from(UserDepartment::tableName())
                                ->where(['ud_dep_id' => array_keys($this->departments)])

                    ], 'dep_users.ud_user_id = ugs_user_id')
                    ->innerJoin(UserGroup::tableName(), 'ug_id = ugs_group_id')
                    ->indexBy('ugs_group_id')
                    ->column();
            } else {
                $groups = UserGroup::getList();
                $this->groupsLimitedAccess = false;
            }
        }
        $this->groups = $groups;

        if ($this->groupsLimitedAccess) {
            if ($this->departmentsLimitedAccess) {
                $users = (new Query())
                    ->select(['nickname'])
                    ->from(Employee::tableName())
                    ->innerJoin([
                        'groupsRelation' =>
                            (new Query())
                                ->select(['distinct(ugs_user_id)'])
                                ->from(UserGroupAssign::tableName())
                                ->andWhere(['ugs_group_id' => array_keys($this->groups)])
                    ], 'groupsRelation.ugs_user_id = id')
                    ->innerJoin([
                        'depRelation' =>
                            (new Query())
                                ->select(['distinct(ud_user_id)'])
                                ->from(UserDepartment::tableName())
                                ->andWhere(['ud_dep_id' => array_keys($this->departments)])
                    ], 'depRelation.ud_user_id = id')
                    ->andWhere(['status' => Employee::STATUS_ACTIVE])
                    ->indexBy('id')
                    ->orderBy(['nickname' => SORT_ASC])
                    ->column();
            } else {
                $users = (new Query())
                    ->select(['nickname'])
                    ->from(Employee::tableName())
                    ->innerJoin([
                        'groupsUsers' =>
                            (new Query())
                                ->select(['distinct(ugs_user_id)'])
                                ->from(UserGroupAssign::tableName())
                                ->andWhere(['ugs_group_id' => array_keys($this->groups)])
                    ], 'groupsUsers.ugs_user_id = id')
                    ->andWhere(['status' => Employee::STATUS_ACTIVE])
                    ->indexBy('id')
                    ->orderBy(['nickname' => SORT_ASC])
                    ->column();
            }
        } else {
            if ($this->departmentsLimitedAccess) {
                $users = (new Query())
                    ->select(['nickname'])
                    ->from(Employee::tableName())
                    ->innerJoin([
                        'groupsUsers' =>
                            (new Query())
                                ->select(['distinct(ud_user_id)'])
                                ->from(UserDepartment::tableName())
                                ->andWhere(['ud_dep_id' => array_keys($this->departments)])
                    ], 'groupsUsers.ud_user_id = id')
                    ->andWhere(['status' => Employee::STATUS_ACTIVE])
                    ->indexBy('id')
                    ->orderBy(['nickname' => SORT_ASC])
                    ->column();
            } else {
                $users = Employee::getActiveUsersList();
                $this->usersLimitedAccess = false;
            }
        }
        $this->users = $users;

        $this->roles = array_map(fn(Role $item) => $item->description, \Yii::$app->authManager->getRoles());
    }
}
