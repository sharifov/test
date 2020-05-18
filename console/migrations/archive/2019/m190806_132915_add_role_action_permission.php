<?php

use yii\db\Migration;

/**
 * Class m190806_132915_add_role_action_permission
 */
class m190806_132915_add_role_action_permission extends Migration
{
    public $routes = ['/call/ajax-call-cancel'];

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
