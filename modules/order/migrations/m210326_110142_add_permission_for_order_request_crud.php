<?php

namespace modules\order\migrations;

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m210326_110142_add_permission_for_order_request_crud
 */
class m210326_110142_add_permission_for_order_request_crud extends Migration
{
    private array $routes = [
        '/order/order-request-crud/index',
        '/order/order-request-crud/create',
        '/order/order-request-crud/update',
        '/order/order-request-crud/delete',
        '/order/order-request-crud/view',
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
