<?php

namespace modules\order\migrations;

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m210331_114105_add_permissions_for_order_data_crud_pages
 */
class m210331_114105_add_permissions_for_order_data_crud_pages extends Migration
{
    private array $routes = [
        '/order/order-data-crud/index',
        '/order/order-data-crud/create',
        '/order/order-data-crud/update',
        '/order/order-data-crud/delete',
        '/order/order-data-crud/view',
    ];

    private array $roles = [
        Employee::ROLE_ADMIN,
        Employee::ROLE_SUPER_ADMIN,
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
