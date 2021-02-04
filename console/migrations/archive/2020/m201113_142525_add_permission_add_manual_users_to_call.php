<?php

use common\models\Employee;
use sales\rbac\rules\call\AssignUsersToCallRule;
use yii\db\Migration;

/**
 * Class m201113_142525_add_permission_add_manual_users_to_call
 */
class m201113_142525_add_permission_add_manual_users_to_call extends Migration
{
    private array $roles = [
        Employee::ROLE_ADMIN,
        Employee::ROLE_SUPER_ADMIN,
    ];

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        $rule = new AssignUsersToCallRule();
        $auth->add($rule);
        $permission = $auth->createPermission('call/assignUsers');
        $permission->description = 'Assign users to call';
        $permission->ruleName = $rule->name;
        $auth->add($permission);

        $this->addPermissionsToRole($permission);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $auth = Yii::$app->authManager;

        $rules = [
            'AssignUsersToCallRule',
        ];

        $permissions = [
            'call/assignUsers',
        ];

        foreach ($permissions as $permissionName) {
            if ($permission = $auth->getPermission($permissionName)) {
                $auth->remove($permission);
            }
        }

        foreach ($rules as $ruleName) {
            if ($rule = $auth->getRule($ruleName)) {
                $auth->remove($rule);
            }
        }
    }

    private function addPermissionsToRole(...$permissions)
    {
        $auth = Yii::$app->authManager;

        foreach ($this->roles as $item) {
            if (!$role = $auth->getRole($item)) {
                continue;
            }
            foreach ($permissions as $permission) {
                $auth->addChild($role, $permission);
            }
        }
    }
}
