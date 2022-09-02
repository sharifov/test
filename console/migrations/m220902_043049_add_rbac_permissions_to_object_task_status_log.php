<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m220902_043049_add_rbac_permissions_to_object_task_status_log
 */
class m220902_043049_add_rbac_permissions_to_object_task_status_log extends Migration
{
    private array $roles = [
        Employee::ROLE_ADMIN,
        Employee::ROLE_SUPER_ADMIN,
    ];

    private array $routes = [
        '/object-task/object-task-status-log/index',
        '/object-task/object-task-status-log/create',
        '/object-task/object-task-status-log/update',
        '/object-task/object-task-status-log/delete',
        '/object-task/object-task-status-log/view',
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
