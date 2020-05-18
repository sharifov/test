<?php

use common\models\Employee;
use yii\db\Migration;

/**
 * Class m190926_055240_add_permission_dashboard
 */
class m190926_055240_add_permission_dashboard extends Migration
{
    public $routes = [
        '/dashboard/index'
    ];

    public $roles = [
        Employee::ROLE_ADMIN,
        Employee::ROLE_SUPER_ADMIN,
        Employee::ROLE_AGENT, Employee::ROLE_SUPERVISION,
        Employee::ROLE_EX_AGENT, Employee::ROLE_EX_SUPER,
        Employee::ROLE_SUP_AGENT, Employee::ROLE_SUP_SUPER,
        Employee::ROLE_QA,
        Employee::ROLE_USER_MANAGER
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
