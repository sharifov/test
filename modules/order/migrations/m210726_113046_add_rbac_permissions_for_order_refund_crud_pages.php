<?php

namespace modules\order\migrations;

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m210726_113046_add_rbac_permissions_for_order_refund_crud_pages
 */
class m210726_113046_add_rbac_permissions_for_order_refund_crud_pages extends Migration
{
    private array $routes = [
        '/order/order-refund-crud/index',
        '/order/order-refund-crud/create',
        '/order/order-refund-crud/update',
        '/order/order-refund-crud/delete',
        '/order/order-refund-crud/view',
    ];

    private array $roles = [
        Employee::ROLE_SUPER_ADMIN,
        Employee::ROLE_ADMIN,
    ];

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        (new RbacMigrationService())->up($this->routes, $this->roles);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        (new RbacMigrationService())->down($this->routes, $this->roles);
    }
}
