<?php

use sales\rbac\rules\clientChat\close\ClientChatCloseReopenRule;
use yii\db\Migration;

/**
 * Class m201026_063130_remove_client_chat_reopen_rule
 */
class m201026_063130_remove_client_chat_reopen_rule extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        if ($permission = $auth->getPermission('client-chat/reopen')) {
            $permission->description = 'Reopen chat';
            $auth->update('client-chat/reopen', $permission);
        }

        if ($rule = $auth->getRule('ClientChatCloseReopenRule')) {
            $auth->remove($rule);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $auth = Yii::$app->authManager;

        if ($permission = $auth->getPermission('client-chat/reopen')) {
            if (!$rule = $auth->getRule('ClientChatCloseReopenRule')) {
                $rule = new ClientChatCloseReopenRule();
                $auth->add($rule);
            }
            $permission->ruleName = $rule->name;
            $permission->description = 'Reopen closed chat';
            $auth->update('client-chat/reopen', $permission);
        }
    }
}
