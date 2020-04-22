<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m200210_142333_add_permissions_qa_task_queue
 */
class m200210_142333_add_permissions_qa_task_queue extends Migration
{
    public $roles = [
        Employee::ROLE_ADMIN,
        Employee::ROLE_SUPER_ADMIN,
    ];

    public $routes = [
        '/qa-task/qa-task-queue/pending',
        '/qa-task/qa-task-queue/processing',
        '/qa-task/qa-task-queue/escalated',
        '/qa-task/qa-task-queue/closed',
        '/qa-task/qa-task-queue/count',
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
