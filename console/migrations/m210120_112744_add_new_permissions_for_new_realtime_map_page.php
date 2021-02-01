<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m210120_112744_add_new_permissions_for_new_realtime_map_page
 */
class m210120_112744_add_new_permissions_for_new_realtime_map_page extends Migration
{
    private array $routes = [
        '/monitor/call-incoming',
        '/monitor/static-data-api',
        '/monitor/list-api'
    ];

    private array $roles = [
        Employee::ROLE_SUPER_ADMIN,
        Employee::ROLE_ADMIN,
        Employee::ROLE_SUPERVISION,
        Employee::ROLE_EX_SUPER,
        Employee::ROLE_QA_SUPER,
        Employee::ROLE_SUP_SUPER,
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
