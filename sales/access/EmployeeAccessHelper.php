<?php

namespace sales\access;

use common\models\Employee;
use sales\auth\Auth;
use yii\helpers\VarDumper;

class EmployeeAccessHelper
{
    /**
     * @param array $defaultRoles
     * @param array|null $roles
     * @param array $excludeRoles
     * @param array $includeRoles
     * @return array
     */
    public static function getRoles(?array $roles, array $defaultRoles, array $excludeRoles, array $includeRoles): array
    {
        if ($roles === null) {
            return [];
        }

        if (empty($roles)) {
            $roles = $defaultRoles;
        }

        if ($excludeRoles) {
            $roles = array_filter($roles, function ($item) use ($excludeRoles) {
                if (in_array($item, $excludeRoles, false)) {
                    return false;
                }
                return true;
            });
        }

        foreach ($includeRoles as $role) {
            $roles[] = $role;
        }

        return $roles;
    }

    /**
     * @param mixed ...$params
     * @return string
     */
    public static function generateHash(...$params): string
    {
        $hash = '';
        foreach ($params as $param) {
            $hash .= VarDumper::dumpAsString($param);
        }
        return $hash;
    }

    /**
     * @param Employee $user
     * @param array $roles
     * @return bool
     */
    public static function entryInRoles(Employee $user, array $roles): bool
    {
        $result = array_intersect($user->getRoles(true), $roles);
        return !empty($result);
    }
}
