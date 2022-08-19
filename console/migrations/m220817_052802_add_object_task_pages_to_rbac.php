<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m220817_052802_add_object_task_pages_to_rbac
 */
class m220817_052802_add_object_task_pages_to_rbac extends Migration
{
    private array $routes = [
        '/object-task/object-task-crud/index',
        '/object-task/object-task-crud/view',
        '/object-task/object-task-crud/update',
        '/object-task/object-task-scenario/index',
        '/object-task/object-task-scenario/view',
        '/object-task/object-task-scenario/update',
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
