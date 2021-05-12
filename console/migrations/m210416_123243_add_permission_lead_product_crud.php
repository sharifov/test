<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m210416_123243_add_permission_lead_product_crud
 */
class m210416_123243_add_permission_lead_product_crud extends Migration
{
    private array $routes = [
        '/lead-product-crud/index',
        '/lead-product-crud/view',
        '/lead-product-crud/create',
        '/lead-product-crud/update',
        '/lead-product-crud/delete',
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
