<?php

use yii\db\Migration;

/**
 * Class m190531_092843_add_access_for_qa_calls_and_sms_page
 */
class m190531_092843_add_access_for_qa_calls_and_sms_page extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;
        if (($permission = $auth->getPermission('/stats/call-sms')) && ($role = $auth->getRole('qa'))) {
            $auth->addChild($role, $permission);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $auth = Yii::$app->authManager;
        if (($permission = $auth->getPermission('/stats/call-sms')) && ($role = $auth->getRole('qa'))) {
            $auth->removeChild($role, $permission);
        }
    }
}
