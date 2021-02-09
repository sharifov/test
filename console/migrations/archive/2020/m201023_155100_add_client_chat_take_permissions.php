<?php

use sales\rbac\rules\clientChat\take\ClientChatTakeNewRule;
use sales\rbac\rules\clientChat\take\ClientChatTakePendingRule;
use yii\db\Migration;

/**
 * Class m201023_155100_add_client_chat_take_permissions
 */
class m201023_155100_add_client_chat_take_permissions extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        if ($clientChatTakePermission = $auth->getPermission('client-chat/take')) {
            $clientChatTakeNewRule = new ClientChatTakeNewRule();
            $auth->add($clientChatTakeNewRule);
            $clientChatTakeNewPermission = $auth->createPermission('client-chat/take/new');
            $clientChatTakeNewPermission->description = 'Client chat take new';
            $clientChatTakeNewPermission->ruleName = $clientChatTakeNewRule->name;
            $auth->add($clientChatTakeNewPermission);
            $auth->addChild($clientChatTakeNewPermission, $clientChatTakePermission);

            $clientChatTakePendingRule = new ClientChatTakePendingRule();
            $auth->add($clientChatTakePendingRule);
            $clientChatTakePendingPermission = $auth->createPermission('client-chat/take/pending');
            $clientChatTakePendingPermission->description = 'Client chat take pending';
            $clientChatTakePendingPermission->ruleName = $clientChatTakePendingRule->name;
            $auth->add($clientChatTakePendingPermission);
            $auth->addChild($clientChatTakePendingPermission, $clientChatTakePermission);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $auth = Yii::$app->authManager;

        $rules = [
            'ClientChatTakeNewRule',
            'ClientChatTakePendingRule',
        ];

        $permissions = [
            'client-chat/take/new',
            'client-chat/take/pending',
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
