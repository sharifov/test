<?php

use yii\db\Migration;

/**
 * Class m190811_102235_add__role_access
 */
class m190811_102235_add__role_access extends Migration
{

    public $routes = [
        '/department/*',
        '/department-phone-project/*',
    ];

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $auth = Yii::$app->authManager;



        foreach ($this->routes as $route) {

            $permission = $auth->getPermission($route);
            if(!$permission) {
                $permission = $auth->createPermission($route);
                $auth->add($permission);
            }


            /*if(!$auth->hasChild($auth->getRole('support'), $permission)) {
                $auth->addChild($auth->getRole('support'), $permission);
            }*/

            if(!$auth->hasChild($auth->getRole('admin'), $permission)) {
                $auth->addChild($auth->getRole('admin'), $permission);
            }

            //$auth->addChild($auth->getRole('supervisor'), $permission);
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
