<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use src\rbac\rules\globalRules\clientChat\ClientChatIsOwnerMyGroupRule;
use src\rbac\rules\globalRules\clientChat\ClientChatOwnerRule;
use yii\db\Migration;

/**
 * Class m201016_113709_remove_old_manage_client_chat_permissions
 */
class m201016_113709_remove_old_manage_client_chat_permissions extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        $rules = [
            'ClientChatOwnerRule',
            'ClientChatIsOwnerMyGroupRule'
        ];
        $permissions = [
            'global/client-chat/isOwnerMyGroup',
            'global/client-chat/isOwner',
            'client-chat/manage/all',
        ];

        foreach ($rules as $ruleName) {
            if ($rule = $auth->getRule($ruleName)) {
                $auth->remove($rule);
            }
        }

        foreach ($permissions as $permissionName) {
            if ($permission = $auth->getPermission($permissionName)) {
                $auth->remove($permission);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $auth = Yii::$app->authManager;

        $clientChatManagePermission = $auth->createPermission('client-chat/manage/all');
        $clientChatManagePermission->description = 'Client Chat Manage Access';
        $auth->add($clientChatManagePermission);

        $clientChatOwnerRule = new ClientChatOwnerRule();
        $auth->add($clientChatOwnerRule);
        $clientChatIsOwnerPermission = $auth->createPermission('global/client-chat/isOwner');
        $clientChatIsOwnerPermission->description = 'Client chat is Owner';
        $clientChatIsOwnerPermission->ruleName = $clientChatOwnerRule->name;
        $auth->add($clientChatIsOwnerPermission);
        $auth->addChild($clientChatIsOwnerPermission, $clientChatManagePermission);

        $ClientChatIsOwnerMyGroupRule = new ClientChatIsOwnerMyGroupRule();
        $auth->add($ClientChatIsOwnerMyGroupRule);
        $clientChatUserHasRolePermission = $auth->createPermission('global/client-chat/isOwnerMyGroup');
        $clientChatUserHasRolePermission->description = 'Client chat is user has the same user group as chat owner';
        $clientChatUserHasRolePermission->ruleName = $ClientChatIsOwnerMyGroupRule->name;
        $auth->add($clientChatUserHasRolePermission);
        $auth->addChild($clientChatUserHasRolePermission, $clientChatManagePermission);

        (new RbacMigrationService())->up([
            'global/client-chat/isOwnerMyGroup',
            'global/client-chat/isOwner',
        ], [
            Employee::ROLE_SUPER_ADMIN,
            Employee::ROLE_ADMIN,
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
        ]);
    }
}
