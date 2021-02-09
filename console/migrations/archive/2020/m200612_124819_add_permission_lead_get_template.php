<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m200612_124819_add_permission_lead_get_template
 */
class m200612_124819_add_permission_lead_get_template extends Migration
{
    public $routes = [
        '/lead/get-template',
    ];

    public $roles = [
        Employee::ROLE_ADMIN,
        Employee::ROLE_SUPER_ADMIN,
        Employee::ROLE_AGENT, Employee::ROLE_SUPERVISION,
        Employee::ROLE_EX_AGENT, Employee::ROLE_EX_SUPER,
        Employee::ROLE_SUP_AGENT, Employee::ROLE_SUP_SUPER,
        Employee::ROLE_QA,
        Employee::ROLE_USER_MANAGER
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
