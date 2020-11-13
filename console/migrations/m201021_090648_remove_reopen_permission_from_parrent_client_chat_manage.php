<?php

use yii\db\Migration;

/**
 * Class m201021_090648_remove_reopen_permission_from_parrent_client_chat_manage
 */
class m201021_090648_remove_reopen_permission_from_parrent_client_chat_manage extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;
        $clientChatReopenPermission = $auth->getPermission('client-chat/reopen');
        if ($clientChatReopenPermission) {
            $clientChatManagePermission = $auth->getPermission('client-chat/manage');
            if ($clientChatManagePermission) {
                $auth->removeChild($clientChatReopenPermission, $clientChatManagePermission);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    }
}
