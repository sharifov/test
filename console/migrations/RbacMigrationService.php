<?php

namespace console\migrations;

use Yii;

class RbacMigrationService
{

    /**
     * @param array $routes
     * @param array $roles
     * @throws \yii\base\Exception
     */
    public function up(array $routes, array $roles): void
    {
        $auth = Yii::$app->authManager;

        foreach ($routes as $route) {

            if (!$permission = $auth->getPermission($route)) {
                $permission = $auth->createPermission($route);
                $auth->add($permission);
            }

            foreach ($roles as $role) {
                if (!$auth->hasChild($auth->getRole($role), $permission)) {
                    $auth->addChild($auth->getRole($role), $permission);
                }
            }
        }

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

    /**
     * @param array $routes
     * @param array $roles
     */
    public function down(array $routes, array $roles): void
    {
        $auth = Yii::$app->authManager;

        foreach ($routes as $route) {
            foreach ($roles as $role) {
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
