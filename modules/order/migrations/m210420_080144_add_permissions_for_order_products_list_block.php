<?php

namespace modules\order\migrations;

use yii\db\Migration;
use common\models\Employee;
use console\migrations\RbacMigrationService;
use Yii;

/**
 * Class m210420_080144_add_permissions_for_order_products_list_block
 */
class m210420_080144_add_permissions_for_order_products_list_block extends Migration
{
    public $routes = [
        '/order/order/view',
    ];

    public $roles = [
        Employee::ROLE_ADMIN,
        Employee::ROLE_SUPER_ADMIN,
    ];

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        $viewProducts = $auth->createPermission('order/view/products');
        $viewProducts->description = 'View order product list';
        $auth->add($viewProducts);

        $viewContacts = $auth->createPermission('order/view/contacts');
        $viewContacts->description = 'View order contacts';
        $auth->add($viewContacts);

        foreach ($this->roles as $item) {
            if ($role = $auth->getRole($item)) {
                $auth->addChild($role, $viewProducts);
                $auth->addChild($role, $viewContacts);
            }
        }

        (new RbacMigrationService())->up($this->routes, $this->roles);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $auth = Yii::$app->authManager;

        $permissions = [
            'order/view/products',
            'order/view/contacts'
        ];

        foreach ($permissions as $permissionName) {
            if ($permission = $auth->getPermission($permissionName)) {
                $auth->remove($permission);
            }
        }

        (new RbacMigrationService())->down($this->routes, $this->roles);
    }
}
