<?php

namespace sales\access;

use common\models\Sources;

class EmployeeSourceAccess
{
    /**
     * @param int|null $userId
     * @param array|null $roles | for this roles return all projects | if null, then return only self projects
     * @param array $excludeRoles
     * @param array $includeRoles
     * @return Sources[]
     *
     * Ex.
     * $projects = EmployeeSourceAccess::getSources();
     * $projects = EmployeeSourceAccess::getSources($userId);
     * $projects = EmployeeSourceAccess::getSources($userId, null);
     * $projects = EmployeeSourceAccess::getSources($userId, [], [Employee::ROLE_ADMIN], [Employee::ROLE_AGENT]);
     */
    public static function getSources(?int $userId = null, ?array $roles = [], array $excludeRoles = [], array $includeRoles = []): array
    {
        return Sources::find()
            ->active()
            ->andWhere(['project_id' => array_keys(EmployeeProjectAccess::getProjects($userId, $roles, $excludeRoles, $includeRoles))])
            ->orderBy('name')
            ->indexBy('id')
            ->all();
    }

    /**
     * @param int $sourceId
     * @param int|null $userId
     * @param array|null $roles
     * @param array $excludeRoles
     * @param array $includeRoles
     * @return bool
     *
     * Ex.
     * EmployeeSourceAccess::isInSource($sourceId)
     * EmployeeSourceAccess::isInSource($sourceId, $userId)
     */
    public static function isInSource(int $sourceId, ?int $userId = null, ?array $roles = [], array $excludeRoles = [], array $includeRoles = []): bool
    {
        foreach (self::getSources($userId, $roles, $excludeRoles, $includeRoles) as $key => $source) {
            if ($key === $sourceId) {
                return true;
            }
        }
        return false;
    }

}
