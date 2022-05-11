<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m220228_112005_add_rbac_permission_for_lead_status_reason_log
 */
class m220228_112005_add_rbac_permission_for_lead_status_reason_log extends Migration
{
    private array $routes = [
        '/lead-status-reason-log-crud/index',
        '/lead-status-reason-log-crud/view',
        '/lead-status-reason-log-crud/delete',
        '/lead-status-reason-log-crud/create',
        '/lead-status-reason-log-crud/update',
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
