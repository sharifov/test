<?php

use yii\db\Migration;
use common\models\Employee;
use console\migrations\RbacMigrationService;

/**
 * Class m220430_145903_add_rbac_permissions_for_quote_communication_open_log_table
 */
class m220430_145903_add_rbac_permissions_for_quote_communication_open_log_table extends Migration
{
    private $routes = [
        '/quote-communication-open-log/index',
        '/quote-communication-open-log/create',
        '/quote-communication-open-log/update',
        '/quote-communication-open-log/delete',
        '/quote-communication-open-log/view',
    ];

    private $roles = [
        Employee::ROLE_SUPER_ADMIN,
        Employee::ROLE_ADMIN,
    ];

    /**
     * @return false|mixed|void
     * @throws \yii\base\Exception
     */
    public function safeUp()
    {
        (new RbacMigrationService())->up($this->routes, $this->roles);
    }

    /**
     * @return false|mixed|void
     */
    public function safeDown()
    {
        (new RbacMigrationService())->down($this->routes, $this->roles);
    }
}
