<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m200623_121959_add_permission_employee_validate
 */
class m200623_121959_add_permission_employee_validate extends Migration
{
    public $routes = [
        '/employee/employee-validation',
    ];

    public $roles = [
        Employee::ROLE_USER_MANAGER,
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
