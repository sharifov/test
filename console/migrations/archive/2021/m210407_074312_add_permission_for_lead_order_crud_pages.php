<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m210407_074312_add_permission_for_lead_order_crud_pages
 */
class m210407_074312_add_permission_for_lead_order_crud_pages extends Migration
{
    private array $routes = [
        '/lead-order-crud/index',
        '/lead-order-crud/view',
        '/lead-order-crud/create',
        '/lead-order-crud/update',
        '/lead-order-crud/delete',
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
