<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m210406_092750_add_case_order_crud_permissions
 */
class m210406_092750_add_case_order_crud_permissions extends Migration
{
    private array $routes = [
        '/case-order-crud/index',
        '/case-order-crud/view',
        '/case-order-crud/create',
        '/case-order-crud/update',
        '/case-order-crud/delete',
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
