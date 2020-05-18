<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m200210_084213_add_permissions_qa_task_module
 */
class m200210_084213_add_permissions_qa_task_module extends Migration
{
    public $roles = [
        Employee::ROLE_ADMIN,
        Employee::ROLE_SUPER_ADMIN,
    ];

    public $routes = [
        '/qa-task/qa-task-status-log/show',
        'crud' => [
            '/qa-task/qa-task-category-crud/*',
            '/qa-task/qa-task-crud/*',
            '/qa-task/qa-task-status-crud/*',
            '/qa-task/qa-task-status-log-crud/*',
            '/qa-task/qa-task-status-reason-crud/*',
        ]
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
