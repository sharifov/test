<?php

use yii\db\Migration;

/**
 * Class m190628_075842_add_role_action_permission
 */
class m190628_075842_add_role_action_permission extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;


        $permission = $auth->createPermission('/notifications/pjax-notify');
        $auth->add($permission);
        $auth->addChild($auth->getRole('admin'), $permission);
        $auth->addChild($auth->getRole('agent'), $permission);
        $auth->addChild($auth->getRole('supervision'), $permission);
        $auth->addChild($auth->getRole('qa'), $permission);
        $auth->addChild($auth->getRole('userManager'), $permission);

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $auth = Yii::$app->authManager;

        if ($permission = $auth->getPermission('/notifications/pjax-notify')) {
            $auth->remove($permission);
        }

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
