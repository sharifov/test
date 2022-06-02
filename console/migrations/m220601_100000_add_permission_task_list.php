<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m220601_100000_add_permission_task_list
 */
class m220601_100000_add_permission_task_list extends Migration
{
    private array $routes = [
        '/task/task-list/create',
        '/task/task-list/update',
        '/task/task-list/view',
        '/task/task-list/delete',
        '/task/task-list/index',
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
