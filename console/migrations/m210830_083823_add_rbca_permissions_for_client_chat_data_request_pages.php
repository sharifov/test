<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m210830_083823_add_rbca_permissions_for_client_chat_data_request_pages
 */
class m210830_083823_add_rbca_permissions_for_client_chat_data_request_pages extends Migration
{
    private $routes = [
        '/client-chat-data-request-crud/index',
        '/client-chat-data-request-crud/create',
        '/client-chat-data-request-crud/update',
        '/client-chat-data-request-crud/delete',
        '/client-chat-data-request-crud/view',
    ];

    private $roles = [
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
