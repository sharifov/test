<?php

use common\models\Employee;
use yii\db\Migration;

/**
 * Class m201109_085013_assign_email_view_permissions
 */
class m201109_085013_assign_email_view_permissions extends Migration
{
    private const ADMIN_ROLES = [
        Employee::ROLE_ADMIN,
        Employee::ROLE_SUPER_ADMIN,
    ];

    private const SIMPLE_ROLES = [
        Employee::ROLE_AGENT,
        Employee::ROLE_SUPERVISION,
        Employee::ROLE_QA,
        Employee::ROLE_QA_SUPER,
        Employee::ROLE_USER_MANAGER,
        Employee::ROLE_SUP_AGENT,
        Employee::ROLE_SUP_SUPER,
        Employee::ROLE_EX_AGENT,
        Employee::ROLE_EX_SUPER,
        Employee::ROLE_SALES_SENIOR,
        Employee::ROLE_EXCHANGE_SENIOR,
        Employee::ROLE_SUPPORT_SENIOR,
    ];

    private const ADMIN_PERMISSIONS = [
        'email/view/all',
    ];

    private const SIMPLE_PERMISSIONS = [
        'email/view/owner',
        'email/view/address_owner',
        'email/view/lead_owner',
        'email/view/case_owner',
    ];

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        $adminPermissions = [];
        foreach (self::ADMIN_PERMISSIONS as $item) {
            if ($permission = $auth->getPermission($item)) {
                $adminPermissions[] = $permission;
            }
        }
        $this->addPermissionsToAdminRole(...$adminPermissions);

        $simplePermissions = [];
        foreach (self::SIMPLE_PERMISSIONS as $item) {
            if ($permission = $auth->getPermission($item)) {
                $simplePermissions[] = $permission;
            }
        }
        $this->addPermissionsToSimpleRole(...$simplePermissions);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $auth = Yii::$app->authManager;

        $adminPermissions = [];
        foreach (self::ADMIN_PERMISSIONS as $item) {
            if ($permission = $auth->getPermission($item)) {
                $adminPermissions[] = $permission;
            }
        }
        $this->removePermissionsFromAdminRole(...$adminPermissions);

        $simplePermissions = [];
        foreach (self::SIMPLE_PERMISSIONS as $item) {
            if ($permission = $auth->getPermission($item)) {
                $simplePermissions[] = $permission;
            }
        }
        $this->removePermissionsFromSimpleRole(...$simplePermissions);
    }

    private function addPermissionsToAdminRole(...$permissions)
    {
        $auth = Yii::$app->authManager;

        foreach (self::ADMIN_ROLES as $item) {
            if (!$role = $auth->getRole($item)) {
                continue;
            }
            foreach ($permissions as $permission) {
                $auth->addChild($role, $permission);
            }
        }
    }

    private function addPermissionsToSimpleRole(...$permissions)
    {
        $auth = Yii::$app->authManager;

        foreach (self::SIMPLE_ROLES as $item) {
            if (!$role = $auth->getRole($item)) {
                continue;
            }
            foreach ($permissions as $permission) {
                $auth->addChild($role, $permission);
            }
        }
    }

    private function removePermissionsFromSimpleRole(...$permissions)
    {
        $auth = Yii::$app->authManager;

        foreach (self::SIMPLE_ROLES as $item) {
            if (!$role = $auth->getRole($item)) {
                continue;
            }
            foreach ($permissions as $permission) {
                if ($auth->hasChild($role, $permission)) {
                    $auth->removeChild($role, $permission);
                }
            }
        }
    }

    private function removePermissionsFromAdminRole(...$permissions)
    {
        $auth = Yii::$app->authManager;

        foreach (self::ADMIN_ROLES as $item) {
            if (!$role = $auth->getRole($item)) {
                continue;
            }
            foreach ($permissions as $permission) {
                if ($auth->hasChild($role, $permission)) {
                    $auth->removeChild($role, $permission);
                }
            }
        }
    }

}
