<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m200128_161747_add_permissions_invoice_status_log
 */
class m200128_161747_add_permissions_invoice_status_log extends Migration
{
    public $roles = [
        Employee::ROLE_ADMIN,
        Employee::ROLE_SUPER_ADMIN,
    ];

    public $routes = [
        '/invoice/invoice-status-log-crud/index',
        '/invoice/invoice-status-log-crud/view',
        '/invoice/invoice-status-log-crud/create',
        '/invoice/invoice-status-log-crud/update',
        '/invoice/invoice-status-log-crud/delete',

        '/invoice/invoice-status-log/show',
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
