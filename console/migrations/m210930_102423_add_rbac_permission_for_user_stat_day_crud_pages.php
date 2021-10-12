<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m210930_102423_add_rbac_permission_for_user_stat_day_crud_pages
 */
class m210930_102423_add_rbac_permission_for_user_stat_day_crud_pages extends Migration
{
    private array $routes = [
        '/user-stat-day-crud/index',
        '/user-stat-day-crud/create',
        '/user-stat-day-crud/update',
        '/user-stat-day-crud/delete',
        '/user-stat-day-crud/view',
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
