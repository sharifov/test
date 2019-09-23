<?php

namespace sales\access;

use common\models\UserDepartment;
use common\models\Department;
use common\models\Employee;
use sales\helpers\user\UserFinder;
use common\models\UserDepartmentQuery;

class EmployeeDepartmentAccess
{

    /** fot this roles return all departments */
    private static $defaultRolesForViewAllDepartments = [
        Employee::ROLE_SUPER_ADMIN,
        Employee::ROLE_ADMIN,
        Employee::ROLE_QA,
        Employee::ROLE_USER_MANAGER
    ];

    /**
     * @param int|null $userId
     * @param array|null $roles | for this roles return all departments | if null, then return only self departments
     * @param array $excludeRoles
     * @param array $includeRoles
     * @return array
     *
     *   [
     *       1 => 'Department 1'
     *       4 => 'Department 4'
     *       7 => 'Department 7'
     *   ]
     *
     * Ex.
     * $projects = EmployeeDepartmentAccess::getDepartments();
     * $projects = EmployeeDepartmentAccess::getDepartments($userId);
     * $projects = EmployeeDepartmentAccess::getDepartments($userId, null);
     * $projects = EmployeeDepartmentAccess::getDepartments($userId, [], [Employee::ROLE_ADMIN], [Employee::ROLE_AGENT]);
     */
    public static function getDepartments(?int $userId = null, ?array $roles = [], array $excludeRoles = [], array $includeRoles = []): array
    {
        $user = UserFinder::find($userId);

        $hash = EmployeeAccessHelper::generateHash($userId, $roles, $excludeRoles, $includeRoles);
        if (($departments = $user->getDepartmentAccess($hash)) !== null) {
            return $departments;
        }

        $departments = null;

        if ($roles = EmployeeAccessHelper::getRoles($roles, self::$defaultRolesForViewAllDepartments, $excludeRoles, $includeRoles)) {
            foreach ($user->getRoles(true) as $role) {
                if (in_array($role, $roles, false)) {
                    $departments = Department::find()->select(['dep_name', 'dep_id'])
                        ->orderBy('dep_name')->indexBy('dep_id')->asArray()->column();
                    break;
                }
            }
        }

        if ($departments === null) {
            $departments = Department::find()->select(['dep_name', 'dep_id'])->andWhere(['dep_id' => self::getDepartmentsSubQuery($user->id)])
                ->orderBy('dep_name')->indexBy('dep_id')->asArray()->column();
        }

        $user->setDepartmentAccess($departments, $hash);
        return $departments;
    }

    /**
     * @param int $userId
     * @return UserDepartmentQuery
     *
     * Ex:
     * $cases = Cases::find()->andWhere(['cs_dep_id' => EmployeeDepartmentAccess::getDepartmentsSubQuery($userId)])->all();
     */
    public static function getDepartmentsSubQuery(int $userId): UserDepartmentQuery
    {
        return UserDepartment::find()->depsByUser($userId);
    }

    /**
     * @param int $departmentId
     * @param int|null $userId
     * @param array|null $roles
     * @param array $excludeRoles
     * @param array $includeRoles
     * @return bool
     *
     * Ex.
     * EmployeeDepartmentAccess::isInDepartment($departmentId)
     * EmployeeDepartmentAccess::isInDepartment($departmentId, $userId)
     */
    public static function isInDepartment(int $departmentId, ?int $userId = null, ?array $roles = [], array $excludeRoles = [], array $includeRoles = []): bool
    {
        foreach (self::getDepartments($userId, $roles, $excludeRoles, $includeRoles) as $key => $department) {
            if ($key === $departmentId) {
                return true;
            }
        }
        return false;
    }

}
