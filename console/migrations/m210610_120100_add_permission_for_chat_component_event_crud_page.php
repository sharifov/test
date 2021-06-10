<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m210610_120100_add_permission_for_chat_component_event_crud_page
 */
class m210610_120100_add_permission_for_chat_component_event_crud_page extends Migration
{
    private array $routes = [
        '/client-chat-component-event-crud/index',
        '/client-chat-component-event-crud/create',
        '/client-chat-component-event-crud/update',
        '/client-chat-component-event-crud/delete',
        '/client-chat-component-event-crud/view',
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
