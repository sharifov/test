<?php

namespace sales\access;

class EmployeeAccessHelper
{
    /**
     * @param array $defaultRoles
     * @param array $roles
     * @param array $excludeRoles
     * @param array $includeRoles
     * @return array
     */
    public static function getRoles(array $defaultRoles, array $roles, array $excludeRoles, array $includeRoles): array
    {
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

}
