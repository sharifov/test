<?php

use yii\db\Migration;

/**
 * Class m190822_112136_add_permission_for_incoming_call_widget
 */
class m190822_112136_add_permission_for_incoming_call_widget extends Migration
{
    public $routes = [
        '/call/incoming-call-widget'
    ];

    public $roles = [
        'admin', 'agent', 'supervision', 'ex_agent', 'ex_super', 'sup_agent', 'sup_super'
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

            foreach ($this->roles as $role) {
                if (!$auth->hasChild($auth->getRole($role), $permission)) {
                    $auth->addChild($auth->getRole($role), $permission);
                }
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
            foreach ($this->roles as $role) {
                if ($permission = $auth->getPermission($route)) {
                    //$auth->remove($permission);
                    if ($auth->hasChild($auth->getRole($role), $permission)) {
                        $auth->removeChild($auth->getRole($role), $permission);
                    }
                }
            }
        }

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }

    }
}
