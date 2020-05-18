<?php

use yii\db\Migration;

/**
 * Class m190807_124306_add_permission_for_supervision_for_duplicate
 */
class m190807_124306_add_permission_for_supervision_for_duplicate extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        $routeDuplicate = $auth->getPermission('/lead/duplicate');
        $supervision = $auth->getRole('supervision');
        try {
            $auth->addChild($supervision, $routeDuplicate);
        } catch (\Throwable $e) {
            echo "Can't add child /lead/duplicate for supervision";
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $auth = Yii::$app->authManager;

        $routeDuplicate = $auth->getPermission('/lead/duplicate');
        $supervision = $auth->getRole('supervision');
        $auth->removeChild($supervision, $routeDuplicate);
    }

}
