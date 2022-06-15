<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m220614_110505_add_permission_to_user_task_crud
 */
class m220614_110505_add_permission_to_user_task_crud extends Migration
{
    private array $routes = [
        '/task/user-task-crud/create',
        '/task/user-task-crud/update',
        '/task/user-task-crud/view',
        '/task/user-task-crud/delete',
        '/task/user-task-crud/index',
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
