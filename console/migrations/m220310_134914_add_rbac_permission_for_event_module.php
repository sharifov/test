<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m220310_134914_add_rbac_permission_for_event_module
 */
class m220310_134914_add_rbac_permission_for_event_module extends Migration
{
    private array $routes = [
        '/event-handler/index',
        '/event-handler/create',
        '/event-handler/update',
        '/event-handler/delete',
        '/event-handler/view',

        '/event-list/index',
        '/event-list/create',
        '/event-list/update',
        '/event-list/delete',
        '/event-list/view',
        '/event-list/clear-cache',
    ];

    private array $roles = [
        Employee::ROLE_SUPER_ADMIN,
        Employee::ROLE_ADMIN,
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
