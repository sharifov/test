<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m220225_134437_add_rbac_permission_for_lead_status_reason_crud_pages
 */
class m220225_134437_add_rbac_permission_for_lead_status_reason_crud_pages extends Migration
{
    private array $routes = [
        '/lead-status-reason-crud/index',
        '/lead-status-reason-crud/create',
        '/lead-status-reason-crud/view',
        '/lead-status-reason-crud/update',
        '/lead-status-reason-crud/delete',
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
