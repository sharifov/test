<?php

use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m220608_110440_add_permission_for_date_sensitive
 */
class m220608_110440_add_permission_for_date_sensitive extends Migration
{
    public $routes = [
        '/db-db-data-sensitive-crud/index',
        '/db-db-data-sensitive-crud/create',
        '/db-db-data-sensitive-crud/view',
        '/db-db-data-sensitive-crud/update',
        '/db-db-data-sensitive-crud/delete',
        '/db-data-sensitive/drop-view',
        '/db-data-sensitive/drop-views',
        '/db-data-sensitive/create-views',
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
