<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m210611_072328_add_permission_for_client_chat_component_rule_crud_pages
 */
class m210611_072328_add_permission_for_client_chat_component_rule_crud_pages extends Migration
{
    private array $routes = [
        '/client-chat-component-rule-crud/index',
        '/client-chat-component-rule-crud/create',
        '/client-chat-component-rule-crud/update',
        '/client-chat-component-rule-crud/delete',
        '/client-chat-component-rule-crud/view',
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
