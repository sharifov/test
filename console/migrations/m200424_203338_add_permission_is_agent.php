<?php

use yii\db\Migration;

/**
 * Class m200424_203338_add_permission_is_agent
 */
class m200424_203338_add_permission_is_agent extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        $isAgent = $auth->createPermission('isAgent');
        $isAgent->description = 'Is Agent';
        $auth->add($isAgent);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $auth = Yii::$app->authManager;

        if ($isAgent = $auth->getPermission('isAgent')) {
            $auth->remove($isAgent);
        }
    }
}
