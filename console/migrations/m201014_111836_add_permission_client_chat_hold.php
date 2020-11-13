<?php

use common\models\Employee;
use sales\rbac\rules\clientChat\hold\ClientChatHoldRule;
use sales\rbac\rules\clientChat\hold\ClientChatUnHoldRule;
use yii\db\Migration;

/**
 * Class m201014_111836_add_permission_client_chat_hold
 */
class m201014_111836_add_permission_client_chat_hold extends Migration
{
    private $roles = [
        Employee::ROLE_ADMIN,
        Employee::ROLE_SUPER_ADMIN,
    ];

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        $holdRule = new ClientChatHoldRule();
        $auth->add($holdRule);
        $holdPermission = $auth->createPermission('client-chat/hold');
        $holdPermission->description = 'Client chat hold';
        $holdPermission->ruleName = $holdRule->name;
        $auth->add($holdPermission);

        $unHoldRule = new ClientChatUnHoldRule();
        $auth->add($unHoldRule);
        $unHoldPermission = $auth->createPermission('client-chat/un_hold');
        $unHoldPermission->description = 'Client chat un hold';
        $unHoldPermission->ruleName = $unHoldRule->name;
        $auth->add($unHoldPermission);

        $this->addPermissionsToRole($holdPermission, $unHoldPermission);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $auth = Yii::$app->authManager;

        $rules = [
            'ClientChatHoldRule',
            'ClientChatUnHoldRule',
        ];

        $permissions = [
            'client-chat/hold',
            'client-chat/un_hold',
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
