<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m210212_071834_add_permissions_module_cruise
 */
class m210212_071834_add_permissions_module_cruise extends Migration
{
    public $routes = [
        '/cruise/cruise/*',
        '/cruise/cruise-cabin/*',
        '/cruise/cruise-cabin-pax/*',
        '/cruise/cruise-quote/*',
    ];

    public $roles = [
        Employee::ROLE_ADMIN,
        Employee::ROLE_SUPER_ADMIN,
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
