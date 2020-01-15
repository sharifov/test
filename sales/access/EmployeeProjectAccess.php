<?php

namespace sales\access;

use common\models\query\ProjectQuery;
use common\models\ProjectEmployeeAccess;
use common\models\Employee;
use common\models\Project;
use sales\helpers\user\UserFinder;

class EmployeeProjectAccess
{

    /** fot this roles return all projects */
    private static $defaultRolesForViewAllProjects = [
        Employee::ROLE_SUPER_ADMIN,
        Employee::ROLE_ADMIN,
        Employee::ROLE_SALES_SENIOR,
        Employee::ROLE_QA,
        Employee::ROLE_USER_MANAGER
    ];

    /**
     * @param int|Employee|null $user
     * @param array|null $roles | for this roles return all projects | if null, then return only self projects
     * @param array $excludeRoles
     * @param array $includeRoles
     * @return array
     *
     *   [
     *       1 => 'Project 1'
     *       8 => 'Project 8'
     *       9 => 'Project 9'
     *   ]
     *
     * Ex.
     * $projects = EmployeeProjectAccess::getProjects();
     * $projects = EmployeeProjectAccess::getProjects($userId);
     * $projects = EmployeeProjectAccess::getProjects($userId, null);
     * $projects = EmployeeProjectAccess::getProjects($userId, [], [Employee::ROLE_ADMIN], [Employee::ROLE_AGENT]);
     */
    public static function getProjects($user = null, ?array $roles = [], array $excludeRoles = [], array $includeRoles = []): array
    {
        $user = UserFinder::getOrFind($user);

        $hash = EmployeeAccessHelper::generateHash($user->id, $roles, $excludeRoles, $includeRoles);
        if (($projects = $user->getProjectAccess($hash)) !== null) {
            return $projects;
        }

        $projects = null;

        if ($roles = EmployeeAccessHelper::getRoles($roles, self::$defaultRolesForViewAllProjects, $excludeRoles, $includeRoles)) {
            foreach ($user->getRoles(true) as $role) {
                if (in_array($role, $roles, false)) {
                    $projects = Project::find()->select(['name', 'id'])->active()
                        ->orderBy('name')->indexBy('id')->asArray()->column();
                    break;
                }
            }
        }

        if ($projects === null) {
            $projects = Project::find()->select(['name', 'id'])
                ->andWhere(['id' => self::getProjectsSubQuery($user->id)])
                ->orderBy('name')->indexBy('id')->asArray()->column();
        }

        $user->setProjectAccess($projects, $hash);
        return $projects;
    }

    /**
     * @param int $userId
     * @param string|null $projectAlias
     * @param string|null $projectEmployeeAccessAlias
     * @return ProjectQuery
     *
     * Ex:
     * $cases = Cases::find()->andWhere(['cs_project_id' => EmployeeProjectAccess::getProjectsSubQuery($userId)])->all();
     */
    public static function getProjectsSubQuery(int $userId, string $projectAlias = null, string $projectEmployeeAccessAlias = null): ProjectQuery
    {
        $projectAlias = $projectAlias ?: Project::tableName();
        $projectEmployeeAccessAlias = $projectEmployeeAccessAlias ?: ProjectEmployeeAccess::tableName();

        return Project::find()
            ->select($projectAlias . '.id')->active()->andWhere([
                $projectAlias . '.id' => ProjectEmployeeAccess::find()
                    ->select($projectEmployeeAccessAlias . '.project_id')
                    ->andWhere([$projectEmployeeAccessAlias . '.employee_id' => $userId])
            ]);
    }

    /**
     * @param int $projectId
     * @param int|null $userId
     * @param array|null $roles
     * @param array $excludeRoles
     * @param array $includeRoles
     * @return bool
     *
     * Ex.
     * EmployeeProjectAccess::isInProject($projectId)
     * EmployeeProjectAccess::isInProject($projectId, $userId)
     */
    public static function isInProject(int $projectId, ?int $userId = null, ?array $roles = [], array $excludeRoles = [], array $includeRoles = []): bool
    {
        foreach (self::getProjects($userId, $roles, $excludeRoles, $includeRoles) as $key => $project) {
            if ($key === $projectId) {
                return true;
            }
        }
        return false;
    }

}
