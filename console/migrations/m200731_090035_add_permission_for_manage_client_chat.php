<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use sales\rbac\rules\globalRules\clientChat\ClientChatOwnerRule;
use sales\rbac\rules\globalRules\clientChat\ClientChatIsOwnerMyGroupRule;
use yii\db\Migration;

/**
 * Class m200731_090035_add_permission_for_manage_client_chat
 */
class m200731_090035_add_permission_for_manage_client_chat extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
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

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $auth = Yii::$app->authManager;

        (new RbacMigrationService())->down([
            'global/client-chat/isOwnerMyGroup',
            'global/client-chat/isOwner'
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
}
