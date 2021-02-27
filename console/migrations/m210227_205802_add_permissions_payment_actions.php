<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m210227_205802_add_permissions_payment_actions
 */
class m210227_205802_add_permissions_payment_actions extends Migration
{
    private array $roles = [
        Employee::ROLE_ADMIN,
        Employee::ROLE_SUPER_ADMIN,
    ];

    private array $routes = [
        '/order/order-actions/start-process',
        '/order/order-actions/cancel-process',
        '/order/payment-actions/void',
        '/order/payment-actions/capture',
        '/order/payment-actions/refund',
        '/order/payment-actions/update',
        '/order/payment-actions/delete',
        '/order/payment-actions/status-log',
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
