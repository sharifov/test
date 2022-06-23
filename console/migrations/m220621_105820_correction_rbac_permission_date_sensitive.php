<?php

use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m220621_105820_correction_rbac_permission_date_sensitive
 */
class m220621_105820_correction_rbac_permission_date_sensitive extends Migration
{
    public array $routes = [
        '/db-date-sensitive-crud/index',
        '/db-date-sensitive-crud/create',
        '/db-date-sensitive-crud/view',
        '/db-date-sensitive-crud/update',
        '/db-date-sensitive-crud/delete'
    ];

    public array $new_routes = [
        '/db-data-sensitive-crud/index',
        '/db-data-sensitive-crud/create',
        '/db-data-sensitive-crud/view',
        '/db-data-sensitive-crud/update',
        '/db-data-sensitive-crud/delete'
    ];

    public array $roles = [
        \common\models\Employee::ROLE_ADMIN,
        \common\models\Employee::ROLE_SUPER_ADMIN,
    ];

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        (new RbacMigrationService())->down($this->routes, $this->roles);
        (new RbacMigrationService())->up($this->new_routes, $this->roles);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        (new RbacMigrationService())->up($this->routes, $this->roles);
        (new RbacMigrationService())->down($this->new_routes, $this->roles);
    }
}
