<?php

use yii\db\Migration;

/**
 * Class m201023_183345_remove_client_chat_rules
 */
class m201023_183345_remove_client_chat_rules extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        if ($rule = $auth->getRule('ClientChatTakeRule')) {
            $auth->remove($rule);
        }

        if ($rule = $auth->getRule('ClientChatHoldRule')) {
            $auth->remove($rule);
        }

        if ($rule = $auth->getRule('ClientChatUnHoldRule')) {
            $auth->remove($rule);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

    }
}
