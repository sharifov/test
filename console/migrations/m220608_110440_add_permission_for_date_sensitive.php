<?php

use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m220608_110440_add_permission_for_date_sensitive
 */
class m220608_110440_add_permission_for_date_sensitive extends Migration
{
    public $routes = [
        '/date-sensitive-crud/index',
        '/date-sensitive-crud/create',
        '/date-sensitive-crud/view',
        '/date-sensitive-crud/update',
        '/date-sensitive-crud/delete',
        '/date-sensitive/drop-view',
        '/date-sensitive/drop-views',
        '/date-sensitive/create-views',
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
