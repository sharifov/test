<?php

namespace src\rbac;

use Yii;

class RbacMoveToAbacService
{
    const TEMPLATE_SUBJECT = '("{role}" in r.sub.env.user.roles)';
    const TEMPLATE_SUBJECT_JSON = '{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"{role}"}';
    const MAIN_TEMPLATE_SUBJECT_JSON = '{"condition":"OR","rules":[{apSubjectJson}],"valid":true}';

    /**
     * @param $permissionName
     * @return array
     */
    public static function getAbacSubjectsByRbacPermission($permissionName): array
    {
        $roles = self::getRolesByPermission($permissionName);
        $apSubject = $apSubjectJson = [];

        foreach ($roles as $role) {
            $apSubject[] = Yii::t('app', self::TEMPLATE_SUBJECT, ['role' => $role]);
            $apSubjectJson[] = Yii::t('app', self::TEMPLATE_SUBJECT_JSON, ['role' => $role]);
        }

        return [
            implode(' || ', $apSubject),
            Yii::t('app', self::MAIN_TEMPLATE_SUBJECT_JSON, ['apSubjectJson' =>  implode(',', $apSubjectJson)])
        ];
    }

    /**
     * @param $permissionName
     * @return array
     */
    private static function getRolesByPermission($permissionName): array
    {
        $roleList = [];
        $roles = \Yii::$app->authManager->getRoles();
        foreach ($roles as $role => $value) {
            $permissions = Yii::$app->authManager->getPermissionsByRole($role);
            if (array_key_exists($permissionName, $permissions)) {
                $roleList[$role] = $role;
            }
        }
        return $roleList;
    }
}
