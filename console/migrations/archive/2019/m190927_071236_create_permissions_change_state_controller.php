<?php

use yii\db\Migration;

/**
 * Class m190927_071236_create_permissions_change_state_controller
 */
class m190927_071236_create_permissions_change_state_controller extends Migration
{
    public $routes = [
        '/lead-change-state/take-over',
        '/lead-change-state/validate-take-over',
        '/lead-change-state/follow-up',
        '/lead-change-state/validate-follow-up',
        '/lead-change-state/trash',
        '/lead-change-state/validate-trash',
        '/lead-change-state/snooze',
        '/lead-change-state/validate-snooze',
        '/lead-change-state/return',
        '/lead-change-state/validate-return',
        '/lead-change-state/reject',
        '/lead-change-state/validate-reject',
    ];

    public $roles = [
        'admin', 'agent', 'supervision', 'ex_agent', 'ex_super'
//        'admin', 'agent', 'supervision', 'ex_agent', 'ex_super', 'sup_agent', 'sup_super'
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
