<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m220809_120522_add_rbac_migration_to_client_chat_form_response_pages
 */
class m220809_120522_add_rbac_migration_to_client_chat_form_response_pages extends Migration
{
    private array $roles = [
        Employee::ROLE_ADMIN,
        Employee::ROLE_SUPER_ADMIN,
    ];

    private array $routes = [
        '/client-chat-form-response-crud/index',
        '/client-chat-form-response-crud/create',
        '/client-chat-form-response-crud/update',
        '/client-chat-form-response-crud/delete',
        '/client-chat-form-response-crud/view',
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
