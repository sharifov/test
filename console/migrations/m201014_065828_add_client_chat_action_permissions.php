<?php

use sales\rbac\rules\clientChat\close\ClientChatCloseHoldRule;
use sales\rbac\rules\clientChat\close\ClientChatCloseIdleRule;
use sales\rbac\rules\clientChat\transfer\ClientChatTransferHoldRule;
use sales\rbac\rules\clientChat\transfer\ClientChatTransferIdleRule;
use yii\db\Migration;

/**
 * Class m201014_065828_add_client_chat_action_permissions
 */
class m201014_065828_add_client_chat_action_permissions extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        if ($clientChatClosePermission = $auth->getPermission('client-chat/close')) {
            $clientChatCloseIdleRule = new ClientChatCloseIdleRule();
            $auth->add($clientChatCloseIdleRule);
            $clientChatCloseIdlePermission = $auth->createPermission('client-chat/close/idle');
            $clientChatCloseIdlePermission->description = 'Client chat close idle';
            $clientChatCloseIdlePermission->ruleName = $clientChatCloseIdleRule->name;
            $auth->add($clientChatCloseIdlePermission);
            $auth->addChild($clientChatCloseIdlePermission, $clientChatClosePermission);

            $clientChatCloseHoldRule = new ClientChatCloseHoldRule();
            $auth->add($clientChatCloseHoldRule);
            $clientChatCloseHoldPermission = $auth->createPermission('client-chat/close/hold');
            $clientChatCloseHoldPermission->description = 'Client chat close idle';
            $clientChatCloseHoldPermission->ruleName = $clientChatCloseHoldRule->name;
            $auth->add($clientChatCloseHoldPermission);
            $auth->addChild($clientChatCloseHoldPermission, $clientChatClosePermission);
        }

        if ($clientChatTransferPermission = $auth->getPermission('client-chat/transfer')) {
            $clientChatTransferIdleRule = new ClientChatTransferIdleRule();
            $auth->add($clientChatTransferIdleRule);
            $clientChatTransferIdlePermission = $auth->createPermission('client-chat/transfer/idle');
            $clientChatTransferIdlePermission->description = 'Client chat transfer new';
            $clientChatTransferIdlePermission->ruleName = $clientChatTransferIdleRule->name;
            $auth->add($clientChatTransferIdlePermission);
            $auth->addChild($clientChatTransferIdlePermission, $clientChatTransferPermission);

            $clientChatTransferHoldRule = new ClientChatTransferHoldRule();
            $auth->add($clientChatTransferHoldRule);
            $clientChatTransferHoldPermission = $auth->createPermission('client-chat/transfer/hold');
            $clientChatTransferHoldPermission->description = 'Client chat transfer new';
            $clientChatTransferHoldPermission->ruleName = $clientChatTransferHoldRule->name;
            $auth->add($clientChatTransferHoldPermission);
            $auth->addChild($clientChatTransferHoldPermission, $clientChatTransferPermission);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $auth = Yii::$app->authManager;

        $rules = [
            'ClientChatCloseIdleRule',
            'ClientChatCloseHoldRule',
            'ClientChatTransferIdleRule',
            'ClientChatTransferHoldRule',
        ];

        $permissions = [
            'client-chat/close/idle',
            'client-chat/close/hold',
            'client-chat/transfer/idle',
            'client-chat/transfer/hold',
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
