<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m220811_105724_add_rbac_for_user_task_status_log_crud
 */
class m220811_105724_add_rbac_for_user_task_status_log_crud extends Migration
{
    private array $routes = [
        '/task/user-task-status-log-crud/index',
        '/task/user-task-status-log-crud/view',
        '/task/user-task-status-log-crud/create',
        '/task/user-task-status-log-crud/update',
        '/task/user-task-status-log-crud/delete',
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
