<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m220520_142102_add_rbac_permissions_for_user_shift_schedule_log_crud_pages
 */
class m220520_142102_add_rbac_permissions_for_user_shift_schedule_log_crud_pages extends Migration
{
    private array $roles = [
        Employee::ROLE_ADMIN,
        Employee::ROLE_SUPER_ADMIN,
    ];

    private array $routes = [
        '/shift/user-shift-schedule-log-crud/index',
        '/shift/user-shift-schedule-log-crud/create',
        '/shift/user-shift-schedule-log-crud/update',
        '/shift/user-shift-schedule-log-crud/delete',
        '/shift/user-shift-schedule-log-crud/view',
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
