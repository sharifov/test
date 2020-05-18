<?php

use yii\db\Migration;

/**
 * Class m190701_115958_add_role_action_permission
 */
class m190701_115958_add_role_action_permission extends Migration
{
    public $routes = ['/call/ajax-missed-calls', '/call/ajax-call-info'];

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        foreach ($this->routes as $route) {
            $permission = $auth->createPermission($route);
            $auth->add($permission);
            $auth->addChild($auth->getRole('admin'), $permission);
            $auth->addChild($auth->getRole('agent'), $permission);
            $auth->addChild($auth->getRole('supervision'), $permission);
            //$auth->addChild($auth->getRole('qa'), $permission);
            //$auth->addChild($auth->getRole('userManager'), $permission);
        }

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

        foreach ($this->routes as $route) {
            if ($permission = $auth->getPermission($route)) {
                $auth->remove($permission);
            }
        }

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
