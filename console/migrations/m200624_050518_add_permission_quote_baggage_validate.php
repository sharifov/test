<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m200624_050518_add_permission_quote_baggage_validate
 */
class m200624_050518_add_permission_quote_baggage_validate extends Migration
{
    public $routes = [
        '/quote/segment-baggage-validate',
    ];

    public $roles = [
        Employee::ROLE_ADMIN, Employee::ROLE_SUPER_ADMIN,
        Employee::ROLE_AGENT, Employee::ROLE_SUPERVISION,
        Employee::ROLE_EX_AGENT, Employee::ROLE_EX_SUPER,
        Employee::ROLE_SUP_AGENT, Employee::ROLE_SUP_SUPER,
        Employee::ROLE_QA, Employee::ROLE_USER_MANAGER,
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
