<?php

namespace modules\product\migrations;

use Yii;
use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m200125_070125_add_permissions_product_product_crud_controller
 */
class m200125_070125_add_permissions_product_product_crud_controller extends Migration
{
    public $roles = [
        Employee::ROLE_ADMIN,
        Employee::ROLE_SUPER_ADMIN,
    ];

    public $routes = [
        '/product/product-crud/create',
        '/product/product-crud/delete',
        '/product/product-crud/index',
        '/product/product-crud/update',
        '/product/product-crud/view',
    ];

    public $oldPermissions = [
        '/product/product/create',
        '/product/product/delete',
        '/product/product/index',
        '/product/product/update',
        '/product/product/view',
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
