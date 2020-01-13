<?php

namespace console\migrations;

use Yii;
use yii\rbac\Role;
use yii2mod\rbac\models\RouteModel;

class RbacMigrationService
{

    private $allRoutes = [];
    private $auth;

    /**
     * @param array $routes
     * @param array $roles
     * @throws \yii\base\Exception
     */
    public function up(array $routes, array $roles): void
    {
        $auth = $this->getAuth();

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
        $auth = $this->getAuth();

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

    public function getAllGroupByRole(Role $role): array
    {
        $auth = $this->getAuth();

        $routes = [];
        $permissions = $auth->getPermissionsByRole($role->name);
        foreach (array_keys($permissions) as $permission) {
            if (strpos($permission, '/*') !== false) {
                $routes[] = $permission;
            }
        }
        return $routes;
    }

    public function getOrCreatePermission(string $name): \yii\rbac\Permission
    {
        $auth = $this->getAuth();

        if ($permission = $auth->getPermission($name)) {
            return $permission;
        }

        $permission = $auth->createPermission($name);
        $auth->add($permission);
        return $permission;
    }

    /**
     * @param string $group example: /controller/*
     * @return array
     * [
     *      /controller/index,
     *      /controller/view,
     *      /controller/update,
     *      /controller/update,
     * ]
     */
    public function getAllRoutesByGroup(string $group): array
    {
        $group = substr($group, 0, (strlen($group)-1));
        $routes = [];
        foreach ($this->getAllRoutes() as $route) {
            if (($route !== $group . '*') && strpos($route, $group) === 0) {
                $routes[] = $route;
            }
        }
        return $routes;
    }

    public function getAllRoutes(): array
    {
        if ($this->allRoutes) {
            return $this->allRoutes;
        }
        $routes = (Yii::createObject(RouteModel::class))->getAvailableAndAssignedRoutes();
        $this->allRoutes = array_merge($routes['available'], $routes['assigned']);
        return $this->allRoutes;
    }

    public function getAuth(): \yii\rbac\ManagerInterface
    {
        if ($this->auth !== null) {
            return $this->auth;
        }
        $this->auth = Yii::$app->authManager;
        return $this->auth;
    }
}
