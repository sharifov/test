<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m220803_061855_add_rbac_migration_to_beq_pages
 */
class m220803_061855_add_rbac_migration_to_beq_pages extends Migration
{
    private array $roles = [
        Employee::ROLE_ADMIN,
        Employee::ROLE_SUPER_ADMIN,
    ];

    private array $routes = [
        '/lead-business-extra-queue-rule-crud/index',
        '/lead-business-extra-queue-rule-crud/create',
        '/lead-business-extra-queue-rule-crud/update',
        '/lead-business-extra-queue-rule-crud/delete',
        '/lead-business-extra-queue-rule-crud/view',

        '/lead-business-extra-queue-crud/index',
        '/lead-business-extra-queue-crud/create',
        '/lead-business-extra-queue-crud/update',
        '/lead-business-extra-queue-crud/delete',
        '/lead-business-extra-queue-crud/view',

        '/lead-business-extra-queue-log-crud/index',
        '/lead-business-extra-queue-log-crud/create',
        '/lead-business-extra-queue-log-crud/update',
        '/lead-business-extra-queue-log-crud/delete',
        '/lead-business-extra-queue-log-crud/view',
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
