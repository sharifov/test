<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m200211_152813_add_permissions_qa_task_action_reason
 */
class m200211_152813_add_permissions_qa_task_action_reason extends Migration
{
    public $roles = [
        Employee::ROLE_ADMIN,
        Employee::ROLE_SUPER_ADMIN,
    ];

    public $new = [
        'crud' => [
            '/qa-task/qa-task-action-reason-crud/*',
        ]
    ];

    public $old = [
        'crud' => [
            '/qa-task/qa-task-status-reason-crud/*',
        ]
    ];

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        (new RbacMigrationService())->down($this->old, $this->roles);
        (new RbacMigrationService())->up($this->new, $this->roles);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        (new RbacMigrationService())->down($this->new, $this->roles);
        (new RbacMigrationService())->up($this->old, $this->roles);
    }
}
