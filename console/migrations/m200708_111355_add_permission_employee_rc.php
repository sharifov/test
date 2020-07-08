<?php

use common\models\Employee;
use yii\db\Migration;

/**
 * Class m200708_111355_add_permission_employee_rc
 */
class m200708_111355_add_permission_employee_rc extends Migration
{
    public $route = [
        '/employee/register-to-rocket-chat',
        '/employee/un-register-to-rocket-chat'
    ];

    public $roles = [
        Employee::ROLE_SUPER_ADMIN,
        Employee::ROLE_ADMIN,
    ];

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        (new \console\migrations\RbacMigrationService())->up($this->route, $this->roles);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        (new \console\migrations\RbacMigrationService())->down($this->route, $this->roles);
    }
}
