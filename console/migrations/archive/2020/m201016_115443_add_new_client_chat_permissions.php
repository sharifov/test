<?php

use sales\rbac\rules\clientChat\manage\ClientChatManageEmptyRule;
use sales\rbac\rules\clientChat\view\ClientChatViewEmptyRule;
use yii\db\Migration;

/**
 * Class m201016_115443_add_new_client_chat_permissions
 */
class m201016_115443_add_new_client_chat_permissions extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        if ($clientChatManagePermission = $auth->getPermission('client-chat/manage')) {
            $clientChatManageAllPermission = $auth->createPermission('client-chat/manage/all');
            $clientChatManageAllPermission->description = 'Client chat manage all';
            $auth->add($clientChatManageAllPermission);
            $auth->addChild($clientChatManageAllPermission, $clientChatManagePermission);

            $clientChatManageEmptyRule = new ClientChatManageEmptyRule();
            $auth->add($clientChatManageEmptyRule);
            $clientChatManageEmptyPermission = $auth->createPermission('client-chat/manage/empty');
            $clientChatManageEmptyPermission->description = 'Client chat manage empty';
            $clientChatManageEmptyPermission->ruleName = $clientChatManageEmptyRule->name;
            $auth->add($clientChatManageEmptyPermission);
            $auth->addChild($clientChatManageEmptyPermission, $clientChatManagePermission);
        }

        if ($clientChatViewPermission = $auth->getPermission('client-chat/view')) {
            $clientChatViewAllPermission = $auth->createPermission('client-chat/view/all');
            $clientChatViewAllPermission->description = 'Client chat view all';
            $auth->add($clientChatViewAllPermission);
            $auth->addChild($clientChatViewAllPermission, $clientChatViewPermission);

            $clientChatViewEmptyRule = new ClientChatViewEmptyRule();
            $auth->add($clientChatViewEmptyRule);
            $clientChatViewEmptyPermission = $auth->createPermission('client-chat/view/empty');
            $clientChatViewEmptyPermission->description = 'Client chat view empty';
            $clientChatViewEmptyPermission->ruleName = $clientChatViewEmptyRule->name;
            $auth->add($clientChatViewEmptyPermission);
            $auth->addChild($clientChatViewEmptyPermission, $clientChatViewPermission);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $auth = Yii::$app->authManager;

        $rules = [
            'ClientChatManageEmptyRule',
            'ClientChatViewEmptyRule',
        ];

        $permissions = [
            'client-chat/manage/all',
            'client-chat/manage/empty',
            'client-chat/view/all',
            'client-chat/view/empty',
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
}
