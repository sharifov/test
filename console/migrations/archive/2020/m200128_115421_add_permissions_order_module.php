<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m200128_115421_add_permissions_order_module
 */
class m200128_115421_add_permissions_order_module extends Migration
{
    public $roles = [
        Employee::ROLE_ADMIN,
        Employee::ROLE_SUPER_ADMIN,
    ];

    public $routes = [
        '/order/order-crud/create', '/order/order-crud/index', '/order/order-crud/update', '/order/order-crud/delete', '/order/order-crud/view',
        '/order/order/delete-ajax',  '/order/order/list-menu-ajax',  '/order/order/update-ajax', '/order/order/create-ajax',

        '/order/order-product-crud/create', '/order/order-product-crud/delete', '/order/order-product-crud/index', '/order/order-product-crud/update', '/order/order-product-crud/view',
        '/order/order-product/create-ajax', '/order/order-product/delete-ajax',
    ];

    public $oldPermissions = [
        '/order/create', '/order/create-ajax', '/order/delete', '/order/delete-ajax', '/order/index', '/order/list-menu-ajax', '/order/update', '/order/update-ajax', '/order/view',

        '/order-product/create', '/order-product/create-ajax', '/order-product/delete', '/order-product/delete-ajax', '/order-product/index', '/order-product/update', '/order-product/view',
    ];

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        foreach ($this->oldPermissions as $item) {
            if ($permission = $auth->getPermission($item)) {
                $auth->remove($permission);
            }
        }

        (new RbacMigrationService())->up($this->routes, $this->roles);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        (new RbacMigrationService())->down($this->routes, $this->roles);

        $auth = Yii::$app->authManager;

        foreach ($this->oldPermissions as $item) {
            if ($permission = $auth->createPermission($item)) {
                $auth->add($permission);
                foreach ($this->roles as $role) {
                    $r = $auth->getRole($role);
                    $auth->addChild($r, $permission);
                }
            }
        }
    }
}
