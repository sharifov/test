<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m200219_064722_add_permissions_qa_task_rules
 */
class m200219_064722_add_permissions_qa_task_rules extends Migration
{
    public $roles = [
        Employee::ROLE_ADMIN,
        Employee::ROLE_SUPER_ADMIN,
    ];

    public $routes = [
        '/qa-task/qa-task-rules/index',
        '/qa-task/qa-task-rules/view',
        '/qa-task/qa-task-rules/update',
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
