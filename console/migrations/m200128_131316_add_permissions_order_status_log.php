<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m200128_131316_add_permissions_order_status_log
 */
class m200128_131316_add_permissions_order_status_log extends Migration
{
    public $roles = [
        Employee::ROLE_ADMIN,
        Employee::ROLE_SUPER_ADMIN,
    ];

    public $routes = [
        '/order/order-status-log-crud/index',
        '/order/order-status-log-crud/view',
        '/order/order-status-log-crud/create',
        '/order/order-status-log-crud/update',
        '/order/order-status-log-crud/delete',

        '/order/order-status-log/show',
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
