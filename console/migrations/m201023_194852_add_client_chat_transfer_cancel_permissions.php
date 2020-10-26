<?php

use common\models\Employee;
use sales\rbac\rules\clientChat\transferCancel\ClientChatTransferCancelOwnerRule;
use yii\db\Migration;

/**
 * Class m201023_194852_add_client_chat_transfer_cancel_permissions
 */
class m201023_194852_add_client_chat_transfer_cancel_permissions extends Migration
{
    public $roles = [
        Employee::ROLE_SUPER_ADMIN,
        Employee::ROLE_ADMIN,
    ];

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        $cancelTransferPermission = $auth->createPermission('client-chat/transfer_cancel');
        $cancelTransferPermission->description = 'Client chat Cancel Transfer';
        $auth->add($cancelTransferPermission);

        $cancelTransferOwnerRule = new ClientChatTransferCancelOwnerRule();
        $auth->add($cancelTransferOwnerRule);
        $cancelTransferOwnerPermission = $auth->createPermission('client-chat/transfer_cancel/owner');
        $cancelTransferOwnerPermission->description = 'Client Chat Cancel Transfer by owner';
        $cancelTransferOwnerPermission->ruleName = $cancelTransferOwnerRule->name;
        $auth->add($cancelTransferOwnerPermission);
        $auth->addChild($cancelTransferOwnerPermission, $cancelTransferPermission);

        $this->addPermissionsToRole(
            $cancelTransferPermission,
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $auth = Yii::$app->authManager;

        $rules = [
            'ClientChatTransferCancelOwnerRule',
        ];

        $permissions = [
            'client-chat/transfer_cancel',
            'client-chat/transfer_cancel/owner',
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
