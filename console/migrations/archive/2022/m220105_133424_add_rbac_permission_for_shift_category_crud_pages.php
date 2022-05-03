<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m220105_133424_add_rbac_permission_for_shift_category_crud_pages
 */
class m220105_133424_add_rbac_permission_for_shift_category_crud_pages extends Migration
{
    private array $routes = [
        '/shift-category-crud/index',
        '/shift-category-crud/create',
        '/shift-category-crud/update',
        '/shift-category-crud/delete',
        '/shift-category-crud/view',
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
