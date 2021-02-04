<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m200619_085508_add_permission_ajax_list
 */
class m200619_085508_add_permission_ajax_list extends Migration
{
    private $routes = [
        '/project/list-ajax',
        '/department/list-ajax',
        '/user-group/list-ajax',
        '/client-chat/index'
    ];

    private $roles = [
        Employee::ROLE_ADMIN,
        Employee::ROLE_SUPER_ADMIN,
        Employee::ROLE_AGENT,
        Employee::ROLE_SUPERVISION,
        Employee::ROLE_QA,
        Employee::ROLE_USER_MANAGER,
        Employee::ROLE_SUP_AGENT,
        Employee::ROLE_SUP_SUPER,
        Employee::ROLE_EX_AGENT,
        Employee::ROLE_EX_SUPER,
        Employee::ROLE_SALES_SENIOR,
        Employee::ROLE_EXCHANGE_SENIOR,
        Employee::ROLE_SUPPORT_SENIOR,
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
