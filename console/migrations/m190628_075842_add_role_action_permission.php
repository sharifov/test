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

        $admin = $auth->getRole('admin');
        $agent = $auth->getRole('agent');
        $supervision = $auth->getRole('supervision');

        $permission = $auth->createPermission('/notifications/pjax-notify');
        $auth->add($permission);
        $auth->addChild($admin, $permission);
        $auth->addChild($agent, $permission);
        $auth->addChild($supervision, $permission);

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
