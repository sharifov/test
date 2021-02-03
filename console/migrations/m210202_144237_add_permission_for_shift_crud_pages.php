<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m210202_144237_add_permission_for_shift_crud_pages
 */
class m210202_144237_add_permission_for_shift_crud_pages extends Migration
{
    private array $routes = [
        '/shift-crud/index',
        '/shift-crud/create',
        '/shift-crud/update',
        '/shift-crud/delete',
        '/shift-crud/view',

        '/shift-schedule-rule-crud/index',
        '/shift-schedule-rule-crud/create',
        '/shift-schedule-rule-crud/update',
        '/shift-schedule-rule-crud/delete',
        '/shift-schedule-rule-crud/view',

        '/user-shift-assign-crud/index',
        '/user-shift-assign-crud/create',
        '/user-shift-assign-crud/update',
        '/user-shift-assign-crud/delete',
        '/user-shift-assign-crud/view',

        '/user-shift-schedule-crud/index',
        '/user-shift-schedule-crud/create',
        '/user-shift-schedule-crud/update',
        '/user-shift-schedule-crud/delete',
        '/user-shift-schedule-crud/view',
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
