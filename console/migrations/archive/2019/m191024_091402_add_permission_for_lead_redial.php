<?php

use yii\db\Migration;

/**
 * Class m191024_091402_add_permission_for_lead_redial
 */
class m191024_091402_add_permission_for_lead_redial extends Migration
{
    public $routes = [
        '/lead-redial/show',
    ];

    public $roles = [
        'admin'//, 'agent', 'supervision', 'ex_agent', 'ex_super', 'qa'
    ];

    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        foreach ($this->routes as $route) {

            if (!$permission = $auth->getPermission($route)) {
                $permission = $auth->createPermission($route);
                $auth->add($permission);
            }

            foreach ($this->roles as $role) {
                if (!$auth->hasChild($auth->getRole($role), $permission)) {
                    $auth->addChild($auth->getRole($role), $permission);
                }
            }
        }

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }

    }

    public function safeDown()
    {
        $auth = Yii::$app->authManager;

        foreach ($this->routes as $route) {
            foreach ($this->roles as $role) {
                if ($permission = $auth->getPermission($route)) {
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
