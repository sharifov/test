<?php

use yii\db\Migration;

/**
 * Class m201202_113554_add_permission_client_chat_teams
 */
class m201202_113554_add_permission_client_chat_teams extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;
        $permission = $auth->createPermission('client-chat/dashboard/filter/group/team_chats');
        $auth->add($permission);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $auth = Yii::$app->authManager;
        if ($permission = $auth->getPermission('client-chat/dashboard/filter/group/team_chats')) {
            $auth->remove($permission);
        }
    }
}
