<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use sales\rbac\rules\clientChat\close\ClientChatCloseReopenRule;
use yii\db\Migration;

/**
 * Class m201014_065903_add_permission_for_reopen_client_chat
 */
class m201014_065903_add_permission_for_reopen_client_chat extends Migration
{
    public $roles = [
        Employee::ROLE_SUPER_ADMIN,
        Employee::ROLE_ADMIN,
    ];

    public $route = [
        '/client-chat/ajax-reopen-chat'
    ];

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        $clientChatClosePermission = $auth->getPermission('client-chat/close');

        $clientChatCloseReopenRule = new ClientChatCloseReopenRule();
        $auth->add($clientChatCloseReopenRule);
        $clientChatReopenPermission = $auth->createPermission('client-chat/close/reopen');
        $clientChatReopenPermission->description = 'Reopen closed chat';
        $clientChatReopenPermission->ruleName = $clientChatCloseReopenRule->name;
        $auth->add($clientChatReopenPermission);
        $auth->addChild($clientChatReopenPermission, $clientChatClosePermission);

        $this->addPermissionsToRole($clientChatReopenPermission);

        (new RbacMigrationService())->up($this->route, $this->roles);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $auth = Yii::$app->authManager;

        $rules = [
            'ClientChatCloseReopenRule',
        ];

        $permissions = [
            'client-chat/close/reopen',
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

        (new RbacMigrationService())->down($this->route, $this->roles);
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
