<?php

use yii\db\Migration;

/**
 * Class m201021_073604_rename_client_chat_close_reopen_permission
 */
class m201021_073604_rename_client_chat_close_reopen_permission extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        $clientChatReopenPermission = $auth->getPermission('client-chat/close/reopen');
        if ($clientChatReopenPermission) {
            $clientChatReopenPermission->name = 'client-chat/reopen';
            $auth->update('client-chat/close/reopen', $clientChatReopenPermission);

            $clientChatClosePermission = $auth->getPermission('client-chat/close');
            $auth->removeChild($clientChatReopenPermission, $clientChatClosePermission);

            $clientChatManagePermission = $auth->getPermission('client-chat/manage');
            if ($clientChatManagePermission) {
                $auth->addChild($clientChatReopenPermission, $clientChatManagePermission);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $auth = Yii::$app->authManager;

        $clientChatReopenPermission = $auth->getPermission('client-chat/reopen');

        if ($clientChatReopenPermission) {
            $clientChatManagePermission = $auth->getPermission('client-chat/manage');
            if ($clientChatManagePermission) {
                $auth->removeChild($clientChatReopenPermission, $clientChatManagePermission);
            }

            $clientChatReopenPermission->name = 'client-chat/close/reopen';
            $auth->update('client-chat/reopen', $clientChatReopenPermission);

            $clientChatClosePermission = $auth->getPermission('client-chat/close');
            $auth->addChild($clientChatReopenPermission, $clientChatClosePermission);
        }
    }
}
