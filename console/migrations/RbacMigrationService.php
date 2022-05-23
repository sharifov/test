<?php

namespace console\migrations;

use Yii;
use yii\helpers\VarDumper;
use yii\rbac\Permission;
use yii\rbac\Role;
use yii2mod\rbac\models\RouteModel;

/**
 * Class RbacMigrationService
 *
 * @property $allRoutes
 * @property $auth
 */
class RbacMigrationService
{
    private $allRoutes;
    private $auth;

    /**
     * @param array $routes
     * @param array $roles
     * @throws \yii\base\Exception
     */
    public function up(array $routes, array $roles): void
    {
        $auth = $this->getAuth();

        $routes = $this->createRoutes($routes);

        foreach ($routes as $route) {
            if (!$permission = $auth->getPermission($route)) {
                $permission = $auth->createPermission($route);
                $auth->add($permission);
            }

            foreach ($roles as $item) {
                if ($role = $auth->getRole($item)) {
                    if (!$auth->hasChild($role, $permission)) {
                        $auth->addChild($role, $permission);
                    }
                }
            }
        }
    }

    /**
     * @param array $routes
     * @param array $roles
     */
    public function down(array $routes, array $roles): void
    {
        $auth = $this->getAuth();

        $routes = $this->createRoutes($routes);

        foreach ($routes as $route) {
            foreach ($roles as $item) {
                if ($role = $auth->getRole($item)) {
                    if ($permission = $auth->getPermission($route)) {
                        if ($auth->hasChild($role, $permission)) {
                            $auth->removeChild($role, $permission);
                        }
                    }
                }
            }
        }
    }

    private function createRoutes(array $routes): array
    {
        $out = [];
        foreach ($routes as $key => $item) {
            if ($key === 'crud') {
                foreach ($item as $crudRoute) {
                    foreach ($this->createCrudRoutesFromGeneralRoute($crudRoute) as $crudItem) {
                        $out[] = $crudItem;
                    }
                }
            } else {
                $out[] = $item;
            }
        }
        return $out;
    }

    /**
     * /path/* --> [/path/index, /path/view, /path/create...]
     */
    private function createCrudRoutesFromGeneralRoute(string $route): array
    {
        if (substr($route, -2) !== '/*') {
            throw new \InvalidArgumentException('2 last symbols of route must be: /* ');
        }
        $generalRoute = substr($route, 0, (strlen($route) - 2));
        return [
            $generalRoute . '/index',
            $generalRoute . '/view',
            $generalRoute . '/create',
            $generalRoute . '/update',
            $generalRoute . '/delete',
        ];
    }

    /**
     * from /controller/* to /controller/index  /controller/view ...
     */
    public function changeGroupRouteToRoutes(string $roleName): array
    {
        $report = [];
        $auth = $this->getAuth();

        if ($role = $auth->getRole($roleName)) {
            foreach ($this->getAllGroupByRole($role) as $group) {
                if ($groupRole = $auth->getPermission($group)) {
                    if ($auth->removeChild($role, $groupRole)) {
                        $report[] = 'removed: ' . $group;
                    } else {
                        $report[] = 'not removed: ' . $group;
                    }
                }

                $routes = $this->getAllRoutesByGroup($group);
//                $report[] = 'found routes: ' . VarDumper::dumpAsString($routes);
                foreach ($routes as $route) {
                    $permission = $this->getOrCreatePermission($route);
                    if (!$auth->hasChild($role, $permission)) {
                        if ($auth->canAddChild($role, $permission)) {
                            if ($auth->addChild($role, $permission)) {
                                $report[] = 'added: ' . $route;
                            } else {
                                $report[] = 'not added: ' . $route;
                            }
                        } else {
                            $report[] = 'cant add: ' . $route;
                        }
                    } else {
                        $report[] =  'already assigned: ' . $permission->name;
                    }
                }
            }
        } else {
            $report[] = 'not found role: ' . $roleName;
        }

        return $report;
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
        $group = substr($group, 0, (strlen($group) - 1));
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
        if ($this->allRoutes !== null) {
            return $this->allRoutes;
        }
        $routeModel = (Yii::createObject(RouteModel::class));
        $appRoutes = $routeModel->getAppRoutes();
        $availableAndAssignedRoutes = $routeModel->getAvailableAndAssignedRoutes();
        $this->allRoutes = array_merge(
            $availableAndAssignedRoutes['available'],
            $availableAndAssignedRoutes['assigned'],
            array_keys($appRoutes)
        );
        $this->allRoutes = array_unique($this->allRoutes);
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

    /**
     * @param Permission|string $searchPermission
     * @return Role[]
     */
    public function getAllRolesCanPermission($searchPermission): array
    {
        if (is_string($searchPermission)) {
            if (!$permission = $this->getAuth()->getPermission($searchPermission)) {
                throw new \RuntimeException('Not found old permission: ' . $searchPermission);
            }
        } elseif (!$searchPermission instanceof Permission) {
            throw new \InvalidArgumentException('Permission must be string or Permission type');
        } else {
            $permission = $searchPermission;
        }

        $roles = [];
        foreach ($this->getAuth()->getRoles() as $role) {
            foreach ($this->getAuth()->getPermissionsByRole($role->name) as $authPermission) {
                if ($permission->name === $authPermission->name) {
                    $roles[] = $role;
                    break;
                }
            }
        }
        return $roles;
    }

    public function addNewPermissionToRolesWhoCanOldPermission(string $newPermission, string $oldPermission): void
    {
        $auth = $this->getAuth();

        if (!$oldAuthPermission = $auth->getPermission($oldPermission)) {
            throw new \RuntimeException('Not found old permission: ' . $oldPermission);
        }

        if (!$newAuthPermission = $auth->getPermission($newPermission)) {
            $newAuthPermission = $auth->createPermission($newPermission);
            $auth->add($newAuthPermission);
        }

        foreach ($this->getAllRolesCanPermission($oldAuthPermission) as $role) {
            if (!$auth->hasChild($role, $newAuthPermission) && $auth->canAddChild($role, $newAuthPermission)) {
                $auth->addChild($role, $newAuthPermission);
            }
        }
    }

    public function removePermissionFromRolesWhoCanOtherPermission(string $permission, string $otherPermission): void
    {
        $auth = $this->getAuth();

        if (!$authPermission = $auth->getPermission($permission)) {
            throw new \RuntimeException('Not found permission: ' . $permission);
        }

        if (!$authOtherPermission = $auth->getPermission($otherPermission)) {
            throw new \RuntimeException('Not found other permission: ' . $otherPermission);
        }

        foreach ($this->getAllRolesCanPermission($authOtherPermission) as $role) {
            $auth->removeChild($role, $authPermission);
        }
    }
}
