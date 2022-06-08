<?php

use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m220608_110440_add_permission_for_date_sensitive
 */
class m220608_110440_add_permission_for_date_sensitive extends Migration
{
    public $routes = [
        '/db-db-date-sensitive-crud/index',
        '/db-db-date-sensitive-crud/create',
        '/db-db-date-sensitive-crud/view',
        '/db-db-date-sensitive-crud/update',
        '/db-db-date-sensitive-crud/delete',
        '/db-date-sensitive/drop-view',
        '/db-date-sensitive/drop-views',
        '/db-date-sensitive/create-views',
    ];

    public $roles = [
        \common\models\Employee::ROLE_ADMIN,
        \common\models\Employee::ROLE_SUPER_ADMIN,
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
