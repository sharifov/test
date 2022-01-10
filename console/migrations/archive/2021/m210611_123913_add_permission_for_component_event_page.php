<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m210611_123913_add_permission_for_component_event_page
 */
class m210611_123913_add_permission_for_component_event_page extends Migration
{
    private array $routes = [
        '/client-chat-component-event/index',
        '/client-chat-component-event/create',
        '/client-chat-component-event/view',
        '/client-chat-component-event/update',
        '/client-chat-component-event/delete',
    ];

    private array $roles = [
        Employee::ROLE_SUPER_ADMIN,
        Employee::ROLE_ADMIN
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
