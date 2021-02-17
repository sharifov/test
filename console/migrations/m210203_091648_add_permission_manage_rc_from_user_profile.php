<?php

use yii\db\Migration;

/**
 * Class m210203_091648_add_permission_manage_rc_from_user_profile
 */
class m210203_091648_add_permission_manage_rc_from_user_profile extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;
        $rcCredentialsBlock = $auth->createPermission('employee/update_RcCredentialsBlock');
        $rcCredentialsBlock->description = 'Employee update page. Show RC Credentials block';
        $auth->add($rcCredentialsBlock);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $auth = Yii::$app->authManager;
        if ($rcCredentialsBlock = $auth->getPermission('employee/update_RcCredentialsBlock')) {
            $auth->remove($rcCredentialsBlock);
        }
    }
}
