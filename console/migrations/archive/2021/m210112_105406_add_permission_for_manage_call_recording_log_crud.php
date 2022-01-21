<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m210112_105406_add_permission_for_manage_call_recording_log_crud
 */
class m210112_105406_add_permission_for_manage_call_recording_log_crud extends Migration
{
    private array $routes = [
        '/call-recording-log-crud/index',
        '/call-recording-log-crud/view',
        '/call-recording-log-crud/create',
        '/call-recording-log-crud/update',
        '/call-recording-log-crud/delete',
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
