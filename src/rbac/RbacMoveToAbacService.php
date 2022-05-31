<?php

namespace src\rbac;

use Yii;

class RbacMoveToAbacService
{
    private const TEMPLATE_SUBJECT = '("{role}" in r.sub.env.user.roles)';
    private const TEMPLATE_SUBJECT_JSON = '{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"{role}"}';
    private const MAIN_TEMPLATE_SUBJECT_JSON = '{"condition":"OR","rules":[{apSubjectJson}],"valid":true}';

    private ?string $apSubject;
    private string $apSubjectJson;

    public function __construct(string $permission)
    {
        $this->getAbacSubjectsByRbacPermission($permission);
    }

    /**
     * @param string $permissionName
     */
    private function getAbacSubjectsByRbacPermission(string $permissionName)
    {
        $roles = self::getRolesByPermission($permissionName);
        $apSubject = $apSubjectJson = [];

        foreach ($roles as $role) {
            $apSubject[] = Yii::t('app', self::TEMPLATE_SUBJECT, ['role' => $role]);
            $apSubjectJson[] = Yii::t('app', self::TEMPLATE_SUBJECT_JSON, ['role' => $role]);
        }
        $this->apSubjectJson = Yii::t('app', self::MAIN_TEMPLATE_SUBJECT_JSON, ['apSubjectJson' => implode(',', $apSubjectJson)]);
        $this->apSubject = implode(' || ', $apSubject);
    }

    /**
     * @param $permissionName
     * @return array
     */
    private function getRolesByPermission(string $permissionName): array
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

    public function getApSubject(): ?string
    {
        return $this->apSubject;
    }

    public function getApSubjectJson(): string
    {
        return $this->apSubjectJson;
    }
}
