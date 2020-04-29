<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m200303_102633_add_permission_setting_clean
 */
class m200303_102633_add_permission_setting_clean extends Migration
{
    public $roles = [
        Employee::ROLE_ADMIN,
        Employee::ROLE_SUPER_ADMIN,
    ];

    public $routes = [
        '/setting/clean',
    ];

    public function safeUp()
    {
        (new RbacMigrationService())->up($this->routes, $this->roles);
    }

    public function safeDown()
    {
        (new RbacMigrationService())->down($this->routes, $this->roles);
    }
}
