<?php

use common\models\Employee;
use yii\db\Migration;

/**
 * Class m200318_153526_change_permissions_cases_category
 */
class m200318_153526_change_permissions_cases_category extends Migration
{
    private $roles = [
        Employee::ROLE_SUPER_ADMIN,
        Employee::ROLE_ADMIN,
        Employee::ROLE_AGENT,
        Employee::ROLE_SUPERVISION,
        Employee::ROLE_QA,
        Employee::ROLE_QA_SUPER,
        Employee::ROLE_USER_MANAGER,
        Employee::ROLE_SUP_AGENT,
        Employee::ROLE_SUP_SUPER,
        Employee::ROLE_EX_AGENT,
        Employee::ROLE_EX_SUPER,
        Employee::ROLE_SALES_SENIOR,
        Employee::ROLE_EXCHANGE_SENIOR,
        Employee::ROLE_SUPPORT_SENIOR,
    ];

    private $routes = [
        '/cases-category/create' => '/case-category/create',
        '/cases-category/delete' => '/case-category/delete',
        '/cases-category/index' => '/case-category/index',
        '/cases-category/update' => '/case-category/update',
        '/cases-category/view' => '/case-category/view',
    ];

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        foreach ($this->roles as $role) {
            foreach ($auth->getPermissionsByRole($role) as $permission) {
                foreach ($this->routes as $key => $value) {
                    if ($permission->name === $key) {
                        if ($roleItem = $auth->getRole($role)) {
                            if (!$route = $auth->getPermission($value)) {
                                $route = $auth->createPermission($value);
                                $auth->add($route);
                            }
                            if (!$auth->hasChild($roleItem, $route)) {
                                $auth->addChild($roleItem, $route);
                            }
                        }
                    }
                }
            }
        }

        foreach ($this->routes as $key => $value) {
            if ($permission = $auth->getPermission($key)) {
                $auth->remove($permission);
            }
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

        foreach ($this->roles as $role) {
            foreach ($auth->getPermissionsByRole($role) as $permission) {
                foreach ($this->routes as $value => $key) {
                    if ($permission->name === $key) {
                        if ($roleItem = $auth->getRole($role)) {
                            if (!$route = $auth->getPermission($value)) {
                                $route = $auth->createPermission($value);
                                $auth->add($route);
                            }
                            if (!$auth->hasChild($roleItem, $route)) {
                                $auth->addChild($roleItem, $route);
                            }
                        }
                    }
                }
            }
        }

        foreach ($this->routes as $value => $key) {
            if ($permission = $auth->getPermission($key)) {
                $auth->remove($permission);
            }
        }

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
