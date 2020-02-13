<?php

namespace modules\product\migrations;

use Yii;
use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m200125_123846_create_permissions_product_module
 */
class m200125_123846_create_permissions_product_module extends Migration
{
    public $roles = [
        Employee::ROLE_ADMIN,
        Employee::ROLE_SUPER_ADMIN,
    ];

    public $routes = [
        '/product/product/create-ajax', '/product/product/delete-ajax',

        '/product/product-type-crud/create', '/product/product-type-crud/delete', '/product/product-type-crud/index', '/product/product-type-crud/update', '/product/product-type-crud/view',

        '/product/product-quote-crud/create', '/product/product-quote-crud/delete', '/product/product-quote-crud/index', '/product/product-quote-crud/update', '/product/product-quote-crud/view',
        '/product/product-quote/delete-ajax',

        '/product/product-option-crud/create', '/product/product-option-crud/delete', '/product/product-option-crud/index', '/product/product-option-crud/update', '/product/product-option-crud/view',

        '/product/product-quote-option-crud/create', '/product/product-quote-option-crud/delete', '/product/product-quote-option-crud/index', '/product/product-quote-option-crud/update', '/product/product-quote-option-crud/view',
        '/product/product-quote-option/create-ajax', '/product/product-quote-option/delete-ajax', '/product/product-quote-option/update-ajax',
    ];

    public $oldPermissions = [
        '/product/product-manage/create-ajax', '/product/product-manage/delete-ajax',

        '/product-type/create', '/product-type/delete', '/product-type/index', '/product-type/update', '/product-type/view',

        '/product-quote/create', '/product-quote/delete', '/product-quote/delete-ajax', '/product-quote/index', '/product-quote/update', '/product-quote/view',

        '/product-option/create', '/product-option/delete', '/product-option/index', '/product-option/update', '/product-option/view',

        '/product-quote-option/create', '/product-quote-option/create-ajax', '/product-quote-option/delete', '/product-quote-option/delete-ajax', '/product-quote-option/index', '/product-quote-option/update', '/product-quote-option/update-ajax', '/product-quote-option/view',
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
